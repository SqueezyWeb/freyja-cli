<?php
/**
 * Freyja CLI Output Formatter Interface.
 *
 * @package Freyja\CLI\Formatters
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Formatters;

/**
 * Formatter interface for console output.
 *
 * @package Freyja\CLI\Formatters
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface OutputFormatterInterface {
  /**
   * Set decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $decorated Whether to decorate messages or not.
   */
  public function setDecorated($decorated);

  /**
   * Retrieve decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if output will decorate messages, false otherwise.
   */
  public function isDecorated();

  /**
   * Set new style.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Style name.
   * @param OutputFormatterStyleInterface $style Style instance.
   */
  public function setStyle($name, OutputFormatterStyleInterface $style);

  /**
   * Check if output formatter has style with specified name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name
   *
   * @return bool
   */
  public function hasStyle($name);

  /**
   * Retrieve style options from style with specified name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name
   *
   * @return OutputFormatterStyleInterface
   */
  public function getStyle($name);

  /**
   * Format message according to given styles.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $message Message to style.
   *
   * @return string Styled message.
   */
  public function format($message);
}
