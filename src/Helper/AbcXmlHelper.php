<?php
declare(strict_types=1);

namespace SetBased\Abc\Event\Helper;

/**
 * Helper class for retrieving information about abc.xml files.
 */
class AbcXmlHelper extends \SetBased\Abc\Console\Helper\AbcXmlHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the event handlers found in the abc.xml file.
   *
   * @param string $type The type of event handler: 'modify' or 'notify'.
   *
   * @return string[]
   */
  public function extractEventHandlers(string $type): array
  {
    $classes = [];

    $xpath = new \DOMXpath($this->xml);
    $list  = $xpath->query(sprintf('/abc/event/%s/handler', $type));
    foreach ($list as $item)
    {
      $classes[] = $item->nodeValue;
    }

    return $classes;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the class name and the path of the generated event dispatcher.
   *
   * @return string[]
   */
  public function extractEventDispatcherClass(): array
  {
    $xpath = new \DOMXpath($this->xml);

    $list  = $xpath->query('/abc/event/dispatcher/class');
    $class = $list[0]->nodeValue;

    $list = $xpath->query('/abc/event/dispatcher/path');
    $path = $list[0]->nodeValue;

    return [$class, $path];
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
