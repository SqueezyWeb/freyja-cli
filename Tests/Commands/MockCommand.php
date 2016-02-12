<?php
/**
 * Freyja CLI Mock Command for test purposes.
 *
 * @package Freyja\CLI\Tests\Commands
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Commands;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

/**
 * Mock Command.
 *
 * @package Freyja\CLI\Tests\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class MockCommand extends Command {
  /**
   * {@inheritdoc}
   */
  protected function configure() {

  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

  }
}
