<?php
/**
 * Freyja CLI Descriptor Command.
 *
 * @package Freyja\CLI\Tests\Textures
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Fixtures;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

/**
 * Descriptor Command.
 *
 * @package Freyja\CLI\Tests\Textures
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class DescriptorCommand1 extends Command {
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('descriptor:command1')
      ->setAliases(array('alias1', 'alias2'))
      ->setDescription('command 1 description')
      ->setHelp('command 1 help');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

  }
}
