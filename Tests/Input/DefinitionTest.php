<?php
/**
 * Freyja CLI Input Definition test.
 *
 * @package Freyja\CLI\Tests\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Input;

use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;

/**
 * Input Definition Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase {
  // protected static $fixtures;

  protected $foo, $bar, $foo1, $foo2;

  // public static function setUpBeforeClass() {
  //   self::$fixtures = __DIR__.'/../Fixtures';
  // }

  /**
   * Test constructor with arguments.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructorArguments() {
    $this->initializeArguments();

    $definition = new Definition;
    $this->assertEquals(array(), $definition->getArguments(), '__construct() creates a new Definition object');

    $definition = new Definition(array($this->foo, $this->bar));
    $this->assertEquals(
      array('foo' => $this->foo, 'bar' => $this->bar),
      $definition->getArguments(),
      '__construct() takes an array of Argument objects as its first argument'
    );
  }

  /**
   * Test constructor with options.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructorOptions() {
    $this->initializeOptions();

    $definition = new Definition;
    $this->assertEquals(array(), $definition->getOptions(), '__construct() creates a new Definition object');

    $definition = new Definition(array($this->foo, $this->bar));
    $this->assertEquals(
      array('foo' => $this->foo, 'bar' => $this->bar),
      $definition->getOptions(),
      '__construct() takes an array of Option objects as its first argument'
    );
  }

  /**
   * Test setArguments().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetArguments() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->setArguments(array($this->foo));
    $this->assertEquals(array('foo' => $this->foo), $definition->getArguments(), 'setArguments() sets the array of Argument objects');
    $definition->setArguments(array($this->bar));

    $this->assertEquals(array('bar' => $this->bar), $definition->getArguments(), 'setArguments() clears all Argument objects');
  }

  /**
   * Test addArguments().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddArguments() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArguments(array($this->foo));
    $this->assertEquals(array('foo' => $this->foo), $definition->getArguments(), 'addArguments() adds an array of Argument objects');
    $definition->addArguments(array($this->bar));
    $this->assertEquals(array('foo' => $this->foo, 'bar' => $this->bar), $definition->getArguments(), 'addArguments() does not clear existing Argument objects');
  }

  /**
   * Test addArgument().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddArgument() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArgument($this->foo);
    $this->assertEquals(array('foo' => $this->foo), $definition->getArguments(), 'addArgument() adds a Argument object');
    $definition->addArgument($this->bar);
    $this->assertEquals(array('foo' => $this->foo, 'bar' => $this->bar), $definition->getArguments(), 'addArgument() adds a Argument object');
  }

  /**
   * Test addArgument() with same argument names.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage An argument with name "foo" already exists.
   */
  public function testArgumentsMustHaveDifferentNames() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArgument($this->foo);
    $definition->addArgument($this->foo1);
  }

  /**
   * Test addArgument() with array not in last position.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage Cannot add an argument after an array argument.
   */
  public function testArrayArgumentHasToBeLast() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArgument(new Argument('fooarray', Argument::IS_ARRAY));
    $definition->addArgument(new Argument('anotherbar'));
  }

  /**
   * Test addArguments() with required argument after optional one.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage Cannot add a required argument after an optional one.
   */
  public function testRequiredArgumentCannotFollowAnOptionalOne() {
    $this->initializeArguments();

    $definition = new Definition;
    $definition->addArgument($this->foo);
    $definition->addArgument($this->foo2);
  }

  /**
   * Test getArgument().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetArgument() {
    $this->initializeArguments();

    $definition = new Definition;
    $definition->addArguments(array($this->foo));
    $this->assertEquals($this->foo, $definition->getArgument('foo'), 'getArgument() returns an Argument by its name');
  }

  /**
   * Test getArgument() with invalid argument.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidArgumentException
   * @expectedExceptionMessage Argument "bar" does not exist.
   */
  public function testGetInvalidArgument() {
    $this->initializeArguments();

    $definition = new Definition;
    $definition->addArguments(array($this->foo));
    $definition->getArgument('bar');
  }

  /**
   * Test hasArgument().
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasArgument() {
    $this->initializeArguments();

    $definition = new Definition;
    $definition->addArguments(array($this->foo));

    $this->assertTrue($definition->hasArgument('foo'), 'hasArgument() returns true if an Argument exists for the given name');
    $this->assertFalse($definition->hasArgument('bar'), 'hasArgument() returns false if an Argument exists for the given name');
  }

  /**
   * Test getArgumentRequiredCount().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetArgumentRequiredCount() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArgument($this->foo2);
    $this->assertEquals(1, $definition->getArgumentRequiredCount(), 'getArgumentRequiredCount() returns the number of required arguments');
    $definition->addArgument($this->foo);
    $this->assertEquals(1, $definition->getArgumentRequiredCount(), 'getArgumentRequiredCount() returns the number of required arguments');
  }

  /**
   * Test getArgumentCount().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetArgumentCount() {
    $this->initializeArguments();

    $definition = new Definition();
    $definition->addArgument($this->foo2);
    $this->assertEquals(1, $definition->getArgumentCount(), 'getArgumentCount() returns the number of arguments');
    $definition->addArgument($this->foo);
    $this->assertEquals(2, $definition->getArgumentCount(), 'getArgumentCount() returns the number of arguments');
  }

  /**
   * Test getArgumentDefaults().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetArgumentDefaults() {
    $definition = new Definition(array(
      new Argument('foo1', Argument::OPTIONAL),
      new Argument('foo2', Argument::OPTIONAL, '', 'default'),
      new Argument('foo3', Argument::OPTIONAL | Argument::IS_ARRAY),
    //  new Argument('foo4', Argument::OPTIONAL | Argument::IS_ARRAY, '', array(1, 2)),
    ));
    $this->assertEquals(array('foo1' => null, 'foo2' => 'default', 'foo3' => array()), $definition->getArgumentDefaults(), 'getArgumentDefaults() return the default values for each argument');

    $definition = new Definition(array(
        new Argument('foo4', Argument::OPTIONAL | Argument::IS_ARRAY, '', array(1, 2)),
    ));
    $this->assertEquals(array('foo4' => array(1, 2)), $definition->getArgumentDefaults(), 'getArgumentDefaults() return the default values for each argument');
  }

  /**
   * Test setOptions().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetOptions() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertEquals(array('foo' => $this->foo), $definition->getOptions(), 'setOptions() sets the array of Option objects');
    $definition->setOptions(array($this->bar));
    $this->assertEquals(array('bar' => $this->bar), $definition->getOptions(), 'setOptions() clears all Option objects');
  }

  /**
   * Test that setOptions() clears options.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "-f" does not exist.
   */
  public function testSetOptionsClearsOptions() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $definition->setOptions(array($this->bar));
    $definition->getOptionByShortcut('f');
  }

  /**
   * Test addOptions().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddOptions() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertEquals(array('foo' => $this->foo), $definition->getOptions(), 'addOptions() adds an array of Option objects');
    $definition->addOptions(array($this->bar));
    $this->assertEquals(array('foo' => $this->foo, 'bar' => $this->bar), $definition->getOptions(), 'addOptions() does not clear existing Option objects');
  }

  /**
   * Test addOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddOption() {
    $this->initializeOptions();

    $definition = new Definition;
    $definition->addOption($this->foo);
    $this->assertEquals(array('foo' => $this->foo), $definition->getOptions(), 'addOption() adds a Option object');
    $definition->addOption($this->bar);
    $this->assertEquals(array('foo' => $this->foo, 'bar' => $this->bar), $definition->getOptions(), 'addOption() adds a Option object');
  }

  /**
   * Test addOption() with duplicate option.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage An option named "foo" already exists.
   */
  public function testAddDuplicateOption() {
    $this->initializeOptions();

    $definition = new Definition;
    $definition->addOption($this->foo);
    $definition->addOption($this->foo2);
  }

  /**
   * Test addOption() with duplicate shortcut.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\Exceptions\LogicException
   * @expectedExceptionMessage An option with shortcut "f" already exists.
   */
  public function testAddDuplicateShortcutOption() {
    $this->initializeOptions();

    $definition = new Definition;
    $definition->addOption($this->foo);
    $definition->addOption($this->foo1);
  }

  /**
   * Test getOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetOption() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertEquals($this->foo, $definition->getOption('foo'), 'getOption()  returns an Option by its name');
  }

  /**
   * Test getOption() with invalid option.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "--bar" does not exist.
   */
  public function testGetInvalidOption() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $definition->getOption('bar');
  }

  /**
   * Test hasOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasOption() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertTrue($definition->hasOption('foo'), 'hasOption() returns true if a Option exists for the given name');
    $this->assertFalse($definition->hasOption('bar'), 'hasOption() returns false if a Option exists for the given name');
  }

  /**
   * Test hasShortcut().
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasShortcut() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertTrue($definition->hasShortcut('f'), 'hasShortcut() returns true if a Option exists for the given shortcut');
    $this->assertFalse($definition->hasShortcut('b'), 'hasShortcut() returns false if a Option exists for the given shortcut');
  }

  /**
   * Test getOptionByShortcut().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetOptionByShortcut() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $this->assertEquals($this->foo, $definition->getOptionByShortcut('f'), 'getOptionByShortcut() returns an Option by its shortcut');
  }

  /**
   * Test multiple getOptionByShortcut().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetOptionByMultiShortcut() {
    $this->initializeOptions();

    $definition = new Definition(array($this->multi));
    $this->assertEquals($this->multi, $definition->getOptionByShortcut('m'), 'getOptionByShortcut() returns an Option by its shortcut');
    $this->assertEquals($this->multi, $definition->getOptionByShortcut('mmm'), 'getOptionByShortcut() returns an Option by its shortcut');
  }

  /**
   * Test getOptionByShortcut() with invalid shortcut.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "-l" does not exist.
   */
  public function testGetOptionByInvalidShortcut() {
    $this->initializeOptions();

    $definition = new Definition(array($this->foo));
    $definition->getOptionByShortcut('l');
  }

  /**
   * Test getOptionDefaults().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetOptionDefaults() {
    $definition = new Definition(array(
      new Option('foo1', null, Option::VALUE_NONE),
      new Option('foo2', null, Option::VALUE_REQUIRED),
      new Option('foo3', null, Option::VALUE_REQUIRED, '', 'default'),
      new Option('foo4', null, Option::VALUE_OPTIONAL),
      new Option('foo5', null, Option::VALUE_OPTIONAL, '', 'default'),
      new Option('foo6', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY),
      new Option('foo7', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY, '', array(1, 2)),
    ));
    $defaults = array(
      'foo1' => false,
      'foo2' => null,
      'foo3' => 'default',
      'foo4' => null,
      'foo5' => 'default',
      'foo6' => array(),
      'foo7' => array(1, 2),
    );
    $this->assertSame($defaults, $definition->getOptionDefaults(), 'getOptionDefaults() returns the default values for all options');
  }

  /**
   * Test getSynopsis().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider getGetSynopsisData
   */
  public function testGetSynopsis(Definition $definition, $expected_synopsis, $message = null) {
    $this->assertEquals($expected_synopsis, $definition->getSynopsis(), $message ? 'getSynopsis() '.$message : '');
  }

  /**
   * Provide data for getSynopsis().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getGetSynopsisData() {
    return array(
      array(new Definition(array(new Option('foo'))), '[--foo]', 'puts optional options in square brackets'),
      array(new Definition(array(new Option('foo', 'f'))), '[-f|--foo]', 'separates shortcut with a pipe'),
      array(new Definition(array(new Option('foo', 'f', Option::VALUE_REQUIRED))), '[-f|--foo FOO]', 'uses shortcut as value placeholder'),
      array(new Definition(array(new Option('foo', 'f', Option::VALUE_OPTIONAL))), '[-f|--foo [FOO]]', 'puts optional values in square brackets'),

      array(new Definition(array(new Argument('foo', Argument::REQUIRED))), '<foo>', 'puts arguments in angle brackets'),
      array(new Definition(array(new Argument('foo'))), '[<foo>]', 'puts optional arguments in square brackets'),
      array(new Definition(array(new Argument('foo', Argument::IS_ARRAY))), '[<foo>]...', 'uses an ellipsis for array arguments'),
      array(new Definition(array(new Argument('foo', Argument::REQUIRED | Argument::IS_ARRAY))), '<foo> (<foo>)...', 'uses parenthesis and ellipsis for required array arguments'),

      array(new Definition(array(new Option('foo'), new Argument('foo', Argument::REQUIRED))), '[--foo] [--] <foo>', 'puts [--] between options and arguments'),
    );
  }

  /**
   * Test getSynopsis() with short flag.
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetShortSynopsis() {
    $definition = new Definition(array(new Option('foo'), new Option('bar'), new Argument('cat')));
    $this->assertEquals('[options] [--] [<cat>]', $definition->getSynopsis(true), 'getSynopsis(true) groups options in [options]');
  }

  /**
   * Initialize arguments.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function initializeArguments() {
    $this->foo = new Argument('foo');
    $this->bar = new Argument('bar');
    $this->foo1 = new Argument('foo');
    $this->foo2 = new Argument('foo2', Argument::REQUIRED);
  }

  /**
   * Initialize options.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function initializeOptions() {
    $this->foo = new Option('foo', 'f');
    $this->bar = new Option('bar', 'b');
    $this->foo1 = new Option('fooBis', 'f');
    $this->foo2 = new Option('foo', 'p');
    $this->multi = new Option('multi', 'm|mm|mmm');
  }
}
