<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command;

use PHPUnit\Framework\TestCase;
use Plaisio\Event\Command\GenerateEventDispatcherCommand;
use Plaisio\Event\Test\Command\Cycle\CycleEvent;
use Plaisio\Event\Test\Command\WrongHandlers\WrongEvent;
use Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler;
use Plaisio\PlaisioInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Path;

/**
 * Integral test for generating event dispatcher.
 */
class GenerateEventDispatcherCommandTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function setUp(): void
  {
    putenv('COLUMNS=100');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test cycle detection.
   */
  public function testCycle(): void
  {
    copy(Path::join(__DIR__, 'Cycle', 'plaisio-event.xml'), Path::join(getcwd(), 'plaisio-event.xml'));

    $application = new Application();
    $application->add(new GenerateEventDispatcherCommand());

    /** @var GenerateEventDispatcherCommand $command */
    $command       = $application->find('plaisio:generate-core-event-dispatcher');
    $commandTester = new CommandTester($command);

    $commandTester->execute(['command' => $command->getName()]);

    $ret = $commandTester->getStatusCode();
    self::assertEquals(-1, $ret);

    $output = $commandTester->getDisplay();
    $output = preg_replace('/\s+/', ' ', $output);
    $output = str_replace("'", '', $output);
    self::assertStringContainsString('Found cycle:', $output);
    self::assertStringContainsString("Event handlers for ".CycleEvent::class." are cyclic", $output);

    unlink(Path::join(getcwd(), 'plaisio-event.xml'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with event handlers that are fine.
   */
  public function testOk(): void
  {
    copy(Path::join(__DIR__, 'Ok', 'plaisio-event.xml'), Path::join(getcwd(), 'plaisio-event.xml'));

    $application = new Application();
    $application->add(new GenerateEventDispatcherCommand());

    @unlink(__DIR__.'/Ok/EventDispatcher.php');

    /** @var GenerateEventDispatcherCommand $command */
    $command       = $application->find('plaisio:generate-core-event-dispatcher');
    $commandTester = new CommandTester($command);

    $commandTester->execute(['command' => $command->getName()]);

    $ret = $commandTester->getStatusCode();
    self::assertEquals(0, $ret);

    $output = $commandTester->getDisplay();
    self::assertStringContainsString('Wrote test/Command/Ok/EventDispatcher.php', $output);

    $commandTester->execute(['command' => $command->getName()]);

    $ret = $commandTester->getStatusCode();
    self::assertEquals(0, $ret);

    $output = $commandTester->getDisplay();
    self::assertStringContainsString('File test/Command/Ok/EventDispatcher.php is up to date.', $output);

    self::assertFileEquals(__DIR__.'/Ok/EventDispatcher.txt', __DIR__.'/Ok/EventDispatcher.php');

    unlink(Path::join(getcwd(), 'plaisio-event.xml'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test event handlers must be concrete, public, abstract, have one argument, and return void.
   */
  public function testWrongHandlers(): void
  {
    copy(Path::join(__DIR__, 'WrongHandlers', 'plaisio-event.xml'), Path::join(getcwd(), 'plaisio-event.xml'));

    $application = new Application();
    $application->add(new GenerateEventDispatcherCommand());

    /** @var GenerateEventDispatcherCommand $command */
    $command       = $application->find('plaisio:generate-core-event-dispatcher');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    $ret = $commandTester->getStatusCode();
    self::assertEquals(-1, $ret);

    $output = $commandTester->getDisplay();
    $output = preg_replace('/\s+/', ' ', $output);
    $output = str_replace("'", '', $output);
    $output = str_replace('"', '', $output);
    self::assertStringContainsString('Caught reflection exception Class NotExists does not exist', $output);
    self::assertStringContainsString('Argument of event handler '.WrongEventHandler::class."::eventsMustBeClass must be a class",
                                     $output);
    self::assertStringContainsString('Class '.WrongEventHandler::class.' does not have method notExists',
                                     $output);
    self::assertStringContainsString('Found multiple @onlyForCompany annotations', $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::nonReturningVoid must return void',
                                     $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::notConcrete must not be abstract',
                                     $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::notStatic must be static',
                                     $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::notPublic must be public',
                                     $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::threeArguments must have exactly two parameters',
                                     $output);
    self::assertStringContainsString('Event handler '.WrongEventHandler::class.'::oneArgument must have exactly two parameters',
                                     $output);
    self::assertStringContainsString('First parameter of event handler '.WrongEventHandler::class.'::notPlaisioObject must be a '.PlaisioInterface::class.', got a '.WrongEvent::class.'.',
                                     $output);

    unlink(Path::join(getcwd(), 'plaisio-event.xml'));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
