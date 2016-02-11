<?php
/**
 * Freyja CLI Output Formatter Style Test.
 *
 * @package Freyja\CLI\Tests\Formatters\Styles
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Formatters\Styles;

use Freyja\CLI\Formatters\Styles\OutputFormatter as Style;

/**
 * Test OutputFormatter Style class.
 *
 * @package Freyja\CLI\Tests\Formatters\Styles
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class OutputFormatterTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $style = new Style('green', 'black', array('bold', 'underscore'));
    $this->assertEquals("\033[32;40;1;4mfoo\033[39;49;22;24m", $style->apply('foo'));

    $style = new Style('red', null, array('blink'));
    $this->assertEquals("\033[31;5mfoo\033[39;25m", $style->apply('foo'));

    $style = new Style(null, 'white');
    $this->assertEquals("\033[47mfoo\033[49m", $style->apply('foo'));
  }

  /**
   * Test setForeground().
   *
   * @since 1.0.0
   * @access public
   */
  public function testForeground() {
    $style = new Style();

    $style->setForeground('black');
    $this->assertEquals("\033[30mfoo\033[39m", $style->apply('foo'));

    $style->setForeground('blue');
    $this->assertEquals("\033[34mfoo\033[39m", $style->apply('foo'));

    $style->setForeground('default');
    $this->assertEquals("\033[39mfoo\033[39m", $style->apply('foo'));

    $this->setExpectedException('InvalidArgumentException');
    $style->setForeground('undefined-color');
  }

  /**
   * Test setBackground().
   *
   * @since 1.0.0
   * @access public
   */
  public function testBackground() {
    $style = new Style();

    $style->setBackground('black');
    $this->assertEquals("\033[40mfoo\033[49m", $style->apply('foo'));

    $style->setBackground('yellow');
    $this->assertEquals("\033[43mfoo\033[49m", $style->apply('foo'));

    $style->setBackground('default');
    $this->assertEquals("\033[49mfoo\033[49m", $style->apply('foo'));

    $this->setExpectedException('InvalidArgumentException');
    $style->setBackground('undefined-color');
  }

  /**
   * Test setOptions().
   *
   * @since 1.0.0
   * @access public
   */
  public function testOptions() {
    $style = new Style;

    $style->setOptions(array('reverse', 'conceal'));
    $this->assertEquals("\033[7;8mfoo\033[27;28m", $style->apply('foo'));

    $style->setOption('bold');
    $this->assertEquals("\033[7;8;1mfoo\033[27;28;22m", $style->apply('foo'));

    $style->unsetOption('reverse');
    $this->assertEquals("\033[8;1mfoo\033[28;22m", $style->apply('foo'));

    $style->setOption('bold');
    $this->assertEquals("\033[8;1mfoo\033[28;22m", $style->apply('foo'));

    $style->setOptions(array('bold'));
    $this->assertEquals("\033[1mfoo\033[22m", $style->apply('foo'));

    try {
      $style->setOption('foo');
      $this->fail('->setOption() throws an \InvalidArgumentException when the option does not exist in the available options');
    } catch (\Exception $e) {
      $this->assertInstanceOf('\InvalidArgumentException', $e, '->setOption() throws an \InvalidArgumentException when the option does not exist in the available options');
      $this->assertContains('Invalid option specified: "foo"', $e->getMessage(), '->setOption() throws an \InvalidArgumentException when the option does not exist in the available options');
    }

    try {
      $style->unsetOption('foo');
      $this->fail('->unsetOption() throws an \InvalidArgumentException when the option does not exist in the available options');
    } catch (\Exception $e) {
      $this->assertInstanceOf('\InvalidArgumentException', $e, '->unsetOption() throws an \InvalidArgumentException when the option does not exist in the available options');
      $this->assertContains('Invalid option specified: "foo"', $e->getMessage(), '->unsetOption() throws an \InvalidArgumentException when the option does not exist in the available options');
    }
  }
}
