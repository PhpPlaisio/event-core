<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Test\Helper;

use PHPUnit\Framework\TestCase;
use SetBased\Abc\Event\Helper\Graph;

/**
 * Test cases for class Graph.
 */
class GraphTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test detecting a cycle.
   */
  public function testCycle(): void
  {
    $graph = new Graph();

    // Add a tree.
    $graph->addEdge('A', 'AB');
    $graph->addEdge('A', 'AC');
    $graph->addEdge('AB', 'ABC');
    $graph->addEdge('AC', 'AC/DC');
    $graph->addEdge('AC/DC', 'Let There Be Rock');

    // Add a cycle.
    $graph->addEdge('one', 'two');
    $graph->addEdge('two', 'three');
    $graph->addEdge('three', 'one');

    $hasCycle = $graph->isCyclic($cycle);
    self::assertTrue($hasCycle);
    self::assertSame(['one', 'two', 'three'], $cycle);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test depth.
   */
  public function testDepth1(): void
  {
    $graph = new Graph();

    // Add a tree.
    $graph->addEdge('A', 'AB');
    $graph->addEdge('A', 'AC');
    $graph->addEdge('AB', 'ABC');
    $graph->addEdge('AC', 'AC/DC');
    $graph->addEdge('AC/DC', 'Let There Be Rock');

    $depths = $graph->depth();
    self::assertEquals(['A'                 => 0,
                        'AC'                => 1,
                        'AC/DC'             => 2,
                        'Let There Be Rock' => 3,
                        'AB'                => 1,
                        'ABC'               => 2],
                       $depths);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test depth. Adding edges in different order.
   */
  public function testDepth2(): void
  {
    $graph = new Graph();

    // Add a tree.
    $graph->addEdge('AC/DC', 'Let There Be Rock');
    $graph->addEdge('A', 'AB');
    $graph->addEdge('AC', 'AC/DC');
    $graph->addEdge('A', 'AC');
    $graph->addEdge('AB', 'ABC');

    $depths = $graph->depth();
    self::assertEquals(['A'                 => 0,
                        'AC'                => 1,
                        'AC/DC'             => 2,
                        'Let There Be Rock' => 3,
                        'AB'                => 1,
                        'ABC'               => 2],
                       $depths);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test detecting a cycle without a cycle.
   */
  public function testNoCycle(): void
  {
    $graph = new Graph();

    // Add a tree.
    $graph->addEdge('A', 'AB');
    $graph->addEdge('A', 'AC');
    $graph->addEdge('AB', 'ABC');
    $graph->addEdge('AC', 'AC/DC');
    $graph->addEdge('AC/DC', 'Let There Be Rock');

    $hasCycle = $graph->isCyclic($cycle);
    self::assertFalse($hasCycle);
    self::assertNull($cycle);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
