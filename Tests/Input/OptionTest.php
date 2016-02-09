<?php
/**
 * Freyja CLI Input Option Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Input;

use Freyja\CLI\Input\Option;
use Freyja\CLI\Exceptions\InvalidOptionException;

/**
 * Input Option test.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class OptionTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $option = new Option('foo');
    $this->assertEquals('foo', $option->getName(), '__construct() takes a name as its first argument');

    $option = new Option('--foo');
    $this->assertEquals('foo', $option->getName(), '__construct() removes the leading -- of the option name');
  }

  /**
   * Test array mode without value.
   *
   * @since 1.0.0
   * @access public
   */
  public function testArrayModeWithoutValue() {
    new Option('foo', 'f', Option::VALUE_IS_ARRAY);
  }

  /**
   * Test shortcut.
   *
   * @since 1.0.0
   * @access public
   */
  public function testShortcut() {
    $option = new Option('foo', 'f');
    $this->assertEquals('f', $option->getShortcut(), '__construct() can take a shortcut as its second argument');
    $option = new Option('foo', '-f|-ff|fff');
    $this->assertEquals('f|ff|fff', $option->getShortcut(), '__construct() removes the leading - of the shortcuts');
    $option = new Option('foo', array('f', 'ff', '-fff'));
    $this->assertEquals('f|ff|fff', $option->getShortcut(), '__construct() removes the leading - of the shortcuts');
    $option = new Option('foo');
    $this->assertNull($option->getShortcut(), '__construct() makes the shortcut null by default');
  }

  /**
   * Test modes.
   *
   * @since 1.0.0
   * @access public
   */
  public function testModes() {
    $option = new Option('foo', 'f');
    $this->assertFalse($option->acceptValue(), '__construct() gives a "Option::VALUE_NONE" mode by default');
    $this->assertFalse($option->isValueRequired(), '__construct() gives a "Option::VALUE_NONE" mode by default');
    $this->assertFalse($option->isValueOptional(), '__construct() gives a "Option::VALUE_NONE" mode by default');

    $option = new Option('foo', 'f', null);
    $this->assertFalse($option->acceptValue(), '__construct() can take "Option::VALUE_NONE" as its mode');
    $this->assertFalse($option->isValueRequired(), '__construct() can take "Option::VALUE_NONE" as its mode');
    $this->assertFalse($option->isValueOptional(), '__construct() can take "Option::VALUE_NONE" as its mode');

    $option = new Option('foo', 'f', Option::VALUE_NONE);
    $this->assertFalse($option->acceptValue(), '__construct() can take "Option::VALUE_NONE" as its mode');
    $this->assertFalse($option->isValueRequired(), '__construct() can take "Option::VALUE_NONE" as its mode');
    $this->assertFalse($option->isValueOptional(), '__construct() can take "Option::VALUE_NONE" as its mode');

    $option = new Option('foo', 'f', Option::VALUE_REQUIRED);
    $this->assertTrue($option->acceptValue(), '__construct() can take "Option::VALUE_REQUIRED" as its mode');
    $this->assertTrue($option->isValueRequired(), '__construct() can take "Option::VALUE_REQUIRED" as its mode');
    $this->assertFalse($option->isValueOptional(), '__construct() can take "Option::VALUE_REQUIRED" as its mode');

    $option = new Option('foo', 'f', Option::VALUE_OPTIONAL);
    $this->assertTrue($option->acceptValue(), '__construct() can take "Option::VALUE_OPTIONAL" as its mode');
    $this->assertFalse($option->isValueRequired(), '__construct() can take "Option::VALUE_OPTIONAL" as its mode');
    $this->assertTrue($option->isValueOptional(), '__construct() can take "Option::VALUE_OPTIONAL" as its mode');
  }

  /**
   * Test invalid modes.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideInvalidModes
   */
  public function testInvalidModes($mode) {
    $this->setExpectedException('Freyja\CLI\Exceptions\InvalidOptionException', sprintf('Option mode "%s" is not valid.', $mode));

    new Option('foo', 'f', $mode);
  }

  /**
   * Provide invalid modes.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideInvalidModes() {
    return array(
      array('ANOTHER_ONE'),
      array(-1)
    );
  }

  /**
   * Test that empty name is invalid.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   */
  public function testEmptyNameIsInvalid() {
    new Option('');
  }

  /**
   * Test that double dash name is invalid.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   */
  public function testDoubleDashNameIsInvalid() {
    new Option('--');
  }

  /**
   * Test that single dash name is invalid.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   */
  public function testSingleDashNameIsInvalid() {
    new Option('-');
  }

  /**
   * Test that single dash shortcut is invalid.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   */
  public function testSingleSashShortcutIsInvalid() {
    new Option('foo', '-');
  }

  /**
   * Test isArray().
   *
   * @since 1.0.0
   * @access public
   */
  public function testIsArray() {
    $option = new Option('foo', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY);
    $this->assertTrue($option->isArray(), 'isArray() returns true if the option can be an array');
    $option = new Option('foo', null, Option::VALUE_NONE);
    $this->assertFalse($option->isArray(), 'isArray() returns false if the option can not be an array');
  }

  /**
   * Test getDescription().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetDescription() {
    $option = new Option('foo', 'f', null, 'Some description');
    $this->assertEquals('Some description', $option->getDescription(), 'getDescription() returns the description message');
  }

  /**
   * Test getDefault().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetDefault() {
    $option = new Option('foo', null, Option::VALUE_OPTIONAL, '', 'default');
    $this->assertEquals('default', $option->getDefault(), 'getDefault() returns the default value');

    $option = new Option('foo', null, Option::VALUE_REQUIRED, '', 'default');
    $this->assertEquals('default', $option->getDefault(), 'getDefault() returns the default value');

    $option = new Option('foo', null, Option::VALUE_REQUIRED);
    $this->assertNull($option->getDefault(), 'getDefault() returns null if no default value is configured');

    $option = new Option('foo', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY);
    $this->assertEquals(array(), $option->getDefault(), 'getDefault() returns an empty array if option is an array');

    $option = new Option('foo', null, Option::VALUE_NONE);
    $this->assertFalse($option->getDefault(), 'getDefault() returns false if the option does not take a value');
  }

  /**
   * Test setDefault().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetDefault() {
    $option = new Option('foo', null, Option::VALUE_REQUIRED, '', 'default');
    $option->setDefault(null);
    $this->assertNull($option->getDefault(), 'setDefault() can reset the default value by passing null');
    $option->setDefault('another');
    $this->assertEquals('another', $option->getDefault(), 'setDefault() changes the default value');

    $option = new Option('foo', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY);
    $option->setDefault(array(1, 2));
    $this->assertEquals(array(1, 2), $option->getDefault(), 'setDefault() changes the default value');
  }

  /**
   * Test setDefault() with Option::VALUE_NONE mode.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage Cannot set a default value when using Option::VALUE_NONE mode.
   */
  public function testDefaultValueWithValueNoneMode() {
    $option = new Option('foo', 'f', Option::VALUE_NONE);
    $option->setDefault('default');
  }

  /**
   * Test setDefault() with single default value and Option::VALUE_IS_ARRAY mode.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage A default value for an array option must be an array.
   */
  public function testDefaultValueWithIsArrayMode() {
    $option = new Option('foo', 'f', Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY);
    $option->setDefault('default');
  }

  /**
   * Test equals.
   *
   * @since 1.0.0
   * @access public
   */
  public function testEquals() {
    $option = new Option('foo', 'f', null, 'Some description');
    $option2 = new Option('foo', 'f', null, 'Alternative description');
    $this->assertTrue($option->equals($option2));

    $option = new Option('foo', 'f', Option::VALUE_OPTIONAL, 'Some description');
    $option2 = new Option('foo', 'f', Option::VALUE_OPTIONAL, 'Some description', true);
    $this->assertFalse($option->equals($option2));

    $option = new Option('foo', 'f', null, 'Some description');
    $option2 = new Option('bar', 'f', null, 'Some description');
    $this->assertFalse($option->equals($option2));

    $option = new Option('foo', 'f', null, 'Some description');
    $option2 = new Option('foo', '', null, 'Some description');
    $this->assertFalse($option->equals($option2));

    $option = new Option('foo', 'f', null, 'Some description');
    $option2 = new Option('foo', 'f', Option::VALUE_OPTIONAL, 'Some description');
    $this->assertFalse($option->equals($option2));
  }
}
