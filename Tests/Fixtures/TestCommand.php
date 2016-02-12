<?php
/**
 * Freyja CLI Test Command.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */
namespace Freyja\CLI\Tests\Fixtures;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

/**
 * Test Command.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class TestCommand extends Command {
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('namespace:name')
      ->setAliases(array('name'))
      ->setDescription('description')
      ->setHelp('help');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('execute called');
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $output->writeln('interact called');
  }
}
