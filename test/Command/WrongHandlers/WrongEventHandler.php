<?php
declare(strict_types=1);

namespace Plaisio\Event\Test\Command\WrongHandlers;

use Plaisio\PlaisioInterface;

/**
 * An empty event handler.
 */
abstract class WrongEventHandler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dependency must exist.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   *
   * @after NotExists::handle
   */
  public static function classNotExists(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Events must be a class.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param int              $event  The event.
   */
  public static function eventsMustBeClass(PlaisioInterface $object, int $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have one argument only.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   *
   * @after Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notHandler1
   */
  public static function handlersNotExists1(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have one argument only.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   *
   * @before Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notHandler2
   */
  public static function handlersNotExists2(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dependency must exist.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   *
   * @after Plaisio\Event\Test\Command\WrongHandlers\WrongEventHandler::notExists
   */
  public static function methodNotExists(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must non or one @onlyForCompany tag only.
   *
   * @param WrongEvent       $event  The eve
   * @param PlaisioInterface $object The parent PhpPlaisio object.nt.
   *
   * @onlyForCompany 1
   * @onlyForCompany 2
   */
  public static function multipleOnlyForCompany(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * First argument must be a PlaisioInterface object.
   *
   * @param WrongEvent $object The parent PhpPlaisio object.
   * @param WrongEvent $event  The event.
   */
  public static function notPlaisioObject(WrongEvent $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must return nothing
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   *
   * @return int
   */
  public static function nonReturningVoid(PlaisioInterface $object, WrongEvent $event): int
  {
    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be concrete.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   */
  abstract static function notConcrete(PlaisioInterface $object, WrongEvent $event): void;

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
   * Event handlers must have two arguments only.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   */
  public static function oneArgument(PlaisioInterface $object): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must have two arguments only.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   * @param int              $bogus  The bogus argument.
   */
  public static function threeArguments(PlaisioInterface $object, WrongEvent $event, int $bogus): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be public.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   */
  private static function notPublic(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Event handlers must be static.
   *
   * @param PlaisioInterface $object The parent PhpPlaisio object.
   * @param WrongEvent       $event  The event.
   */
  public function notStatic(PlaisioInterface $object, WrongEvent $event): void
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
