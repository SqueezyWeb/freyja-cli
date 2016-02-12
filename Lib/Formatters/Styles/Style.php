<?php
/**
 * Freyja CLI Output Formatter Style.
 *
 * @package Freyja\CLI\Formatters\Styles
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Formatters\Styles;

use Freyja\Exceptions\InvalidArgumentException;

/**
 * Formatter style class for console output.
 *
 * @package Freyja\CLI\Formatters\Styles
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class Style implements StyleInterface {
  /**
   * Available foreground colors.
   *
   * @since 1.0.0
   * @access private
   * @static
   * @var array
   */
  private static $available_foreground_colors = array(
    'black' => array('set' => 30, 'unset' => 39),
    'red' => array('set' => 31, 'unset' => 39),
    'green' => array('set' => 32, 'unset' => 39),
    'yellow' => array('set' => 33, 'unset' => 39),
    'blue' => array('set' => 34, 'unset' => 39),
    'magenta' => array('set' => 35, 'unset' => 39),
    'cyan' => array('set' => 36, 'unset' => 39),
    'white' => array('set' => 37, 'unset' => 39),
    'default' => array('set' => 39, 'unset' => 39),
  );

  /**
   * Available background colors.
   *
   * @since 1.0.0
   * @access private
   * @static
   * @var array
   */
  private static $available_background_colors = array(
    'black' => array('set' => 40, 'unset' => 49),
    'red' => array('set' => 41, 'unset' => 49),
    'green' => array('set' => 42, 'unset' => 49),
    'yellow' => array('set' => 43, 'unset' => 49),
    'blue' => array('set' => 44, 'unset' => 49),
    'magenta' => array('set' => 45, 'unset' => 49),
    'cyan' => array('set' => 46, 'unset' => 49),
    'white' => array('set' => 47, 'unset' => 49),
    'default' => array('set' => 49, 'unset' => 49),
  );

  /**
   * Available options.
   *
   * @since 1.0.0
   * @access private
   * @static
   * @var array
   */
  private static $available_options = array(
    'bold' => array('set' => 1, 'unset' => 22),
    'underscore' => array('set' => 4, 'unset' => 24),
    'blink' => array('set' => 5, 'unset' => 25),
    'reverse' => array('set' => 7, 'unset' => 27),
    'conceal' => array('set' => 8, 'unset' => 28),
  );

  /**
   * Foreground color.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $foreground;

  /**
   * Background color.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $background;

  /**
   * Options.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $options = array();

  /**
   * Initialize output formatter style.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|null $foreground Optional. Foreground color name. Default null.
   * @param string|null $background Optional. Background color name. Default null.
   * @param array $options Optional. Style options. Default empty.
   */
  public function __construct($foreground = null, $background = null, array $options = array()) {
    if (!is_null($foreground))
      $this->setForeground($foreground);
    if (!is_null($background))
      $this->setBackground($background);
    if (!empty($options))
      $this->setOptions($options);
  }

  /**
   * Set style foreground color.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|null $color Optional. Color name. Default null.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if color name is invalid.
   */
  public function setForeground($color = null) {
    try {
      $this->setColor('foreground', $color);
    } catch (InvalidArgumentException $e) {
      throw $e;
    }
  }

  /**
   * Set style background color.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|null $color Optional. Color name. Default null.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if color name is invalid.
   */
  public function setBackground($color = null) {
    try {
      $this->setColor('background', $color);
    } catch (InvalidArgumentException $e) {
      throw $e;
    }
  }

  /**
   * Set some specific style option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $option Option name.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if option name is invalid.
   */
  public function setOption($option) {
    try {
      if (!isset(static::$available_options[$option]))
        $this->invalidArgument('option', $option);

      if (!in_array(static::$available_options[$option], $this->options))
        $this->options[$option] = static::$available_options[$option];
    } catch (InvalidArgumentException $e) {
      throw $e;
    }
  }

  /**
   * Unset some specific style option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $option Option name.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if option name is invalid.
   */
  public function unsetOption($option) {
    try {
      if (!isset(static::$available_options[$option]))
        $this->invalidArgument('option', $option);

      if (isset($this->options[$option]))
        unset($this->options[$option]);
    } catch (InvalidArgumentException $e) {
      throw $e;
    }
  }

  /**
   * Set multiple style options at once.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $options
   */
  public function setOptions(array $options) {
    $this->options = array();

    foreach ($options as $option)
      $this->setOption($option);
  }

  /**
   * {@inheritdoc}
   */
  public function setUnset($foreground, $background, array $options = array()) {
    $this->foreground['unset'] = $foreground;
    $this->background['unset'] = $background;
    foreach ($options as $name => $option) {
      $this->options[$name]['unset'] = $option['unset'];
    }
  }

  /**
   * Reset unset colors and options.
   *
   * @since 1.0.1
   * @access public
   */
  public function resetUnset() {
    $this->foreground['unset'] = $this->available_foreground_colors['default']['unset'];
    $this->background['unset'] = $this->available_background_colors['default']['unset'];
    foreach ($this->options[] as $name => &$option)
      if (isset($option['set']))
        $option['unset'] = $this->available_options[$name]['unset'];
  }

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
  public function getForeground() {
    return isset($this->foreground['set']) ? $this->foreground['set'] : $this->available_foreground_colors['default']['set'];
  }

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
  public function getBackground() {
    return isset($this->background['set']) ? $this->background['set'] : $this->available_background_colors['default']['set'];
  }

  /**
   * Retrieve options.
   *
   * @since 1.0.1
   * @access public
   *
   * @return array Active options.
   */
  public function getOptions() {
    return isset($this->options['set']) ? $this->options['set'] : array();
  }

  /**
   * Apply style to given text.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $text Text to style.
   *
   * @return string
   */
  public function apply($text) {
    $set = array();
    $unset = array();

    if (!is_null($this->foreground)) {
      $set[] = $this->foreground['set'];
      $unset[] = $this->foreground['unset'];
    }
    if (!is_null($this->background)) {
      $set[] = $this->background['set'];
      $unset[] = $this->background['unset'];
    }
    if (!is_null($this->options)) {
      foreach ($this->options as $option) {
        if (isset($option['set']))
          $set[] = $option['set'];
        if (isset($option['unset']))
          $unset[] = $option['unset'];
      }
    }

    if (empty($set))
      return $text;

    return sprintf(
      "\033[%sm%s\033[%sm",
      implode(';', $set),
      $text,
      implode(';', $unset)
    );
  }

  /**
   * Utility for setting foreground and background colors.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $element Element to set color to. Accepts foreground, background.
   * @param string|null $color Color name.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if element is invalid.
   */
  private function setColor($element, $color) {
    if (!in_array($element, array('foreground', 'background')))
      throw new InvalidArgumentException('Invalid element for setColor(). Accepted values: "foreground", "background"');

    if (is_null($color)) {
      $this->$element = null;
      return;
    }

    if (!isset(static::${'available_'.$element.'_colors'}[$color]))
      $this->invalidArgument($element.' color', $color);

    $this->$element = static::${'available_'.$element.'_colors'}[$color];
  }

  /**
   * Raise exception for invalid argument.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $element Element that is invalid.
   * @param string $value Invalid value name.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException always.
   */
  private function invalidArgument($element, $value) {
    throw new InvalidArgumentException(sprintf(
      'Invalid %s specified: "%s". Expected one of (%s)',
      $element,
      $value,
      implode(', ', array_keys(static::${'available_'.str_replace(' ', '_', $element).'s'}))
    ));
  }
}
