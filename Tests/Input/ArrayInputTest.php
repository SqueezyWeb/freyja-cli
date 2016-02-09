<?php
/**
 * Freyja CLI ArrayInput Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Input;

use Freyja\CLI\Input\ArrayInput;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;

/**
 * Tests for ArrayInput class.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ArrayInputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test getFirstArgument().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetFirstArgument() {
    $input = new ArrayInput(array());
    $this->assertNull($input->getFirstArgument(), 'getFirstArgument() returns null if no arguments were passed');

    $input = new ArrayInput(array('name' => 'Fabien'));
    $this->assertEquals('Fabien', $input->getFirstArgument(), 'getFirstArgument() returns the first argument passed');

    $input = new ArrayInput(array('--foo' => 'bar', 'name' => 'Fabien'));
    $this->assertEquals('Fabien', $input->getFirstArgument(), 'getFirstArgument() returns the first argument passed');
  }

  /**
   * Test hasParameterOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasParameterOption() {
    $input = new ArrayInput(array('name' => 'Fabien', '--foo' => 'bar'));
    $this->assertTrue($input->hasParameterOption('--foo'), 'hasParameterOption() returns true if an option is present in the passed parameters');
    $this->assertFalse($input->hasParameterOption('--bar'), 'hasParameterOption() returns false if an option is not present in the passed parameters');

    $input = new ArrayInput(array('--foo'));
    $this->assertTrue($input->hasParameterOption('--foo'), 'hasParameterOption() returns true if an option is present in the passed parameters');

    $input = new ArrayInput(array('--foo', '--', '--bar'));
    $this->assertTrue($input->hasParameterOption('--bar'), 'hasParameterOption() returns true if an option is present in the passed parameters');
    $this->assertFalse($input->hasParameterOption('--bar', true), 'hasParameterOption()) returns false if an option is present in the passed parameters after an end of options signal');
  }

  /**
   * Test getParameterOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetParameterOption() {
    $input = new ArrayInput(array('name' => 'Fabien', '--foo' => 'bar'));
    $this->assertEquals('bar', $input->getParameterOption('--foo'), 'getParameterOption() returns the option of specified name');
    $this->assertFalse($input->getParameterOption('--bar'), 'getParameterOption() returns the default if an option is not present in the passed parameters');

    $input = new ArrayInput(array('Fabien', '--foo' => 'bar'));
    $this->assertEquals('bar', $input->getParameterOption('--foo'), 'getParameterOption() returns the option of specified name');

    $input = new ArrayInput(array('--foo', '--', '--bar' => 'woop'));
    $this->assertEquals('woop', $input->getParameterOption('--bar'), 'getParameterOption() returns the correct value if an option is present in the passed parameters');
    $this->assertFalse($input->getParameterOption('--bar', false, true), 'getParameterOption() returns false if an option is present in the passed parameters after an end of options signal');
  }

  /**
   * Test parseArguments().
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseArguments() {
    $input = new ArrayInput(array('name' => 'foo'), new Definition(array(new Argument('name'))));

    $this->assertEquals(array('name' => 'foo'), $input->getArguments(), 'parse() parses required arguments');
  }

  /**
   * Test parseOptions().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideOptions
   */
  public function testParseOptions($input, $options, $expected_options, $message) {
    $input = new ArrayInput($input, new Definition($options));

    $this->assertEquals($expected_options, $input->getOptions(), $message);
  }

  /**
   * Provide options to testParseOptions().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideOptions() {
    return array(
      array(
        array('--foo' => 'bar'),
        array(new Option('foo')),
        array('foo' => 'bar'),
        'parse() parses long options',
      ),
      array(
        array('--foo' => 'bar'),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL, '', 'default')),
        array('foo' => 'bar'),
        'parse() parses long options with a default value',
      ),
      array(
        array('--foo' => null),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL, '', 'default')),
        array('foo' => 'default'),
        'parse() parses long options with a default value',
      ),
      array(
        array('-f' => 'bar'),
        array(new Option('foo', 'f')),
        array('foo' => 'bar'),
        'parse() parses short options',
      ),
      array(
        array('--' => null, '-f' => 'bar'),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL, '', 'default')),
        array('foo' => 'default'),
        'parse() does not parse opts after an end of options signal',
      ),
      array(
        array('--' => null),
        array(),
        array(),
        'parse() does not choke on end of options signal',
      ),
    );
  }

  /**
   * Test prasing of invalid input.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideInvalidInput
   */
  public function testParseInvalidInput($parameters, $definition, $expected_exception, $expected_exception_message) {
    $this->setExpectedException('Freyja\CLI\Exceptions\\'.$expected_exception, $expected_exception_message);

    new ArrayInput($parameters, $definition);
  }

  /**
   * Provide invalid input.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideInvalidInput() {
    return array(
      array(
        array('foo' => 'foo'),
        new Definition(array(new Argument('name'))),
        'InvalidArgumentException',
        'Argument "foo" does not exist.'
      ),
      array(
        array('--foo' => null),
        new Definition(array(new Option('foo', 'f', Option::VALUE_REQUIRED))),
        'InvalidOptionException',
        'Option "--foo" requires a value.'
      ),
      array(
        array('--foo' => 'foo'),
        new Definition,
        'InvalidOptionException',
        'Option "--foo" does not exist.'
      ),
      array(
        array('-o' => 'foo'),
        new Definition,
        'InvalidOptionException',
        'Option "-o" does not exist.'
      )
    );
  }

  /**
   * Test __toString().
   *
   * @since 1.0.0
   * @access public
   */
  public function testToString() {
    $input = new ArrayInput(array('-f' => null, '-b' => 'bar', '--foo' => 'b a z', '--lala' => null, 'test' => 'Foo', 'test2' => "A\nB'C"));
    $this->assertEquals(
      '-f -b=bar --foo='.escapeshellarg('b a z').' --lala Foo '.escapeshellarg("A\nB'C"),
      (string) $input
    );
  }
}
