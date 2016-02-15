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
 * @version 1.0.1
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
   * Set unset styles.
   *
   * Used to fully support nested styles.
   *
   * @since 1.0.1
   * @access public
   *
   * @param int $foreground Color code for the foreground.
   * @param int $background Color code for the background.
   * @param array $options Optional. Array of options. Default empty.
   */
  public function setUnset($foreground, $background, array $options = array());

  /**
   * Retrieve foreground color.
   *
   * Important: this only retrieves the foreground color of this style, i.e. the
   * 'set' key in Style::$foreground.
   *
   * @since 1.0.1
   * @access public
   *
   * @return int Foreground 'set' color code.
   */
  public function getForeground();

  /**
   * Retrieve background color.
   *
   * Important: this only retrieves the background color for this style, i.e. the
   * 'set' key in Style::$background.
   *
   * @since 1.0.1
   * @access public
   *
   * @return int Background 'set' color code.
   */
  public function getBackground();

  /**
   * Retrieve options.
   *
   * @since 1.0.1
   * @access public
   *
   * @return array Active options.
   */
  public function getOptions();

  /**
   * Reset unset colors and options.
   *
   * @since 1.0.1
   * @access public
   */
  public function resetUnset();

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
