<?php
/**
 * Freyja CLI Stream Output Test.
 *
 * @package Freyja\CLI\Tests\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Output;

use Freyja\CLI\Output\Output;
use Freyja\CLI\Output\StreamOutput;

/**
 * Test StreamOutput class.
 *
 * @package Freyja\CLI\Tests\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class StreamOutputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Stream resource.
   *
   * @since 1.0.0
   * @access protected
   * @var resource
   */
  protected $stream;

  /**
   * Set up tests.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function setUp() {
    $this->stream = fopen('php://memory', 'a', false);
  }

  /**
   * Tear down tests.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function tearDown() {
    $this->stream = null;
  }

  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $output = new StreamOutput($this->stream, Output::VERBOSITY_QUIET, true);
    $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first parameter');
    $this->assertTrue($output->isDecorated(), '__construct() takes the decorated flag as its second argument');
  }

  /**
   * Test that a stream resource is required.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\InvalidArgumentException
   * @expectedExceptionMessage The StreamOutput class needs a stream as its first argument.
   */
  public function testStreamIsRequired() {
    new StreamOutput('foo');
  }

  /**
   * Test getStream().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetStream() {
    $output = new StreamOutput($this->stream);
    $this->assertEquals($this->stream, $output->getStream(), 'getStream() returns the current stream');
  }

  /**
   * Test doWrite().
   *
   * @since 1.0.0
   * @access public
   */
  public function testDoWrite() {
    $output = new StreamOutput($this->stream);
    $output->writeln('foo');
    rewind($output->getStream());
    $this->assertEquals('foo'.PHP_EOL, stream_get_contents($output->getStream()), 'doWrite() writes to the stream');
  }
}
