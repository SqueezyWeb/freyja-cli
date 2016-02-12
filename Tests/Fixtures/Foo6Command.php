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

class Foo6Command extends Command {
  protected function configure() {
    $this->setName('0foo:bar')->setDescription('0foo:bar command');
  }
}
