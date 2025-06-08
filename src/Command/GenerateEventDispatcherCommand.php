<?php
declare(strict_types=1);

namespace Plaisio\Event\Command;

use Plaisio\Console\Command\PlaisioCommand;
use Plaisio\Console\Helper\PlaisioXmlPathHelper;
use Plaisio\Console\Helper\TwoPhaseWrite;
use Plaisio\Event\Exception\MetadataExtractorException;
use Plaisio\Event\Helper\EventDispatcherCodeGenerator;
use Plaisio\Event\Helper\EventHandlerMetadataExtractor;
use Plaisio\Event\Helper\PlaisioXmlQueryHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for generation the code for the core's exception handler.
 */
class GenerateEventDispatcherCommand extends PlaisioCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure(): void
  {
    $this->setName('plaisio:generate-core-event-dispatcher')
         ->setDescription('Generates the code for the core\'s event dispatcher');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $this->io->title('Plaisio: Generate Core Event Dispatcher');

    try
    {
      $metadataExtractor = new EventHandlerMetadataExtractor($this->io);
      $modifyHandlers    = $metadataExtractor->extractEventHandlers('modify');
      $notifyHandlers    = $metadataExtractor->extractEventHandlers('notify');

      $xmlHelper = new PlaisioXmlQueryHelper(PlaisioXmlPathHelper::plaisioXmlPath('event'));
      [$class, $path] = $xmlHelper->queryEventDispatcherClass();

      $generator = new EventDispatcherCodeGenerator();
      $code      = $generator->generateCode($class, $modifyHandlers, $notifyHandlers);

      $writer = new TwoPhaseWrite($this->io);
      $writer->write($path, $code);
    }
    catch (MetadataExtractorException $exception)
    {
      $this->io->error($exception->getMessage());

      return -1;
    }

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
