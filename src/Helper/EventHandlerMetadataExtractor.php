<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Helper;

use Composer\Factory;
use Composer\IO\BufferIO;
use SetBased\Abc\Console\Style\AbcStyle;
use SetBased\Abc\Event\Exception\MetadataExtractorException;
use SetBased\Exception\FallenException;

/**
 * Command for generation the code for the core's exception handler.
 */
class EventHandlerMetadataExtractor
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The path of the abc.xml config file.
   *
   * @var string
   */
  private $configFilename;

  /**
   * The number of errors occurred.
   *
   * @var int
   */
  private $errorCount = 0;

  /**
   * The output decorator.
   *
   * @var AbcStyle
   */
  private $io;

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * EventHandlerMetadataExtractor constructor.
   *
   * @param AbcStyle $io             The output decorator.
   * @param string   $configFilename The path of the abc.xml config file.
   */
  public function __construct(AbcStyle $io, string $configFilename)
  {
    $this->io             = $io;
    $this->configFilename = $configFilename;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Compares two event handlers for sorting.
   *
   * @param array $handler1 The first event handler.
   * @param array $handler2 The second event handler.
   *
   * @return int
   */
  public static function compare(array $handler1, array $handler2): int
  {
    if ($handler1['depth']==$handler2['depth'])
    {
      return strcmp(sprintf('%s::%s', $handler1['class'], $handler1['method']),
                    sprintf('%s::%s', $handler2['class'], $handler2['method']));
    }

    return ($handler1['depth']<$handler2['depth']) ? -1 : 1;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Compares two handlers.
   *
   * @param array $agent1 The first agent.
   * @param array $agent2 The second agent.
   *
   * @return int
   *
   * @throws \ReflectionException
   */
  public static function compareAgents(array $agent1, array $agent2): int
  {
    $reflection1 = new \ReflectionClass($agent1['type']);
    $reflection2 = new \ReflectionClass($agent2['type']);

    if ($reflection1->isSubclassOf($agent2['type'])) return -1;
    if ($reflection2->isSubclassOf($agent1['type'])) return 1;

    if ($agent1['class']==$agent2['class']) return 0;

    return ($agent1['class']<$agent2['class']) ? -1 : 1;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a Graph with all event handlers.
   *
   * @param array $handlers The event handlers.
   *
   * @return Graph
   */
  private static function createGraph(array $handlers): Graph
  {
    $graph = new Graph();
    foreach ($handlers as $handler)
    {
      foreach ($handler['before'] as $child)
      {
        $graph->addEdge(sprintf('%s::%s', $handler['class'], $handler['method']), $child);
      }
      foreach ($handler['after'] as $parent)
      {
        $graph->addEdge($parent, sprintf('%s::%s', $handler['class'], $handler['method']));
      }
    }

    return $graph;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the class name and method name from an event handler.
   *
   * @param string $handler The event handler. Must be in class::method format.
   *
   * @return array
   */
  private static function extractClassMethod(string $handler): array
  {
    $parts = preg_split('/::/', $handler);

    if (count($parts)!=2)
    {
      throw new MetadataExtractorException("Event handler '%s' does not conform to excepted format: class::method.",
                                           $handler);
    }

    return [$parts[0], $parts[1]];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if an event handler exists, otherwise false.
   *
   * @param array[] $handlers All handlers of an event.
   * @param string  $search   The searched event handler.
   *
   * @return bool
   */
  private static function handlerExist(array $handlers, string $search): bool
  {
    foreach ($handlers as $handler)
    {
      if (sprintf('%s::%s', $handler['class'], $handler['method'])==$search)
      {
        return true;
      }
    }

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts event handlers from abc.xml and reflection of classes.
   *
   * @param string $type The type of event handler: 'modify' or 'notify'.
   *
   * @return array
   */
  public function extractEventHandlers(string $type): array
  {
    $ret      = [];
    $handlers = $this->readEventHandlers($type);

    foreach ($handlers as $handler)
    {
      $this->io->writeln(sprintf('Processing %s', $handler));

      try
      {
        [$class, $method] = self::extractClassMethod($handler);

        $reflectionClass = new \ReflectionClass($class);
        $metadata        = $this->handlerMetaData($reflectionClass, $method);

        if (empty($ret[$metadata['event']])) $ret[$metadata['event']] = [];
        $ret[$metadata['event']][] = $metadata;
      }
      catch (MetadataExtractorException $e)
      {
        $this->io->error($e->getMessage());

        $this->errorCount++;
      }
      catch (\ReflectionException $e)
      {
        $this->io->error($e->getMessage());

        $this->errorCount++;
      }
    }
    $this->noErrors();

    $this->validateHandlers($ret);
    $this->noErrors();

    $this->sortByDepth($ret);

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Cleans a (before or after) dependency and validates the handler exists.
   *
   * @param string $handler
   *
   * @return string
   */
  private function cleanDependency(string $handler): string
  {
    try
    {
      [$class, $method] = self::extractClassMethod($handler);

      $reflectionClass = new \ReflectionClass($class);
      if (!$reflectionClass->hasMethod($method))
      {
        throw new MetadataExtractorException("Class '%s' does not have method '%s'.", $class, $method);
      }

      $reflectionMethod = $reflectionClass->getMethod($method);

      return sprintf('%s::%s', $reflectionClass->getName(), $reflectionMethod->getName());
    }
    catch (\ReflectionException $exception)
    {
      throw new MetadataExtractorException([$exception], "Caught reflection exception '%s.", $exception->getMessage());
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract the company for which the event handler is limited to if any.
   *
   * @param \ReflectionMethod $reflectionMethod The reflection of the method.
   *
   * @return string|null
   */
  private function extractCompany(\ReflectionMethod $reflectionMethod): ?string
  {
    $comment = $reflectionMethod->getDocComment();

    $pattern = "/@onlyForCompany\s*(.+)/";
    preg_match_all($pattern, $comment, $matches, PREG_SET_ORDER);

    switch (true)
    {
      case empty($matches):
        $onlyForCompany = null;
        break;

      case sizeof($matches)==1:
        $onlyForCompany = $matches[0][1];
        break;

      default:
        throw new MetadataExtractorException('Found multiple @onlyForCompany annotations. Expected none or only one @onlyForCompany annotation.');
    }

    return $onlyForCompany;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract the dependencies of the event handler.
   *
   * @param \ReflectionMethod $reflectionMethod The reflection of the method.
   *
   * @return array
   */
  private function extractDependencies(\ReflectionMethod $reflectionMethod): array
  {
    $comment = $reflectionMethod->getDocComment();

    $pattern = "/@(before|after)\s*(.+)/";
    preg_match_all($pattern, $comment, $matches, PREG_SET_ORDER);

    $before = [];
    $after  = [];
    foreach ($matches as $match)
    {
      $order   = $match[1];
      $handler = $match[2];
      try
      {
        $this->io->writeln(sprintf('  Found dependency (%s): %s', $order, $handler));

        $handler = $this->cleanDependency($handler);

        switch ($order)
        {
          case 'before':
            $before[] = $handler;
            break;

          case 'after':
            $after[] = $handler;
            break;

          default:
            throw new FallenException('order', $order);
        }
      }
      catch (MetadataExtractorException $e)
      {
        $this->io->error($e->getMessage());

        $this->errorCount++;
      }
    }

    return [$before, $after];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of a method that is an event handler.
   *
   * @param \ReflectionClass $reflectionClass The reflection class.
   * @param string           $method          The name of the method.
   *
   * @return array
   *
   * @throws \ReflectionException
   */
  private function handlerMetaData(\ReflectionClass $reflectionClass, string $method): array
  {
    $reflectionMethod = $reflectionClass->getMethod($method);

    $this->validateHandler($reflectionClass, $reflectionMethod);

    $arguments = $reflectionMethod->getParameters();
    $argument  = $arguments[0];

    $event = $argument->getClass()->getName();
    if ($event===null)
    {
      throw new MetadataExtractorException("Argument of event handler '%s' must be a class.",
                                           $reflectionClass->getName().'::'.$method);
    }

    [$before, $after] = $this->extractDependencies($reflectionMethod);
    $onlyForCompany = $this->extractCompany($reflectionMethod);

    return ['event'           => $event,
            'class'           => $reflectionClass->getName(),
            'method'          => $method,
            'before'          => $before,
            'after'           => $after,
            'only_or_company' => $onlyForCompany,
            'depth'           => 0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception when one or more errors are encountered.
   */
  private function noErrors(): void
  {
    if ($this->errorCount>0)
    {
      throw new MetadataExtractorException('One or more errors encountered.');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the event handlers found in the abc.xml files.
   *
   * @param string $type The type of event handler: 'modify' or 'notify'.
   *
   * @return string[]
   */
  private function readEventHandlers(string $type): array
  {
    $composer   = Factory::create(new BufferIO());
    $abcXmlList = AbcXmlHelper::getAbcXmlOfInstalledPackages($composer);

    if (is_file('abc.xml'))
    {
      $abcXmlList[] = 'abc.xml';
    }

    $handlers = [];
    foreach ($abcXmlList as $abcXmlPath)
    {
      $helper   = new AbcXmlHelper($abcXmlPath);
      $handlers = array_merge($handlers, $helper->extractEventHandlers($type));
    }

    $handlers = array_unique($handlers);

    sort($handlers);

    return $handlers;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sort handlers by depth and name.
   *
   * @param array $events The event handlers.
   */
  private function sortByDepth(array &$events)
  {
    foreach ($events as $event => &$handlers)
    {
      $graph  = self::createGraph($handlers);
      $depths = $graph->depth();
      foreach ($handlers as &$handler)
      {
        $handler['depth'] = $depths[sprintf('%s::%s', $handler['class'], $handler['method'])] ?? 0;
      }

      usort($handlers, [self::class, 'compare']);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates the event handler.
   *
   * @param \ReflectionClass  $reflectionClass  The reflection of the class.
   * @param \ReflectionMethod $reflectionMethod The reflection of the method.
   */
  private function validateHandler(\ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod): void
  {
    if (!$reflectionMethod->isPublic())
    {
      throw new MetadataExtractorException("Event handler '%s' must be public.",
                                           $reflectionClass->getName().'::'.$reflectionMethod->getName());
    }

    if ($reflectionMethod->isAbstract())
    {
      throw new MetadataExtractorException("Event handler '%s' must not be abstract.",
                                           $reflectionClass->getName().'::'.$reflectionMethod->getName());
    }

    if (!$reflectionMethod->isStatic())
    {
      throw new MetadataExtractorException("Event handler '%s' must be static.",
                                           $reflectionClass->getName().'::'.$reflectionMethod->getName());
    }

    if ($reflectionMethod->getReturnType()!==null && $reflectionMethod->getReturnType()->getName()!=='void')
    {
      throw new MetadataExtractorException("Event handler '%s' must return void.",
                                           $reflectionClass->getName().'::'.$reflectionMethod->getName());
    }

    if ($reflectionMethod->getNumberOfParameters()!==1)
    {
      throw new MetadataExtractorException("Event handler '%s' must have exactly one parameter.",
                                           $reflectionClass->getName().'::'.$reflectionMethod->getName());
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates event handlers.
   *
   * @param array $events All event handlers.
   */
  private function validateHandlers(array $events): void
  {
    foreach ($events as $event => $handlers)
    {
      try
      {
        $this->validateHandlersPass1($handlers);
        $this->validateHandlersPass2($handlers, $event);
      }
      catch (MetadataExtractorException $e)
      {
        $this->io->error($e->getMessage());

        $this->errorCount++;
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates event handlers pass 1: dependencies exists.
   *
   * @param array[] $handlers All handlers of an event.
   */
  private function validateHandlersPass1(array $handlers): void
  {
    foreach ($handlers as $handler)
    {
      foreach ($handler['before'] as $before)
      {
        if (!self::handlerExist($handlers, $before))
        {
          throw new MetadataExtractorException('Dependency (before) %s of %s not found.', $before, $handler['event']);
        }
      }

      foreach ($handler['after'] as $after)
      {
        if (!self::handlerExist($handlers, $after))
        {
          throw new MetadataExtractorException('Dependency (after) %s of %s not found.', $after, $handler['event']);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates event handlers pass 2: cyclic dependencies.
   *
   * @param array[] $handlers All handlers of an event.
   * @param string  $event    The event.
   */
  private function validateHandlersPass2(array $handlers, string $event): void
  {
    $graph = self::createGraph($handlers);

    if ($graph->isCyclic($cycle))
    {
      $lines = array_merge(['Found cycle:'], array_map(function ($element) {
        return sprintf('  * %s', $element);
      }, $cycle));

      $this->io->error($lines);

      throw new MetadataExtractorException('Event handlers for %s are cyclic.', $event);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
