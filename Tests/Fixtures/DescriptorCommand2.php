<?php
/**
 * Freyja CLI Descriptor Command.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Fixtures;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

class DescriptorCommand2 extends Command {
  protected function configure() {
    $this->setName('descriptor:command2')
      ->setDescription('command 2 description')
      ->setHelp('command 2 help')
      ->addUsage('-o|--option_name <argument_name>')
      ->addUsage('<argument_name>')
      ->addArgument('argument_name', Argument::REQUIRED)
      ->addOption('option_name', 'o', Option::VALUE_NONE);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

  }
}
