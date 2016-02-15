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

class Foo2Command extends Command {
  protected function configure() {
    $this->setName('foo1:bar')
      ->setDescription('The foo1:bar command')
      ->setAliases(array('afoobar2'));
  }

  protected function execute(InputInterface $input, OutputInterface $output){
  }
}
