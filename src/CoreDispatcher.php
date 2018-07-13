<?php

namespace SetBased\Abc\Event;

use SetBased\Abc\Abc;
use SplQueue;

/**
 * The core implementation of the event dispatcher.
 */
class CoreDispatcher implements EventDispatcher
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * True if ans only if this dispatcher is dispatching events.
   *
   * @var bool
   */
  private $isRunning;

  /**
   * The event queue.
   *
   * @var SplQueue
   */
  private $queue;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    $this->isRunning = false;
    $this->queue     = new SplQueue();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dispatches all queued events.
   */
  public function dispatch(): void
  {
    // Return immediately if this dispatcher is dispatching events already.
    if ($this->isRunning) return;

    $this->isRunning = true;

    while (!$this->queue->isEmpty())
    {
      $event    = $this->queue->dequeue();
      $handlers = Abc::$DL::abcEventCoreGetHandlers(Abc::$companyResolver->getCmpId(), $event->getId());
      foreach ($handlers as $handler)
      {
        $class = $handler['ael_class'];
        /** @var EventHandler $object */
        $object = new $class();
        $object->handle($event);
      }
    }

    $this->isRunning = false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Provides all listeners with an event to modify.
   *
   * @param Event $event The event to modify.
   *
   * @return Event The event that was passed, now modified by callers.
   */
  public function modify(Event $event): Event
  {
    throw new \LogicException('Not implemented');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Queues an event.
   *
   * @param Event $event The event.
   */
  public function notify(Event $event): void
  {
    $this->queue->enqueue($event);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
