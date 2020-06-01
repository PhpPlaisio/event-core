<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command\Ok;

use Plaisio\PlaisioInterface;

/**
 * An empty event handler.
 */
abstract class OkEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param OkEvent1         $event  The event.
   *
   * @before \Plaisio\Event\Test\Command\Ok\OkEventHandler::handle2
   */
  public static function handle1(PlaisioInterface $object, OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param OkEvent1         $event  The event.
   *
   * @before \Plaisio\Event\Test\Command\Ok\OkEventHandler::handle3
   * @after \Plaisio\Event\Test\Command\Ok\OkEventHandler::handle1
   *
   * @onlyForCompany 1
   */
  public static function handle2(PlaisioInterface $object, OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param OkEvent1         $event  The event.
   *
   * @after \Plaisio\Event\Test\Command\Ok\OkEventHandler::handle2
   */
  public static function handle3(PlaisioInterface $object, OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param OkEvent2         $event  The event.
   */
  public static function handle4(PlaisioInterface $object, OkEvent2 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
