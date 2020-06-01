<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command\Cycle;

use Plaisio\PlaisioInterface;

/**
 * An empty event handler.
 */
abstract class CycleEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param CycleEvent       $event  The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle3
   */
  public static function handle1(PlaisioInterface $object, CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param CycleEvent       $event  The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle1
   */
  public static function handle2(PlaisioInterface $object, CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param CycleEvent       $event  The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle2
   */
  public static function handle3(PlaisioInterface $object, CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
