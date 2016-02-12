<?php
/**
 * Freyja CLI Command Test.
 *
 * @package Freyja\CLI\Tests\Commands
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Commands;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Tests\Fixtures\TestCommand;
use Freyja\CLI\Helpers\FormatterHelper;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Input\ArrayInput;
use Freyna\CLI\Output\OutputInterface;
use Freyja\CLI\Output\NullOutput;
use Freyja\CLI\Testers\CommandTester;

/**
 * Test Command class.
 *
 * @package Freyja\CLI\Tests\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class CommandTest extends \PHPUnit_Framework_TestCase {
  protected static $fixtures;

  public static function setUpBeforeClass() {
    self::$fixtures = __DIR__.'/../Fixtures/';
  }

  public function testConstructor() {
    $command = new TestCommand();
    $this->assertEquals('namespace:name', $command->getName(), '__construct() sets the command name');
  }

  /**
   * Test getDefinition() and setDefinition().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetSetDefinition() {
    $command = new TestCommand;
    $ret = $command->setDefinition($definition = new Definition);
    $this->assertEquals($command, $ret, 'setDefinition() implements a fluent interface');
    $this->assertEquals($definition, $command->getDefinition(), 'setDefinition() sets the current Definition instance');

    $command->setDefinition(array(new Argument('foo'), new Option('bar')));
    $this->assertTrue($command->getDefinition()->hasArgument('foo'), 'setDefinition() also takes an array of Arguments and Options as an argument');
    $this->assertTrue($command->getDefinition()->hasOption('bar'), 'setDefinition() also takes an array of Arguments and Options as an argument');
    $command->setDefinition(new Definition);
  }

  /**
   * Test addArgument().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddArgument() {
    $command = new TestCommand;
    $ret = $command->addArgument('foo');
    $this->assertEquals($command, $ret, 'addArgument() implements a fluent interface');
    $this->assertTrue($command->getDefinition()->hasArgument('foo'), 'addArgument() adds an argument to the command');
  }

  /**
   * Test addOption().
   *
   * @since 1.0.0
   * @access public
   */
  public function testAddOption() {
    $command = new TestCommand;
    $ret = $command->addOption('foo');
    $this->assertEquals($command, $ret, 'addOption() implements a fluent interface');
    $this->assertTrue($command->getDefinition()->hasOption('foo'), 'addOption() adds an option to the command');
  }

  /**
   * Test getNamespace(), getName(), and setName().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetNamespaceGetNameSetName() {
    $command = new TestCommand;
    $this->assertEquals('namespace:name', $command->getName(), 'getName() returns the command name');

    $command->setName('foo');
    $this->assertEquals('foo', $command->getName(), 'setName() sets the command name');

    $ret = $command->setName('foobar:bar');
    $this->assertEquals($command, $ret, 'setName() implements a fluent interface');
    $this->assertEquals('foobar:bar', $command->getName(), 'setName() sets the command name');
  }

  /**
   * Test invalid command names.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideInvalidCommandNames
   */
  public function testInvalidCommandNames($name) {
    $this->setExpectedException('Freyja\Exceptions\InvalidArgumentException', sprintf('Command name "%s" is invalid.', $name));

    $command = new TestCommand;
    $command->setName($name);
  }

  /**
   * Provide invalid command names.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideInvalidCommandNames() {
    return array(
      array(''),
      array('foo:')
    );
  }

  /**
   * Test getDescription() and setDescription().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetSetDescription() {
    $command = new TestCommand;
    $this->assertEquals('description', $command->getDescription(), 'getDescription() returns the description');

    $ret = $command->setDescription('description1');
    $this->assertEquals($command, $ret, 'setDescription() implements a fluent interface');
    $this->assertEquals('description1', $command->getDescription(), 'setDescription() sets the description');
  }

  /**
   * Test getHelp() and setHelp().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetSetHelp() {
    $command = new TestCommand;
    $this->assertEquals('help', $command->getHelp(), 'getHelp() returns the help');

    $ret = $command->setHelp('help1');
    $this->assertEquals($command, $ret, 'setHelp() implements a fluent interface');
    $this->assertEquals('help1', $command->getHelp(), 'setHelp() sets the help');

    $command->setHelp('');
    $this->assertEquals('', $command->getHelp(), 'getHelp() does not fall back to the description when help is empty');
  }

  /**
   * Test getProcessedHelp().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetProcessedHelp() {
    $command = new TestCommand;
    $command->setHelp('The %command.name% command does... Example: php %command.full_name%.');
    $this->assertContains('The namespace:name command does...', $command->getProcessedHelp(), 'getProcessedHelp() replaces %command.name% correctly');
    $this->assertNotContains('%command.full_name%', $command->getProcessedHelp(), 'getProcessedHelp() replaces %command.full_name%');

    $command = new TestCommand;
    $command->setHelp('');
    $this->assertContains('description', $command->getProcessedHelp(), 'getProcessedHelp() falls back to the description');
  }

  /**
   * Test getAliases() and setAliases().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetSetAliases() {
    $command = new TestCommand;
    $this->assertEquals(array('name'), $command->getAliases(), 'getAliases() returns the aliases');

    $ret = $command->setAliases(array('name1'));
    $this->assertEquals($command, $ret, 'setAliases() implements a fluent interface');
    $this->assertEquals(array('name1'), $command->getAliases(), 'setAliases() sets the aliases');
  }

  /**
   * Test getSynopsis().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetSynopsis() {
    $command = new TestCommand;
    $command->addOption('foo');
    $command->addArgument('bar');
    $this->assertEquals('namespace:name [--foo] [--] [<bar>]', $command->getSynopsis(), 'getSynopsis() returns the synopsis');
  }

  /**
   * Test getHelper().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetHelper() {
    $this->markTestSkipped(
      'Helper support in Command is broken, and this test raises a Fatal Error. Re-enable this test when Helper support has been fixed.'
    );
    // $command = new TestCommand;
    // $formatter_helper = new FormatterHelper;
    // $this->assertEquals($formatter_helper->getName(), $command->getHelper('formatter')->getName(), 'getHelper() returns the correct helper');
  }

  /**
   * Test run() interactive.
   *
   * @since 1.0.0
   * @access public
   */
  public function testRunInteractive() {
    $tester = new CommandTester(new TestCommand);

    $tester->execute(array(), array('interactive' => true));

    $this->assertEquals('interact called'.PHP_EOL.'execute called'.PHP_EOL, $tester->getDisplay(), 'run() calls the interact() method if the input is interactive');
  }

  /**
   * Test run() non interactive.
   *
   * @since 1.0.0
   * @access public
   */
  public function testRunNonInteractive() {
    $tester = new CommandTester(new TestCommand);

    $tester->execute(array(), array('interactive' => false));

    $this->assertEquals('execute called'.PHP_EOL, $tester->getDisplay(), 'run() does not call the interact() method if the input is not interactive');
  }

  /**
   * Test run() with invalid option.
   *
   * @since 1.0.0
   * @access public
   *
   * @expectedException Freyja\CLI\Exceptions\InvalidOptionException
   * @expectedExceptionMessage Option "--bar" does not exist.
   */
  public function testRunWithInvalidOption() {
    $command = new TestCommand;
    $tester = new CommandTester($command);
    $tester->execute(array('--bar' => true));
  }

  /**
   * Test that run() returns integer exit code.
   *
   * @since 1.0.0
   * @access public
   */
  public function testRunReturnsIntegerExitCode() {
    $command = new TestCommand;
    $exit_code = $command->run(new ArrayInput(array()), new NullOutput);
    $this->assertSame(0, $exit_code, 'run() returns integer exit code (treats null as 0)');

    $command = $this->getMockForAbstractClass('Freyja\CLI\Commands\Command', array('execute'));
    $command->expects($this->once())
      ->method('execute')
      ->will($this->returnValue('2.3'));
    $exit_code = $command->run(new ArrayInput(array()), new NullOutput);
    $this->assertSame(2, $exit_code, 'run() returns integer exit code (casts numeric to int)');
  }

  /**
   * Test run() returns always integer.
   *
   * @since 1.0.0
   * @access public
   */
  public function testRunReturnsAlwaysInteger() {
    $command = new TestCommand;
    $this->assertSame(0, $command->run(new ArrayInput(array()), new NullOutput));
  }
}
