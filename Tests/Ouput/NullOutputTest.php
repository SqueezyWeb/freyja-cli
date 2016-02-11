<?php
/**
 * Freyja CLI Null Output Test.
 *
 * @package Freyja\CLI\Tests\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Output;

use Freyja\CLI\Output\NullOutput;
use Freyja\CLI\Output\OutputInterface;

/**
 * Test NullOutput class.
 *
 * @package Freyja\CLI\Tests\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class NullOutputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $output = new NullOutput;

    ob_start();
    $output->write('foo');
    $buffer = ob_get_clean();

    $this->assertSame('', $buffer, 'write() does nothing (at least nothing is printed)');
    $this->assertFalse($output->isDecorated(), 'isDecorated() returns false');
  }

  /**
   * Test verbosity.
   *
   * @since 1.0.0
   * @access public
   */
  public function testVerbosity() {
    $output = new NullOutput;
    $this->assertSame(OutputInterface::VERBOSITY_QUIET, $output->getVerbosity(), 'getVerbosity() returns VERBOSITY_QUIET for NullOutput by default');

    $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
    $this->assertSame(OutputInterface::VERBOSITY_QUIET, $output->getVerbosity(), 'getVerbosity() always returns VERBOSITY_QUIET for NullOutput');
  }
}
