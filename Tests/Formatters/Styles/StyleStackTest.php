<?php
/**
 * Freyja CLI Style Stack Test.
 *
 * @package Freyja\CLI\Tests\Formatters\Styles
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Formatters\Styles;

use Freyja\CLI\Formatters\Styles\StyleStack;
use Freyja\CLI\Formatters\Styles\Style;

/**
 * Test StyleStack class.
 *
 * @package Freyja\CLI\Tests\Formatters\Styles
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class StyleStackTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $stack = new StyleStack;
    $this->assertEquals(new Style, $stack->getEmptyStyle(), '__construct() creates a new Style instance if null is passed');

    $style = new Style('red');
    $stack = new StyleStack($style);
    $this->assertSame($style, $stack->getEmptyStyle(), '__construct() sets the style passed as empty style');
  }

  /**
   * Test push().
   *
   * @since 1.0.0
   * @access public
   */
  public function testPush() {
    $stack = new StyleStack;
    $r = new \ReflectionObject($stack);
    $p = $r->getProperty('styles');
    $p->setAccessible(true);

    $style = new Style('red');
    $stack->push($style);
    $stylestack = $p->getValue($stack);
    $this->assertTrue(1 === count($stylestack), 'push() pushes only one element in the empty array');
    $this->assertSame($style, $stylestack[0], 'push() pushes passed style in the empty array');

    $new = new Style('green');
    $stack->push($new);
    $stylestack = $p->getValue($stack);
    $new->setUnset(31, null);
    $this->assertTrue(2 === count($stylestack), 'push() adds the new element to the array');
    $this->assertSame($new, $stylestack[1], 'push() adds the new element at the end of the array');
    $this->assertEquals(array($style, $new), $stylestack, 'push() does not replace old array elements');
  }

  /**
   * Test getEmptyStyle().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetEmptyStyle() {
    $empty = new Style('magenta');
    $stack = new StyleStack($empty);
    $stack->push(new Style)->push(new Style('red'))->push(new Style('cyan'));
    $this->assertSame($empty, $stack->getEmptyStyle(), 'getEmptyStyle() returns the empty style');
  }

  /**
   * Test reset().
   *
   * @since 1.0.0
   * @access public
   */
  public function testReset() {
    $stack = new StyleStack;
    $stack->push(new Style);
    $r = new \ReflectionObject($stack);
    $p = $r->getProperty('styles');
    $p->setAccessible(true);

    $stack->reset();
    $this->assertEquals(array(), $p->getValue($stack), 'reset() empties styles array');
  }

  /**
   * Test pop().
   *
   * @since 1.0.0
   * @access public
   */
  public function testPop() {
    $stack = new StyleStack;
    $r = new \ReflectionObject($stack);
    $p = $r->getProperty('styles');
    $p->setAccessible(true);

    $styles = $p->getValue($stack);
    $this->assertTrue(empty($styles), 'StyleStack::$styles is initially empty');

    $pop = $stack->pop();
    $this->assertEquals(new Style, $pop, 'pop() returns StyleStack::$empty_style if StyleStack::$styles is empty');
    $styles = $p->getValue($stack);
    $this->assertTrue(empty($styles), 'StyleStack::$styles is still empty after pop()');

    $style = new Style('red');
    $stack->push($style);
    $styles = $p->getValue($stack);
    $this->assertTrue(1 === count($styles), 'StyleStack::$styles has exactly one element after push()');
    $pop = $stack->pop();
    $this->assertSame($style, $pop, 'pop() returns the last element in the stack');

    $green = new Style('green');
    $stack->push($style)->push($green);
    $styles = $p->getValue($stack);
    $this->assertTrue(2 === count($styles), 'StyleStack::$styles has exactly two elements after push()');
    $pop = $stack->pop();
    $this->assertSame($green, $pop, 'pop() returns the last element in the stack and its properties have been resetted');
    $pop = $stack->pop();
    $this->assertSame($style, $pop, 'pop() returns the only remaining element in the stack');
    $pop = $stack->pop();
    $this->assertEquals(new Style, $pop, 'pop() returns the $empty_style when $styles has been emptied');
  }

  /**
   * Test getCurrent().
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetCurrent() {
    $stack = new StyleStack;
    $this->assertEquals(new Style, $stack->getCurrent(), 'getCurrent() returns $empty_style when $styles is empty');

    $style = new Style('red');
    $stack->push($style);
    $this->assertSame($style, $stack->getCurrent(), 'getCurrent() returns the only one style in the stack');

    $green = new Style('green');
    $stack->push($green);
    $green->setUnset(31, null);
    $this->assertSame($green, $stack->getCurrent(), 'getCurrent() returns the last style in the stack, with updated properties');

    $stack->pop();
    $this->assertSame($style, $stack->getCurrent(), 'getCurrent() returns the last style in the stack after pop()');
  }
}
