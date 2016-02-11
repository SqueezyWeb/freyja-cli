<?php
/**
 * Freyja CLI Output Test.
 *
 * @package Freyja\CLI\Tests\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Output;

use Freyja\CLI\Output\Output;
use Freyja\CLI\Formatters\Styles\OutputFormatter as Style;

/**
 * Test Output class.
 *
 * @package Freyja\CLI\Tests\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class OutputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $output = new TestOutput(Output::VERBOSITY_QUIET, true);
    $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
    $this->assertTrue($output->isDecorated(), '__construct() takes the decorated flag as its second argument');
  }

  /**
   * Test setDecorated().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetDecorated() {
    $output = new TestOutput;
    $output->setDecorated(true);
    $this->assertTrue($output->isDecorated(), 'setDecorated() sets the decorated flag');
  }

  /**
   * Test setVerbosity() and getVerbosity().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetGetVerbosity() {
    $output = new TestOutput();
    $output->setVerbosity(Output::VERBOSITY_QUIET);
    $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '->setVerbosity() sets the verbosity');

    $this->assertTrue($output->isQuiet());
    $this->assertFalse($output->isVerbose());
    $this->assertFalse($output->isVeryVerbose());
    $this->assertFalse($output->isDebug());

    $output->setVerbosity(Output::VERBOSITY_NORMAL);
    $this->assertFalse($output->isQuiet());
    $this->assertFalse($output->isVerbose());
    $this->assertFalse($output->isVeryVerbose());
    $this->assertFalse($output->isDebug());

    $output->setVerbosity(Output::VERBOSITY_VERBOSE);
    $this->assertFalse($output->isQuiet());
    $this->assertTrue($output->isVerbose());
    $this->assertFalse($output->isVeryVerbose());
    $this->assertFalse($output->isDebug());

    $output->setVerbosity(Output::VERBOSITY_VERY_VERBOSE);
    $this->assertFalse($output->isQuiet());
    $this->assertTrue($output->isVerbose());
    $this->assertTrue($output->isVeryVerbose());
    $this->assertFalse($output->isDebug());

    $output->setVerbosity(Output::VERBOSITY_DEBUG);
    $this->assertFalse($output->isQuiet());
    $this->assertTrue($output->isVerbose());
    $this->assertTrue($output->isVeryVerbose());
    $this->assertTrue($output->isDebug());
  }

  /**
   * Test writeln() with VERBOSITY_QUIET.
   *
   * @since 1.0.0
   * @access public
   */
  public function testWriteWithVerbosityQuiet() {
    $output = new TestOutput(Output::VERBOSITY_QUIET);
    $output->writeln('foo');
    $this->assertEquals('', $output->output, 'writeln() outputs nothing if verbosity is set to VERBOSITY_QUIET');
  }

  /**
   * Test writeln() with array of messages.
   *
   * @since 1.0.0
   * @access public
   */
  public function testWriteAnArrawOfMessages() {
    $output = new TestOutput;
    $output->writeln(array('foo', 'bar'));
    $this->assertEquals("foo\nbar\n", $output->output, 'writeln() can take an array of messages to output');
  }

  /**
   * Test writeln() with raw message.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $message
   * @param string $type
   * @param string $expected_output
   *
   * @dataProvider provideWriteArguments
   */
  public function testWriteRawMessage($message, $type, $expected_output) {
    $output = new TestOutput;
    $output->writeln($message, $type);
    $this->assertEquals($expected_output, $output->output);
  }

  /**
   * Provide arguments for use with writeln().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideWriteArguments() {
    return array(
      array('<info>foo</info>', Output::OUTPUT_RAW, "<info>foo</info>\n"),
      array('<info>foo</info>', Output::OUTPUT_PLAIN, "foo\n")
    );
  }

  /**
   * Test writeln() with decoration turned off.
   *
   * @since 1.0.0
   * @access public
   */
  public function testWriteWithDecorationTurnedOff() {
    $output = new TestOutput;
    $output->setDecorated(false);
    $output->writeln('<info>foo</info>');
    $this->assertEquals("foo\n", $output->output, 'writeln() strips decoration tags if decoration is set to false');
  }

  /**
   * Test writeln() with decorated message.
   *
   * @since 1.0.0
   * @access public
   */
  public function testWriteDecoratedMessage() {
    $foo_style = new Style('yellow', 'red', array('blink'));
    $output = new TestOutput;
    $output->getFormatter()->setStyle('FOO', $foo_style);
    $output->setDecorated(true);
    $output->writeln('<foo>foo</foo>');
    $this->assertEquals(
      "\033[33;41;5mfoo\033[39;49;25m\n",
      $output->output,
      'writeln() decorates the output'
    );
  }

  /**
   * Test write() and writeln() with invalid style.
   *
   * @since 1.0.0
   * @access public
   */
  public function testWriteWithInvalidStyle() {
    $output = new TestOutput;

    $output->clear();
    $output->write('<bar>foo</bar>');
    $this->assertEquals('<bar>foo</bar>', $output->output, 'write() does nothing when a style does not exist');

    $output->clear();
    $output->writeln('<bar>foo</bar>');
    $this->assertEquals("<bar>foo</bar>\n", $output->output, 'writeln() does nothing when a style does not exist');
  }

  /**
   * Test write() with verbosity option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $verbosity
   * @param string $expected
   * @param string $msg
   *
   * @dataProvider verbosityProvider
   */
  public function testWriteWithVerbosityOption($verbosity, $expected, $msg) {
    $output = new TestOutput();

    $output->setVerbosity($verbosity);
    $output->clear();
    $output->write('1', false);
    $output->write('2', false, Output::VERBOSITY_QUIET);
    $output->write('3', false, Output::VERBOSITY_NORMAL);
    $output->write('4', false, Output::VERBOSITY_VERBOSE);
    $output->write('5', false, Output::VERBOSITY_VERY_VERBOSE);
    $output->write('6', false, Output::VERBOSITY_DEBUG);
    $this->assertEquals($expected, $output->output, $msg);
  }

  /**
   * Provide verbosity.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function verbosityProvider() {
    return array(
      array(Output::VERBOSITY_QUIET, '2', 'write() in QUIET mode only outputs when an explicit QUIET verbosity is passed'),
      array(Output::VERBOSITY_NORMAL, '123', 'write() in NORMAL mode outputs anything below an explicit VERBOSE verbosity'),
      array(Output::VERBOSITY_VERBOSE, '1234', 'write() in VERBOSE mode outputs anything below an explicit VERY_VERBOSE verbosity'),
      array(Output::VERBOSITY_VERY_VERBOSE, '12345', 'write() in VERY_VERBOSE mode outputs anything below an explicit DEBUG verbosity'),
      array(Output::VERBOSITY_DEBUG, '123456', 'write() in DEBUG mode outputs everything'),
    );
  }
}

/**
 * Test Output.
 *
 * @package Freyja\CLI\Tests\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class TestOutput extends Output {
  /**
   * Output string.
   *
   * @since 1.0.0
   * @access public
   * @var string
   */
  public $output = '';

  /**
   * Clear output.
   *
   * @since 1.0.0
   * @access public
   */
  public function clear() {
    $this->output = '';
  }

  /**
   * {@inheritdoc}
   */
  protected function doWrite($message, $newline) {
    $this->output .= $message.($newline ? "\n" : '');
  }
}
