<?php
declare(strict_types=1);

namespace Plaisio\Event\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Event\Command\GenerateEventDispatcherCommand;
use Plaisio\Event\Test\CoreEventDispatcherTest\Event1;
use Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler;
use Plaisio\PlaisioKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test cass for CoreEventDispatcher.
 */
class CoreEventDispatcherTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The kernel for testing purposes.
   *
   * @var PlaisioKernel
   */
  private $kernel;

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * @inheritdoc
   */
  public function setUp(): void
  {
    $this->kernel = new TestKernel();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the event handler.
   */
  public function test1(): void
  {
    putenv(sprintf('%s=%s', 'PLAISIO_CONFIG_DIR', __DIR__.'/CoreEventDispatcherTest'));

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
    EventHandler::$log        = [];
    EventHandler::$dispatcher = $this->kernel->eventDispatcher;
    $this->kernel->eventDispatcher->notify(new Event1());
    $this->kernel->eventDispatcher->dispatch();

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
    EventHandler::$log        = [];
    EventHandler::$dispatcher = $this->kernel->eventDispatcher;;
    $event1 = new Event1();
    $event2 = $this->kernel->eventDispatcher->modify($event1);

    self::assertSame($event1, $event2);
    self::assertEquals([EventHandler::class.'::handle1',
                        EventHandler::class.'::handle2',
                        EventHandler::class.'::handle3'], EventHandler::$log);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
