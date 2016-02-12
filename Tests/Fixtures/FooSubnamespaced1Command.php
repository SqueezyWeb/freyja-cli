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

class FooSubnamespaced1Command extends Command {
  public $input;
  public $output;

  protected function configure() {
    $this->setName('foo:bar:baz')
      ->setDescription('The foo:bar:baz command')
      ->setAliases(array('foobarbaz'));
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;
  }
}
