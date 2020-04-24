<?php
declare(strict_types=1);

namespace Plaisio\Event\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Event\Command\GenerateEventDispatcherCommand;
use Plaisio\Event\Test\CoreEventDispatcherTest\Event1;
use Plaisio\Event\Test\CoreEventDispatcherTest\EventDispatcher;
use Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test cass for CoreEventDispatcher.
 */
class CoreEventDispatcherTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the event handler.
   */
  public function test1(): void
  {
    putenv(sprintf('%s=%s', 'PLAISIO_CONFIG', __DIR__.'/CoreEventDispatcherTest/plaisio.xml'));

    $application = new Application();
    $application->add(new GenerateEventDispatcherCommand());

    /** @var GenerateEventDispatcherCommand $command */
    $command       = $application->find('plaisio:generate-core-event-dispatcher');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    $ret = $commandTester->getStatusCode();
    self::assertEquals(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests EventDispatcher::notify().
   */
  public function test2(): void
  {
    $dispatcher               = new EventDispatcher();
    EventHandler::$log        = [];
    EventHandler::$dispatcher = $dispatcher;
    $dispatcher->notify(new Event1());
    $dispatcher->dispatch();

    self::assertEquals([EventHandler::class.'::handle1',
                        EventHandler::class.'::handle2',
                        EventHandler::class.'::handle3',
                        EventHandler::class.'::handle4'], EventHandler::$log);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests EventDispatcher::notify().
   */
  public function test3(): void
  {
    $dispatcher               = new EventDispatcher();
    EventHandler::$log        = [];
    EventHandler::$dispatcher = $dispatcher;
    $event1                   = new Event1();
    $event2                   = $dispatcher->modify($event1);

    self::assertSame($event1, $event2);
    self::assertEquals([EventHandler::class.'::handle1',
                        EventHandler::class.'::handle2',
                        EventHandler::class.'::handle3'], EventHandler::$log);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
