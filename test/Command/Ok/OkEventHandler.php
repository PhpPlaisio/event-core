<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Test\Command\Ok;

/**
 * An empty event handler.
 */
abstract class OkEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param OkEvent1 $event The event.
   *
   * @before \SetBased\Abc\Event\Test\Command\Ok\OkEventHandler::handle2
   */
  public static function handle1(OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param OkEvent1 $event The event.
   *
   * @before \SetBased\Abc\Event\Test\Command\Ok\OkEventHandler::handle3
   * @after \SetBased\Abc\Event\Test\Command\Ok\OkEventHandler::handle1
   *
   * @onlyForCompany 1
   */
  public static function handle2(OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param OkEvent1 $event The event.
   *
   * @after \SetBased\Abc\Event\Test\Command\Ok\OkEventHandler::handle2
   */
  public static function handle3(OkEvent1 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Empty event handler.
   *
   * @param OkEvent2 $event The event.
   */
  public static function handle4(OkEvent2 $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
