<?php
declare(strict_types=1);

namespace Plaisio\Event\Test;

use Plaisio\CompanyResolver\CompanyResolver;
use Plaisio\CompanyResolver\UniCompanyResolver;
use Plaisio\Event\Test\CoreEventDispatcherTest\EventDispatcher;
use Plaisio\PlaisioKernel;

/**
 * Kernel for testing purposes.
 */
class TestKernel extends PlaisioKernel
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the helper object for deriving the company.
   *
   * @return CompanyResolver
   */
  public function getCompany(): CompanyResolver
  {
    return new UniCompanyResolver(1);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the event dispatcher.
   *
   * @return EventDispatcher
   */
  public function getEventDispatcher(): EventDispatcher
  {
    return new EventDispatcher($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
