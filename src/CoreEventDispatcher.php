<?php
declare(strict_types=1);

namespace Plaisio\Event;

use Plaisio\PlaisioInterface;
use Plaisio\PlaisioObject;

/**
 * The core abstract implementation of the event dispatcher.
 */
abstract class CoreEventDispatcher extends PlaisioObject implements EventDispatcher
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The modify event handlers.
   *
   * @var array[]|null
   */
  protected static ?array $modifyHandlers;

  /**
   * The notify event handlers.
   *
   * @var array[]|null
   */
  protected static ?array $notifyHandlers;

  /**
   * Whether this dispatcher is dispatching events.
   *
   * @var bool
   */
  private bool $isRunning;

  /**
   * The event queue.
   *
   * @var \SplQueue
   */
  private \SplQueue $queue;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   */
  public function __construct(PlaisioInterface $object)
  {
    parent::__construct($object);

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
    if ($this->isRunning)
    {
      return;
    }

    $this->isRunning = true;

    while (!$this->queue->isEmpty())
    {
      $event = $this->queue->dequeue();
      $handlers = static::$notifyHandlers[get_class($event)] ?? [];
      foreach ($handlers as $handler)
      {
        [$callable, $cmpId] = $handler;
        if ($cmpId===null || $cmpId===$this->nub->company->cmpId)
        {
          $callable($this, $event);
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
  public function modify(object $event): object
  {
    $handlers = static::$modifyHandlers[get_class($event)] ?? [];
    foreach ($handlers as $handler)
    {
      [$callable, $cmpId] = $handler;
      if ($cmpId===null || $cmpId===$this->nub->company->cmpId)
      {
        $callable($this, $event);
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
  public function notify(object $event): void
  {
    if (!$this->isQueued($event))
    {
      $this->queue->enqueue($event);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether an event is already queued.
   *
   * @param object $event The event.
   *
   * @return bool
   */
  private function isQueued(object $event): bool
  {
    $serial = serialize($event);
    foreach ($this->queue as $tmpEvent)
    {
      if (serialize($tmpEvent)===$serial)
      {
        return true;
      }
    }

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
