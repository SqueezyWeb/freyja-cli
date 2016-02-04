<?php
/**
 * Input Optionn class file.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;
use Freyja\Exceptions\InvalidArgumentException;
use Freyja\Exceptions\LogicException;

/**
 * Represent a command line option.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class Option {
  /**
   * Option without value.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const VALUE_NONE = 1;

  /**
   * Option value required.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const VALUE_REQUIRED = 2;

  /**
   * Option value optional.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const VALUE_OPTIONAL = 4;

  /**
   * Option with multiple values.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const VALUE_IS_ARRAY = 8;

  /**
   * Option name.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $name;

  /**
   * Option shortcut.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $shortcut;

  /**
   * Option mode.
   *
   * @since 1.0.0
   * @access private
   * @var int
   */
  private $mode;

  /**
   * Default value.
   *
   * @since 1.0.0
   * @access private
   * @var mixed
   */
  private $default;

  /**
   * Option description text.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $description;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @param string|array $shortcut Optional. Shortcuts. Can be null, a string of
   * shortcuts delimited by | or an array of shortcuts. Default null.
   * @param int $mode Optional. Option mode. One of the VALUE_* constants. If
   * null, self::VALUE_NONE is assigned. Default null.
   * @param string $description Optional. Description text. Default empty.
   * @param mixed $default Optional. Default velue. Must be null for
   * self::VALUE_REQUIRED or self::VALUE_NONE. Default null.
   *
   * @throws InvalidArgumentException if option mode is invalid or incompatible.
   */
  public function __construct($name, $shortcut = null, $mode = null, $description = '', $default = null) {
    if (0 === strpos($name, '--'))
      $name = substr($name, 2);

    if (empty($name))
      throw new InvalidArgumentException('An option name cannot be empty.');

    if (null !== $shortcut) {
      if (is_array($shortcut))
        $shortcut = implode('|', $shortcut);
      $shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));
      $shortcuts = array_filter($shortcuts);
      $shortcut = implode('|', $shortcuts);

      if (empty($shortcut))
        // TODO: maybe throw some kind of warning here.
        $shortcut = null;
    }

    if (null === $mode)
      $mode = self::VALUE_NONE;
    elseif (!is_int($mode) || $mode > 15 || $mode < 1)
      throw new InvalidArgumentException(
        sprintf('Option mode "%s" is not valid.', $mode)
      );

    $this->name = $name;
    $this->shortcut = $shortcut;
    $this->mode = $mode;
    $this->description = $description;
    $this->setDefault($default);
  }

  /**
   * Retrieve option shortcut.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string The shortcut.
   */
  public function getShortcut() {
    return $this->shortcut;
  }

  /**
   * Retrieve option name.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string The option name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Whether the option accepts a value.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if value mode is self::VALUE_NONE, false otherwise.
   */
  public function acceptValue() {
    return self::VALUE_NONE !== (self::VALUE_NONE & $this->mode);
  }

  /**
   * Whether the option requires a value.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if value mode is self::VALUE_REQUIRED, false otherwise.
   */
  public function isValueRequired() {
    return self::VALUE_REQUIRED === (self::VALUE_REQUIRED & $this->mode);
  }

  /**
   * Whether the option takes an optional value.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if value mode is self::VALUE_OPTIONAL, false otherwise.
   */
  public function isValueOptional() {
    return self::VALUE_OPTIONAL === (self::VALUE_OPTIONAL & $this->mode);
  }

  /**
   * Whether the option can take multiple values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if value mode is self::VALUE_IS_ARRAY, false otherwise.
   */
  public function isArray() {
    return self::VALUE_IS_ARRAY === (self::VALUE_IS_ARRAY & $this->mode);
  }

  /**
   * Set default value.
   *
   * @since 1.0.0
   * @access public
   *
   * @param mixed $default Default value.
   *
   * @throws Freyja\Exceptions\LogicException if incorrect default value is given.
   */
  public function setDefault($default = null) {
    if (!$this->acceptValue() && null !== $default)
      throw new LogicException('Cannot set a default value when using Option::VALUE_NONE mode.');

    if ($this->isArray())
      if (null === $default)
        $default = array();
      elseif (!is_array($default))
        throw new LogicException('A default value for an array option must be an array.');

    $this->default = $this->acceptValue() ? $default : false;
  }

  /**
   * Retrieve default value.
   *
   * @since 1.0.0
   * @access public
   *
   * @return mixed Default value.
   */
  public function getDefault() {
    return $this->default;
  }

  /**
   * Retrieve description text.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Description text.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Whether the given option equals this one.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Option $option Option to compare.
   * @return bool
   */
  public function equals(Option $option) {
    return $option->getName() === $this->getName() &&
      $option->getShortcut() === $this->getShortcut() &&
      $option->getDefault() === $this->getDefault() &&
      $option->isArray() === $this->isArray() &&
      $option->isValueRequired() === $this->isValueRequired() &&
      $option->isValueOptional() === $this->isValueOptional();
  }
}
