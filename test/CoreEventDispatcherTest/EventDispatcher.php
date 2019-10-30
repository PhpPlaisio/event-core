<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Test\CoreEventDispatcherTest;

use SetBased\Abc\Event\CoreEventDispatcher;

/**
 * Concrete implementation of the event dispatcher.
 */
class EventDispatcher extends CoreEventDispatcher
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected static $modifyHandlers =
    [
      'SetBased\Abc\Event\Test\CoreEventDispatcherTest\Event1' => [['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle1', null],
                                                                   ['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle2', null],
                                                                   ['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle3', null]]
    ];

  /**
   * @inheritdoc
   */
  protected static $notifyHandlers =
    [
      'SetBased\Abc\Event\Test\CoreEventDispatcherTest\Event1' => [['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle1', null],
                                                                   ['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle2', null],
                                                                   ['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle3', null]],

      'SetBased\Abc\Event\Test\CoreEventDispatcherTest\Event2' => [['SetBased\Abc\Event\Test\CoreEventDispatcherTest\EventHandler::handle4', null]]
    ];

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
