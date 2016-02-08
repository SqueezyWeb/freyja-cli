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
}
