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

class FooCommand extends Command {
  public $input;
  public $output;

  protected function configure() {
    $this->setName('foo:bar')
      ->setDescription('The foo:bar command')
      ->setAliases(array('afoobar'));
  }

  protected function interact(InputInterface $input, OutputInterface $output){
    $output->writeln('interact called');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $output->writeln('called');
  }
}
