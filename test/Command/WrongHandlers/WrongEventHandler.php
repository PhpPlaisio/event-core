<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command\WrongHandlers;

/**
 * An empty event handler.
 */
abstract class WrongEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dependency must exists.
   *
   * @param WrongEvent $event The event.
   *
   * @after NotExists::handle
   */
  public static function classNotExists(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Events must be a class.
   *
   * @param int $event The event.
   */
  public static function eventsMustBeClass(int $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have one argument only.
   *
   * @param WrongEvent $event The event.
   *
   * @after  Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notHandler1
   */
  public static function handlersNotExists1(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have one argument only.
   *
   * @param WrongEvent $event The event.
   *
   * @before Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notHandler2
   */
  public static function handlersNotExists2(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dependency must exists.
   *
   * @param WrongEvent $event The event.
   *
   * @after Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notExists
   */
  public static function methodNotExists(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must non or one @onlyForCompany tag only.
   *
   * @param WrongEvent $event The event.
   *
   * @onlyForCompany 1
   * @onlyForCompany 2
   */
  public static function multipleOnlyForCompany(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must return nothing
   *
   * @param WrongEvent $event The event.
   *
   * @return int
   */
  public static function nonReturningVoid(WrongEvent $event): int
  {
    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be concrete.
   *
   * @param WrongEvent $event The event.
   */
  abstract static function notConcrete(WrongEvent $event): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * This is not a handler.
   */
  public static function notHandler1(): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * This is not a handler.
   */
  public static function notHandler2(): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have one argument only.
   *
   * @param WrongEvent $event The event.
   * @param int        $bogus The bogus argument.
   */
  public static function twoArguments(WrongEvent $event, int $bogus): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be public.
   *
   * @param WrongEvent $event The event.
   */
  private static function notPublic(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be static.
   *
   * @param WrongEvent $event The event.
   */
  public function notStatic(WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
