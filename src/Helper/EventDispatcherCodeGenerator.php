<?php
declare(strict_types=1);

namespace Plaisio\Event\Helper;

use SetBased\Helper\CodeStore\Importing;
use SetBased\Helper\CodeStore\PhpCodeStore;

/**
 * Class for generating the PHP code of event dispatcher.
 */
class EventDispatcherCodeGenerator
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The aprent class of the generated event dispatcher.
   *
   * @var string
   */
  private static $parentClass = '\\Plaisio\\Event\\CoreEventDispatcher';

  /**
   * The helper object for importing classes.
   *
   * @var Importing
   */
  private $importing;

  /**
   * The PHP code store.
   *
   * @var PhpCodeStore
   */
  private $store;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    $this->store = new PhpCodeStore();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * If string represents a class constant returns an aray with the class and constnat name. Otherwise returns null.
   *
   * @param string|null $string The string.
   * @param string      $class  The name of the class for resoling self and static.
   *
   * @return array|null
   */
  private static function splitClassConstant(?string $string, string $class): ?array
  {
    if ($string===null) return null;

    $n = preg_match('/^(?P<class>.*)::(?P<constant>[A-Z0-9_]+)$/', $string, $parts);
    if ($n==1)
    {
      $class = (in_array($parts['class'], ['self', 'static'])) ? $class : $parts['class'];

      return ['class'    => $class,
              'constant' => $parts['constant']];
    }

    return null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP code of the event dispatcher.
   *
   * @param string  $fullyQualifiedName The fully qualified class name.
   * @param array[] $modifyHandlers     The metadata of the modify event handlers.
   * @param array[] $notifyHandlers     The metadata of the notify event handlers.
   *
   * @return string
   */
  public function generateCode(string $fullyQualifiedName, array $modifyHandlers, array $notifyHandlers): string
  {
    $parts     = explode('\\', $fullyQualifiedName);
    $class     = array_pop($parts);
    $namespace = ltrim(implode('\\', $parts), '\\');

    $this->importing = new Importing($namespace);
    $this->importing->addClass(self::$parentClass);
    $this->importClasses($modifyHandlers);
    $this->importClasses($notifyHandlers);

    $this->importing->prepare();

    $this->generateHeader($namespace);
    $this->generateClass($class, $modifyHandlers, $notifyHandlers);
    $this->generateTrailer();

    return $this->store->getCode();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP code for all event handlers.
   *
   * @param array[] $allHandlers The metadata of the event handlers.
   * @param string  $property    The property holding the event handlers: $modifyHandlers or $notifyHandlers.
   */
  private function generateAllHandlers(array $allHandlers, string $property)
  {
    // Do not generate code if there are no handlers.
    if (empty($allHandlers)) return;

    $this->store->append('/**');
    $this->store->append(' * @inheritdoc', false);
    $this->store->append(' */', false);
    $this->store->append(sprintf('protected static %s =', $property));
    $this->store->append('  [', false);
    $first = true;
    foreach ($allHandlers as $event => $handlers)
    {
      if (!$first)
      {
        $this->store->appendToLastLine(',');
        $this->store->append('');
      }

      $this->store->append(sprintf("    %s::class => [", $this->importing->simplyFullyQualifiedName($event)), false);
      $this->generateHandlers($handlers, mb_strlen($this->store->getLastLine()));

      $first = false;
    }
    $this->store->append('  ];', false);
    $this->store->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP code of the class definition.
   *
   * @param string  $class          The class name.
   * @param array[] $modifyHandlers The metadata of the modify event handlers.
   * @param array[] $notifyHandlers The metadata of the notify event handlers.
   */
  private function generateClass(string $class, array $modifyHandlers, array $notifyHandlers): void
  {
    $this->store->append('/**');
    $this->store->append(' * Concrete implementation of the event dispatcher.', false);
    $this->store->append(' */', false);
    $this->store->append(sprintf('class %s extends %s',
                                 $class,
                                 $this->importing->simplyFullyQualifiedName(self::$parentClass)));
    $this->store->append('{');
    $this->store->appendSeparator();
    $this->generateAllHandlers($modifyHandlers, '$modifyHandlers');
    $this->generateAllHandlers($notifyHandlers, '$notifyHandlers');
    $this->store->appendSeparator();
    $this->store->append('}');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP code for all event handlers for an event.
   *
   * @param array $handlers
   * @param int   $indent The indent.
   */
  private function generateHandlers(array $handlers, int $indent): void
  {
    $first = true;
    foreach ($handlers as $handler)
    {
      $line = sprintf("[[%s::class, '%s'], %s]",
                      $this->importing->simplyFullyQualifiedName($handler['class']),
                      $handler['method'],
                      $this->resolveOnlyForCompany($handler));
      if ($first)
      {
        $this->store->appendToLastLine($line);
      }
      else
      {
        $this->store->appendToLastLine(',');
        $this->store->append(str_repeat(' ', $indent).$line, false);
      }

      $first = false;
    }
    $this->store->appendToLastLine(']');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates all PHP code just before the class definition.
   *
   * @param string $namespace The namespace.
   */
  private function generateHeader(string $namespace): void
  {
    $this->store->append('<?php');
    $this->store->append('declare(strict_types=1);');
    $this->store->append('');
    $this->store->append(sprintf('namespace %s;', $namespace));
    $this->store->append('');
    $this->store->append($this->importing->imports());
    $this->store->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates all PHP code just after the class definition.
   */
  private function generateTrailer(): void
  {
    $this->store->append('');
    $this->store->appendSeparator();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds classes of event handlers to the importing helper object.
   *
   * @param array $allHandlers The metadata of the event handlers.
   */
  private function importClasses(array $allHandlers): void
  {
    foreach ($allHandlers as $event => $handlers)
    {
      if (!empty($handlers))
      {
        $this->importing->addClass($event);
        foreach ($handlers as $handler)
        {
          $this->importing->addClass($handler['class']);
          $parts = self::splitClassConstant($handler['only_for_company'], $handler['class']);
          if (is_array($parts))
          {
            $this->importing->addClass($parts['class']);
          }
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the PHP code for the company for with an event handler is limited.
   *
   * @param array $handler The event handler details.
   *
   * @return string
   */
  private function resolveOnlyForCompany(array $handler): string
  {
    $parts = self::splitClassConstant($handler['only_for_company'], $handler['class']);
    if (is_array($parts))
    {
      $onlyForCompany = sprintf('%s::%s',
                                $this->importing->simplyFullyQualifiedName($parts['class']),
                                $parts['constant']);
    }
    else
    {
      $onlyForCompany = $handler['only_for_company'] ?? 'null';
    }

    return $onlyForCompany;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
