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

/**
 * Foo Command.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class Foo1Command extends Command {
  public $input;
  public $output;

  protected function configure() {
    $this->setName('foo:bar1')
      ->setDescription('The foo:bar1 command')
      ->setAliases(array('afoobar1'));
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;
  }
}
