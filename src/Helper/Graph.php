<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Helper;

/**
 * Simple directed graph class for cyclic detection.
 */
class Graph
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The vertices of the graph.
   *
   * @var array
   */
  private $edges = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract the found cycle form the recursion stack.
   *
   * @param array $recursion The recursion stack.
   *
   * @return array
   */
  private static function extractCycle(array $recursion): array
  {
    $ret = [];
    foreach ($recursion as $node => $bool)
    {
      if ($bool) $ret[] = $node;
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a directed edge to the graph.
   *
   * @param string $from The name of from node.
   * @param string $to   The name of the to node.
   */
  public function addEdge(string $from, string $to): void
  {
    if (!empty($this->edges[$from])) $this->edges[$from] = [];

    $this->edges[$from][] = $to;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an array with the depth of the nodes.
   *
   * @return array
   */
  public function depth(): array
  {
    $depths = [];
    foreach ($this->edges as $parent => $child)
    {
      $this->depthHelper(0, $parent, $depths);
    }

    return $depths;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return true if the graph has one or more cycles.
   *
   * @param string|null $cycle On return a found cycle if any.
   *
   * @return bool
   */
  public function isCyclic(?string &$cycle = null): bool
  {
    $visited   = [];
    $recursion = [];
    foreach ($this->edges as $parent => $child)
    {
      if (!($visited[$parent] ?? false))
      {
        if ($this->isCyclicHelper($parent, $visited, $recursion))
        {
          $cycle = self::extractCycle($recursion);

          return true;
        }
      }
    }

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper function for depth function.
   *
   * @param int    $depth  The current depth.
   * @param string $node   The current node.
   * @param array  $depths The depth of all nodes.
   */
  private function depthHelper(int $depth, string $node, array &$depths): void
  {
    if (($depths[$node] ?? -1)<$depth)
    {
      $depths[$node] = $depth;
      if (!empty($this->edges[$node]))
      {
        foreach ($this->edges[$node] as $child)
        {
          $this->depthHelper($depth + 1, $child, $depths);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper function for DFS cyclic detection.
   *
   * @param string   $node      The current node.
   * @param string[] $visited   The visited nodes.
   * @param string[] $recursion The recursion stack.
   *
   * @return bool
   */
  private function isCyclicHelper(string $node, array &$visited, array &$recursion): bool
  {
    $visited[$node]   = true;
    $recursion[$node] = true;

    if (!empty($this->edges[$node]))
    {
      foreach ($this->edges[$node] as $child)
      {
        if (($visited[$child] ?? false)===false)
        {
          if ($this->isCyclicHelper($child, $visited, $recursion))
          {
            return true;
          }
        }
        elseif (($recursion[$child] ?? false))
        {
          return true;
        }
      }
    }

    $recursion[$node] = false;

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
