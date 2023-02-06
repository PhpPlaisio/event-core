<?php
declare(strict_types=1);

namespace Plaisio\Event\Helper;

/**
 * Helper class for retrieving information about plaisio.xml files.
 */
class PlaisioXmlQueryHelper extends \Plaisio\Console\Helper\PlaisioXmlQueryHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the class name and the path of the generated event dispatcher.
   *
   * @return string[]
   */
  public function queryEventDispatcherClass(): array
  {
    $xpath = new \DOMXpath($this->xml);

    $list  = $xpath->query('/event/dispatcher/class');
    $class = $list[0]->nodeValue;

    $list = $xpath->query('/event/dispatcher/path');
    $path = $list[0]->nodeValue;

    return [$class, $path];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the event handlers found in the plaisio.xml file.
   *
   * @param string $type The type of event handler: 'modify' or 'notify'.
   *
   * @return string[]
   */
  public function queryEventHandlers(string $type): array
  {
    $classes = [];

    $xpath = new \DOMXpath($this->xml);
    $list  = $xpath->query(sprintf('/event/%s/handler', $type));
    foreach ($list as $item)
    {
      $classes[] = $item->nodeValue;
    }

    return $classes;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
