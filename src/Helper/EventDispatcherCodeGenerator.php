<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Helper;

use SetBased\Helper\CodeStore\PhpCodeStore;

/**
 * Class for generating the PHP code of event dispatcher.
 */
class EventDispatcherCodeGenerator
{
  //--------------------------------------------------------------------------------------------------------------------
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
   * Generates the PHP code of the event dispatcher.
   *
   * @param string  $class          The fully qualified class name.
   * @param array[] $modifyHandlers The metadata of the modify event handlers.
   * @param array[] $notifyHandlers The metadata of the notify event handlers.
   *
   * @return string
   */
  public function generateCode(string $class, array $modifyHandlers, array $notifyHandlers): string
  {
    $parts     = explode('\\', $class);
    $class     = array_pop($parts);
    $namespace = implode('\\', $parts);

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

      $this->store->append(sprintf("    '%s' => [", $event), false);
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
    $this->store->append(sprintf('class %s extends CoreEventDispatcher', $class));
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
      $line = sprintf("['%s::%s', %s]", $handler['class'], $handler['method'], $handler['only_or_company']);
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
    $this->store->append('use SetBased\Abc\Event\CoreEventDispatcher;');
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
}

//----------------------------------------------------------------------------------------------------------------------
