<?php
/**
 * Freyja CLI Input Argument Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Input;

use Freyja\CLI\Input\Argument;

/**
 * Test Input Argument.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ArgumentTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $argument = new Argument('foo');
    $this->assertEquals(
      'foo',
      $argument->getName(),
      '__construct() takes a name as its first argument'
    );
  }

  /**
   * Test Argument Modes.
   *
   * @since 1.0.0
   * @access public
   */
  public function testModes() {
    $argument = new Argument('foo');
    $this->assertFalse($argument->isRequired(), '__construct() gives a "Argument::OPTIONAL" mode by default');

    $argument = new Argument('foo', null);
    $this->assertFalse($argument->isRequired(), '__construct() gives a "Argument::OPTIONAL" mode when null is passed');

    $argument = new Argument('foo', Argument::OPTIONAL);
    $this->assertFalse($argument->isRequired(), '__construct() can take "Argument::OPTIONAL" as its mode');

    $argument = new Argument('foo', Argument::REQUIRED);
    $this->assertTrue($argument->isRequired(), '__construct() can take "Argument::REQUIRED" as its mode');
  }

  /**
   * Test invalid argument modes.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $mode Argument mode.
   *
   * @dataProvider provideInvalidModes
   */
  public function testInvalidModes($mode) {
    $this->setExpectedException('Freyja\CLI\Exceptions\InvalidArgumentException', sprintf('Argument mode "%s" is not valid.', $mode));

    new Argument('foo', $mode);
  }

  /**
   * Provide invalid modes.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Array of invalid modes.
   */
  public function provideInvalidModes() {
    return array(
      array('ANOTHER_ONE'),
      array(-1)
    );
  }

  /**
   * Test Array Argument.
   *
   * @since 1.0.0
   * @access public
   */
  public function testIsArray() {
    $argument = new Argument('foo', Argument::IS_ARRAY);
    $this->assertTrue($argument->isArray(), 'isArray() returns true if the argument can be an array');

    $argument = new Argument('foo', Argument::OPTIONAL | Argument::IS_ARRAY);
    $this->assertTrue($argument->isArray(), 'isArray() returns true if the argument can be an array');

    $argument = new Argument('foo', Argument::OPTIONAL);
    $this->assertFalse($argument->isArray(), 'isArray() returns false if the argument can not be an array');
  }

  /**
   * Test getDescription() method.
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetDescription() {
    $argument = new Argument('foo', null, 'Some description');
    $this->assertEquals('Some description', $argument->getDescription(), 'getDescription() returns the description message');
  }

  /**
   * Test getDefault() method.
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetDefault() {
    $argument = new Argument('foo', Argument::OPTIONAL, '', 'default');
    $this->assertEquals('default', $argument->getDefault(), 'getDefault() returns the default value');
  }

  /**
   * Test setDefault() method.
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetDefault() {
    $argument = new Argument('foo', Argument::OPTIONAL, '', 'default');

    $argument->setDefault(null);
    $this->assertNull($argument->getDefault(), 'setDefault() can reset the default value by passing null');

    $argument->setDefault('another');
    $this->assertEquals('another', $argument->getDefault(), 'setDefault() changes the default value');

    $argument = new Argument('foo', Argument::OPTIONAL | Argument::IS_ARRAY);
    $argument->setDefault(array(1,2));
    $this->assertEquals(
      array(1,2),
      $argument->getDefault(),
      'setDefault() changes the default value when it is an array'
    );
  }

  /**
   * Test setDefault() with required argument.
   *
   * setDefault() should raise an exception when argument is required.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage Cannot set a default value for Argument::REQUIRED mode.
   */
  public function testSetDefaultWithRequiredArgument() {
    $argument = new Argument('foo', Argument::REQUIRED);
    $argument->setDefault('default');
  }

  /**
   * Test setDefault() with array argument.
   *
   * When argument expects an array argument and a single value is given, it
   * should raise an exception.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage A default value for an array argument must be an array.
   */
  public function testSetDefaultWithArrayArgument() {
    $argument = new Argument('foo', Argument::IS_ARRAY);
    $argument->setDefault('default');
  }
}
