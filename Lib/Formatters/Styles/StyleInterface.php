<?php
/**
 * Freyja CLI Output Formatter Style Interface.
 *
 * @package Freyja\CLI\Formatters\Styles
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Formatters\Styles;

/**
 * Formatter style interface for defining styles.
 *
 * @package Freyja\CLI\Formatters\Styles
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface StyleInterface {
  /**
   * Set style foreground color.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $color Optional. Color name. Default null.
   */
  public function setForeground($color = null);

  /**
   * Set style background color.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $color Optional. Color name. Default null.
   */
  public function setBackground($color = null);

  /**
   * Set some specific style option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $option Option name.
   */
  public function setOption($option);

  /**
   * Unset some specific style option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $option Option name.
   */
  public function unsetOption($option);

  /**
   * Set multiple style options at once.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $options
   */
  public function setOptions(array $options);

  /**
   * Apply style to given text.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $text Text to style.
   *
   * @return string Styled string.
   */
  public function apply($text);
}
