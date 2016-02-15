<?php
/**
 * Freyja CLI HelperSet Test.
 *
 * @package Freyja\CLI\Tests\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Helpers;

use Freyja\CLI\Helpers\HelperSet;
use Freyja\CLI\Tests\Fixtures\TestCommand as Command;

/**
 * Test HelperSet class.
 *
 * @package Freyja\CLI\Tests\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class HelperSetTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $mock_helper = $this->getGenericMockHelper('fake_helper');
    $helperset = new HelperSet(array('fake_helper_alias' => $mock_helper));

    $this->assertEquals($mock_helper, $helperset->get('fake_helper'), '__construct() sets given helper to helpers');
    $this->assertTrue($helperset->has('fake_helper_alias'), '__construct() sets helper alias for given helper');
  }

  /**
   * Test set().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSet() {
    $helperset = new HelperSet();
    $helperset->set($this->getGenericMockHelper('fake_helper', $helperset));
    $this->assertTrue($helperset->has('fake_helper'), 'set() adds helper to helpers');

    $helperset = new HelperSet();
    $helperset->set($this->getGenericMockHelper('fake_helper_01', $helperset));
    $helperset->set($this->getGenericMockHelper('fake_helper_02', $helperset));
    $this->assertTrue($helperset->has('fake_helper_01'), 'set() will set multiple helpers on consecutive calls');
    $this->assertTrue($helperset->has('fake_helper_02'), 'set() will set multiple helpers on consecutive calls');

    $helperset = new HelperSet();
    $helperset->set($this->getGenericMockHelper('fake_helper', $helperset), 'fake_helper_alias');
    $this->assertTrue($helperset->has('fake_helper'), 'set() adds helper alias when set');
    $this->assertTrue($helperset->has('fake_helper_alias'), 'set() adds helper alias when set');
  }

  /**
   * Test has().
   *
   * @since 1.0.0
   * @access public
   */
  public function testHas() {
    $helperset = new HelperSet(array('fake_helper_alias' => $this->getGenericMockHelper('fake_helper')));
    $this->assertTrue($helperset->has('fake_helper'), 'has() finds set helper');
    $this->assertTrue($helperset->has('fake_helper_alias'), 'has() finds set helper by alias');
  }

  /**
   * Test get().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGet() {
    $helper_01 = $this->getGenericMockHelper('fake_helper_01');
    $helper_02 = $this->getGenericMockHelper('fake_helper_02');
    $helperset = new HelperSet(array('fake_helper_01_alias' => $helper_01, 'fake_helper_02_alias' => $helper_02));
    $this->assertEquals($helper_01, $helperset->get('fake_helper_01'), 'get() returns correct helper by name');
    $this->assertEquals($helper_01, $helperset->get('fake_helper_01_alias'), 'get() returns correct helper by alias');
    $this->assertEquals($helper_02, $helperset->get('fake_helper_02'), 'get() returns correct helper by name');
    $this->assertEquals($helper_02, $helperset->get('fake_helper_02_alias'), 'get() returns correct helper by alias');

    $helperset = new HelperSet;
    try {
      $helperset->get('foo');
      $this->fail('get() throws InvalidArgumentException when helper not found');
    } catch (\Exception $e) {
      $this->assertInstanceOf('\InvalidArgumentException', $e, 'get() throws InvalidArgumentException when helper not found');
      $this->assertInstanceOf('\Freyja\Exceptions\ExceptionInterface', $e, 'get() throws domain specific exception when helper not found');
      $this->assertContains('Helper "foo" is not defined.', $e->getMessage(), 'get() throws InvalidArgumentException when helper not found');
    }
  }

  /**
   * Test setCommand().
   *
   * @since 1.0.0
   * @access public
   */
  public function testSetCommand() {
    $cmd_01 = new Command('foo');
    $cmd_02 = new Command('bar');

    $helperset = new HelperSet;
    $helperset->setCommand($cmd_01);
    $this->assertEquals($cmd_01, $helperset->getCommand(), 'setCommand() stores given command');

    $helperset = new HelperSet;
    $helperset->setCommand($cmd_01);
    $helperset->setCommand($cmd_02);
    $this->assertEquals($cmd_02, $helperset->getCommand(), 'setCommand() overwrites command with consecutive calls');
  }

  /**
   * Test getCommand().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetCommand() {
    $cmd = new Command('foo');
    $helperset = new HelperSet;
    $helperset->setCommand($cmd);
    $this->assertEquals($cmd, $helperset->getCommand(), 'getCommand() retrieves stored command');
  }

  /**
   * Test iteration.
   *
   * @since 1.0.0
   * @access public
   */
  public function testIteration() {
    $helperset = new HelperSet;
    $helperset->set($this->getGenericMockHelper('fake_helper_01', $helperset));
    $helperset->set($this->getGenericMockHelper('fake_helper_02', $helperset));

    $helpers = array('fake_helper_01', 'fake_helper_02');
    $i = 0;

    foreach ($helperset as $helper)
      $this->assertEquals($helpers[$i++], $helper->getName());
  }

  /**
   * Create generic mock for the helper interface.
   *
   * Optionally check for a call to setHelperSet() with a specific HelperSet
   * instance.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $name
   * @param HelperSet $helperset Optional. Allow a mock to verify a particular
   * helperset is being added to the Helper. Default null.
   */
  private function getGenericMockHelper($name, HelperSet $helperset = null) {
    $mock_helper = $this->getMock('\Freyja\CLI\Helpers\HelperInterface');
    $mock_helper->expects($this->any())
      ->method('getName')
      ->will($this->returnValue($name));

    if ($helperset)
      $mock_helper->expects($this->any())
        ->method('setHelperSet')
        ->with($this->equalTo($helperset));

    return $mock_helper;
  }
}
