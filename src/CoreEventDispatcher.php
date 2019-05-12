<?php
declare(strict_types=1);

namespace SetBased\Abc\Event;

use SetBased\Abc\Abc;

/**
 * The core abstract implementation of the event dispatcher.
 */
abstract class CoreEventDispatcher implements EventDispatcher
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The modify event handlers.
   *
   * @var array[]|null
   */
  protected static $modifyHandlers;

  /**
   * The notify event handlers.
   *
   * @var array[]|null
   */
  protected static $notifyHandlers;

  /**
   * True if ans only if this dispatcher is dispatching events.
   *
   * @var bool
   */
  private $isRunning;

  /**
   * The event queue.
   *
   * @var \SplQueue
   */
  private $queue;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    $this->isRunning = false;
    $this->queue     = new \SplQueue();
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
      $handlers = static::$notifyHandlers[get_class($event)] ?? [];
      foreach ($handlers as $handler)
      {
        list($callable, $cmpId) = $handler;
        if ($cmpId===null || $cmpId==Abc::$companyResolver->getCmpId())
        {
          $callable($event);
        }
      }
    }

    $this->isRunning = false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Applies all listeners with an event to modify.
   *
   * @param object $event The event to modify.
   *
   * @return object The event that was passed, now modified by callers.
   */
  public function modify($event)
  {
    $handlers = static::$modifyHandlers[get_class($event)] ?? [];
    foreach ($handlers as $handler)
    {
      list($callable, $cmpId) = $handler;
      if ($cmpId===null || $cmpId==Abc::$companyResolver->getCmpId())
      {
        $callable($event);
      }
    }

    return $event;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Queues an event.
   *
   * @param object $event The event.
   */
  public function notify($event): void
  {
    $this->queue->enqueue($event);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
