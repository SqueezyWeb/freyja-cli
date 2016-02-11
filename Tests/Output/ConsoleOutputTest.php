<?php
/**
 * Freyja CLI Console Output Test.
 *
 * @package Freyja\CLI\Tests\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Output;

use Freyja\CLI\Output\ConsoleOutput;
use Freyja\CLI\Output\Output;

/**
 * Test ConsoleOutput class.
 *
 * @package Freyja\CLI\Tests\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ConsoleOutputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $output = new ConsoleOutput(Output::VERBOSITY_QUIET, true);
    $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
    $this->assertSame($output->getFormatter(), $output->getErrorOutput()->getFormatter(), '__construct() takes a formatter or null as the third argument');
  }
}
