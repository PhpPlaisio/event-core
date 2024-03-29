<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\CoreEventDispatcherTest;

use Plaisio\PlaisioInterface;

/**
 * An empty event handler.
 */
class EventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ID of my company.
   */
  const MY_COMPANY = 1;

  /**
   * The event dispatcher.
   *
   * @var \Plaisio\Event\EventDispatcher
   */
  public static \Plaisio\Event\EventDispatcher $dispatcher;

  /**
   * The log.
   *
   * @var array
   */
  public static array $log = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param Event1           $event  The event.
   *
   * @before \Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle2
   */
  public static function handle1(PlaisioInterface $object, Event1 $event): void
  {
    self::$log[] = __METHOD__;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param Event1           $event  The event.
   *
   * @before \Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle3
   * @after \Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle1
   */
  public static function handle2(PlaisioInterface $object, Event1 $event): void
  {
    self::$log[] = __METHOD__;

    self::$dispatcher->notify(new Event2());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param Event1           $event  The event.
   *
   * @after \Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle2
   *
   * @onlyForCompany \Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::MY_COMPANY
   */
  public static function handle3(PlaisioInterface $object, Event1 $event): void
  {
    self::$log[] = __METHOD__;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param Event2           $event  The event.
   *
   * @onlyForCompany self::MY_COMPANY
   */
  public static function handle4(PlaisioInterface $object, Event2 $event): void
  {
    self::$log[] = __METHOD__;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
