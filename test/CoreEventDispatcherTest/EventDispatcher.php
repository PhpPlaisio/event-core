<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\CoreEventDispatcherTest;

use Plaisio\Event\CoreEventDispatcher;

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
      'Plaisio\Event\Test\CoreEventDispatcherTest\Event1' => [['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle1', null],
                                                              ['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle2', null],
                                                              ['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle3', null]]
    ];

  /**
   * @inheritdoc
   */
  protected static $notifyHandlers =
    [
      'Plaisio\Event\Test\CoreEventDispatcherTest\Event1' => [['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle1', null],
                                                              ['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle2', null],
                                                              ['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle3', null]],

      'Plaisio\Event\Test\CoreEventDispatcherTest\Event2' => [['Plaisio\Event\Test\CoreEventDispatcherTest\EventHandler::handle4', null]]
    ];

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
