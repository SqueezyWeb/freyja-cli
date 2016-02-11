<?php
/**
 * Freyja CLI Output Formatter.
 *
 * @package Freyja\CLI\Formatters
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Formatters;

use Freyja\Exceptions\InvalidArgumentException;
use Freyja\CLI\Formatters\Styles\StyleInterface;
use Freyja\CLI\Formatters\Styles\OutputFormatter as Style;

/**
 * Formatter class for console output.
 *
 * @package Freyja\CLI\Formatters
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class OutputFormatter implements OutputFormatterInterface {
  /**
   * Whether output should be decorated.
   *
   * @since 1.0.0
   * @access private
   * @var bool
   */
  private $decorated;

  /**
   * Styles associated with formatter.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $styles = array();

  /**
   * Escape "<" special char in given text.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param string $text Text to escape.
   *
   * @return string Escaped text.
   */
  public static function escape($text) {
    return preg_replace('/([^\\\\]?)</', '$1\\<', $text);
  }

  /**
   * Initialize console output formatter.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $decorated Whether this formatter should actually decorate strings.
   * @param StyleInterface[] $styles Array of "name => FormatterStyle"
   * instances.
   */
  public function __construct($decorated = false, array $styles = array()) {
    $this->decorated = (bool) $decorated;

    $this->setStyle('error', new Style('white', 'red'));
    $this->setStyle('info', new Style('green'));
    $this->setStyle('warning', new Style('yellow'));
    $this->setStyle('comment', new Style('yellow'));
    $this->setStyle('question', new Style('black', 'cyan'));

    foreach ($styles as $name => $style)
      $this->setStyle($name, $style);
  }

  /**
   * {@inheritdoc}
   */
  public function setDecorated($decorated) {
    $this->decorated = (bool) $decorated;
  }

  /**
   * {@inheritdoc}
   */
  public function isDecorated() {
    // Cast to bool since it does not have a default value.
    return (bool) $this->decorated;
  }

  /**
   * {@inheritdoc}
   */
  public function setStyle($name, StyleInterface $style) {
    $this->styles[strtolower($name)] = $style;
  }

  /**
   * {@inheritdoc}
   */
  public function hasStyle($name) {
    return isset($this->styles[strtolower($name)]);
  }

  /**
   * {@inheritdoc}
   */
  public function getStyle($name) {
    if (!$this->hasStyle($name))
      throw new InvalidArgumentException(sprintf('Undefined style: %s', $name));

    return $this->styles[strtolower($name)];
  }

  /**
   * {@inheritdoc}
   */
  public function format($message) {
    return preg_replace_callback('#<([a-z][a-z0-9_=;-]*)>(.*?)</\1>#i', array($this, 'formatCallback'), (string) $message);
  }

  /**
   * Callback for format.
   *
   * Matches format tags and formats enclosed string according to the style tag.
   * Supports nested tags.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param array $matches Array of preg_replace_callback() matches.
   *
   * @return string Formatted message.
   */
  protected function formatCallback(array $matches) {
    $style = strtolower($matches[1]);
    $message = $matches[2];

    if (!$this->hasStyle($style))
      return $matches[0];

    if (preg_match('#<([a-z][a-z0-9_=;-]*)>(.*?)</\1>#i', $message))
      $message = $this->format($message);

    return str_replace('\\<', '<', $this->applyStyle($style, $message));
  }

  /**
   * Apply given style to message.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $style Style name.
   * @param string $text Input text.
   *
   * @return string Styled text.
   */
  private function applyStile($style, $text) {
    return $this->isDecorated() && strlen($text) > 0 ? $this->styles[$style]->apply($text) : $text;
  }
}
