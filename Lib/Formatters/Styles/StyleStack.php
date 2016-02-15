<?php
/**
 * Freyja CLI Formatters Style Stack.
 *
 * @package Freyja\CLI\Formatters\Styles
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Formatters\Styles;

use Freyja\Exceptions\InvalidArgumentException;

/**
 * Stack of styles.
 *
 * @package Frayja\CLI\Formatters\Styles
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class StyleStack {
  /**
   * Stack of styles.
   *
   * @since 1.0.0
   * @access private
   * @var StyleInterface[]
   */
  private $styles;

  /**
   * Empty style instance.
   *
   * @since 1.0.0
   * @access private
   * @var StyleInterface
   */
  private $empty_style;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param StyleInterface|null $empty_style Optional. Default empty style.
   * When null, a default Style instance will be created. Default null.
   */
  public function __construct(StyleInterface $empty_style = null) {
    $this->empty_style = $empty_style ?: new Style;
  }

  /**
   * Reset stack.
   *
   * Empty internal arrays.
   *
   * @since 1.0.0
   * @access public
   */
  public function reset() {
    $this->styles = array();
  }

  /**
   * Push style in the stack.
   *
   * @since 1.0.0
   * @access public
   *
   * @param StyleInterface $style
   *
   * @return self
   */
  public function push(StyleInterface &$style) {
    if (!empty($this->styles))
      $old = $this->styles[count($this->styles) - 1];
    else
      $old = $this->empty_style;

    $style->setUnset(
      $old->getForeground(),
      $old->getBackground(),
      $old->getOptions()
    );

    $this->styles[] = $style;

    return $this;
  }

  /**
   * Pop style from the stack.
   *
   * @since 1.0.0
   * @access public
   *
   * @return StyleInterface
   */
  public function pop() {
    if (empty($this->styles))
      return $this->empty_style;
    $style = array_pop($this->styles);
    $style->resetUnset();
    return $style;
  }

  /**
   * Compute current style with stacks top codes.
   *
   * @since 1.0.0
   * @access public
   *
   * @return Style
   */
  public function getCurrent() {
    if (empty($this->styles))
      return $this->empty_style;

    return $this->styles[count($this->styles) - 1];
  }

  /**
   * Set empty style.
   *
   * @since 1.0.0
   * @access public
   *
   * @param StyleInterface $style
   *
   * @return StyleStack
   */
  public function setEmptyStyle(StyleInterface $style) {
    $this->empty_style = $style;

    return $this;
  }

  /**
   * Retrieve empty style.
   *
   * @since 1.0.0
   * @access public
   *
   * @return StyleInterface
   */
  public function getEmptyStyle() {
    return $this->empty_style;
  }
}
