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
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

class Foo3Command extends Command {
  protected function configure() {
    $this->setName('foo3:bar')
      ->setDescription('The foo3:bar command');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    try {
      try {
        throw new \Exception('First exception <p>this is html</p>');
      } catch (\Exception $e) {
        throw new \Exception('Second exception <comment>comment</comment>', 0, $e);
      }
    } catch (\Exception $e) {
      throw new \Exception('Third exception <fg=blue;bg=red>comment</>', 404, $e);
    }
  }
}
