<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command\Cycle;

/**
 * An empty event handler.
 */
abstract class CycleEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param CycleEvent $event The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle3
   */
  public static function handle1(CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param CycleEvent $event The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle1
   */
  public static function handle2(CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param CycleEvent $event The event.
   *
   * @after \Plaisio\Event\Test\Command\Cycle\CycleEventHandler::handle2
   */
  public static function handle3(CycleEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
