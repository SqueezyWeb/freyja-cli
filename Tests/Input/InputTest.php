<?php
/**
 * Freyja CLI Input Test.
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
 * Input Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class InputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $input = new ArrayInput(
      array('name' => 'foo'),
      new Definition(array(
        new Argument('name')
      ))
    );

    $this->assertEquals('foo', $input->getArgument('name'), '->__construct() takes a Definition as an argument');
  }

  /**
   * Test options.
   *
   * @since 1.0.0
   * @access public
   */
  public function testOptions() {
    $input = new ArrayInput(
      array('--name' => 'foo'),
      new Definition(array(
        new Option('name')
      ))
    );
    $this->assertEquals(
      'foo',
      $input->getOption('name'),
      '->getOption() returns the value for the given option'
    );

    $input->setOption('name', 'bar');
    $this->assertEquals(
      'bar',
      $input->getOption('name'),
      '->setOption() sets the value for a given option'
    );
    $this->assertEquals(
      array('name' => 'bar'),
      $input->getOptions(),
      '->getOptions() returns all option values'
    );

    $input = new ArrayInput(
      array('--name' => 'foo'),
      new Definition(array(
        new Option('name'),
        new Option('bar', '', Option::VALUE_OPTIONAL, '', 'default')
      ))
    );
    $this->assertEquals(
      'default',
      $input->getOption('bar'),
      '->getOption() returns the default value for optional options'
    );
    $this->assertEquals(
      array('name' => 'foo', 'bar' => 'default'),
      $input->getOptions(),
      '->getOptions() returns all option values, even optional ones'
    );
  }

  /**
   * Test set invalid option.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException \Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "--foo" does not exist.
   */
  public function testSetInvalidOption() {
    $input = new ArrayInput(
      array('--name', 'foo'),
      new Definition(array(
        new Option('name'),
        new Option('bar', '', Option::VALUE_OPTIONAL, '', 'default')
      ))
    );
    $input->setOption('foo', 'bar');
  }

  /**
   * Test get invalid option.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException \Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "--foo" does not exist.
   */
  public function testGetInvalidOption() {
    $input = new ArrayInput(
      array('--name', 'foo'),
      new Definition(array(
        new Option('name'),
        new Option('bar', '', Option::VALUE_OPTIONAL, '', 'default')
      ))
    );
    $input->getOption('foo');
  }

  /**
   * Test Arguments.
   *
   * @since 1.0.0
   * @access public
   */
  public function testArguments() {
    $input = new ArrayInput(
      array('name' => 'foo'),
      new Definition(array(
        new Argument('name')
      ))
    );
    $this->assertEquals(
      'foo',
      $input->getArgument('name'),
      '->getArgument() returns the value for the given argument'
    );

    $input->setArgument('name', 'bar');
    $this->assertEquals(
      'bar',
      $input->getArgument('name'),
      '->setArgument()  sets the value for a given argument'
    );
    $this->assertEquals(
      array('name' => 'bar'),
      $input->getArguments(),
      '->getArguments() returns all argument values'
    );

    $input = new ArrayInput(
      array('name' => 'foo'),
      new Definition(array(
        new Argument('name'),
        new Argument('bar', Argument::OPTIONAL, '', 'default')
      ))
    );
    $input->assertEquals(
      'default',
      $input->getArgument('bar'),
      '->getArgument() returns the default value for optional arguments'
    );
    $this->assertEquals(
      array('name' => 'foo', 'bar' => 'default'),
      $input->getArguments(),
      '->getArguments() returns all argument values, even optional ones'
    );
  }

  /**
   * Test set invalid argument.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException \Freyja\CLI\Exceptions\InvalidArgumentException
   * @expectedExceptionMessage Argument "foo" does not exist.
   */
  public function testSetInvalidArgument() {
    $input = new ArrayInput(
      array('name' => 'foo'),
      new Definition(array(
        new Argument('name'),
        new Argument('bar', Argument::OPTIONAL, '', 'default')
      ))
    );
    $input->setArgument('foo', 'bar');
  }

  /**
   * Test get invalid argument.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidArgumentException
   * @expectedExceptionMessage Argument "foo" does not exist.
   */
  public function testGetInvalidArgument() {
    $input = new ArrayInput(
      array('name' => 'foo'),
      new Definition(array(
        new Argument('name'),
        new Argument('bar', Argument::OPTIONAL, '', 'default')
      ))
    );
    $input->getArgument('foo');
  }
}
