<?php
declare(strict_types=1);

namespace Plaisio\Event\Test;

use Plaisio\CompanyResolver\CompanyResolver;
use Plaisio\CompanyResolver\UniCompanyResolver;
use Plaisio\Kernel\Nub;

/**
 * Kernel for testing purposes.
 */
class TestKernel extends Nub
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    parent::__construct();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the helper object for deriving the company.
   *
   * @return CompanyResolver
   */
  public function getCompanyResolver(): CompanyResolver
  {
    return new UniCompanyResolver(1);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
