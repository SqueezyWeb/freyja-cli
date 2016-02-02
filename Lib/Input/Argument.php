<?php
/**
 * Input Argument class file.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;
use Freyja\Exceptions\InvalidArgumentException;
use Freyja\Exceptions\LogicException;

/**
 * Represents a command line argument.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class Argument {
  /**
   * Argument required mode.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const REQUIRED = 1;

  /**
   * Argument optional mode.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const OPTIONAL = 2;

  /**
   * Argument array mode.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const IS_ARRAY = 4;

  /**
   * Argument name.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $name;

  /**
   * Argument mode.
   *
   * @since 1.0.0
   * @access private
   * @var int
   */
  private $mode;

  /**
   * Argument default value.
   *
   * @since 1.0.0
   * @access private
   * @var mixed
   */
  private $default;

  /**
   * Argument description.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $description;

  /**
   * Constructor.
   *
   * @param string $name Argument name.
   * @param int $mode Optional. Argument mode: self::REQUIRED, self::OPTIONAL,
   * or self::IS_ARRAY. If null, it is initialized at self::OPTIONAL. Default
   * null.
   * @param string $description Optional. Argument description text. Default
   * empty.
   * @param mixed $default Optional. Default value (for self::OPTIONAL or
   * self::IS_ARRAY modes only). Default null.
   *
   * @throws InvalidArgumentException if argument mode is not valid.
   */
  public function __construct($name, $mode = null, $description = '', $default = null) {
    if (null === $mode)
      $mode = self::OPTIONAL;
    elseif (!is_int($mode) || $mode > 7 || $mode < 1)
      throw new InvalidArgumentException(
        sprintf('Argument mode "%s" is not valid.', $mode)
      );

    $this->name = $name;
    $this->mode = $mode;
    $this->description = $description;

    $this->setDefault($default);
  }

  /**
   * Retrieve argument name.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string The argument name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Whether argument is required or not.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool Whether argument mode is self::REQUIRED or not.
   */
  public function isRequired() {
    return self::REQUIRED === (self::REQUIRED & $this->mode);
  }

  /**
   * Whether the argument can take multiple values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool Whether argument mode is self::IS_ARRAY.
   */
  public function isArray() {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  /**
   * Set default value.
   *
   * @since 1.0.0
   * @access public
   *
   * @param mixed $default Default value.
   *
   * @throws LogicException if given default value is incorrect.
   */
  public function setDefault($default = null) {
    if (self::REQUIRED === $this->mode && null !== $default)
      throw new LogicException('Cannot set a default value for Argument::REQUIRED mode.');

    if ($this->isArray())
      if (null === $default)
        $default = array();
      elseif (!is_array($default))
        throw new LogicException('A default value for an array argument must be an array.');

    $this->default = $default;
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
}
