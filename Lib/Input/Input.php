<?php
/**
 * Freyja CLI Base Input.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

use Freyja\Exceptions\InvalidArgumentException;
use Freyja\Exceptions\RuntimeException;

/**
 * Base class for all concrete Input classes.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 * @abstract
 */
abstract class Input implements InputInterface {
  /**
   * Input Definition.
   *
   * @since 1.0.0
   * @access protected
   * @var Definition
   */
  protected $definition;

  /**
   * Input Options.
   *
   * @since 1.0.0
   * @access protected
   * @var array
   */
  protected $options = array();

  /**
   * Input Arguments.
   *
   * @since 1.0.0
   * @access protected
   * @var array
   */
  protected $arguments = array();

  /**
   * Whether current input is interactive or not.
   *
   * @since 1.0.0
   * @access protected
   * @var bool
   */
  protected $interactive = true;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Definition $definition Optional. Definition instance. Default null.
   */
  public function __construct(Definition $definition = null) {
    if (is_null($definition)) {
      $this->definition = new Definition;
    } else {
      $this->bind($definition);
      $this->validate();
    }
  }

  /**
   * Bind current Input instance with given arguments and options.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Definition $definition Definition instance.
   */
  public function bind(Definition $definition) {
    $this->arguments = array();
    $this->options = array();
    $this->definition = $definition;

    $this->parse();
  }

  /**
   * Process command line arguments.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   */
  abstract protected function parse();

  /**
   * Validate input.
   *
   * @since 1.0.0
   * @access public
   *
   * @throws Freyja\Exceptions\RuntimeException if not enough arguments are given.
   */
  public function validate() {
    $definition = $this->definition;
    $given_arguments = $this->arguments;

    $missing_arguments = array_filter(array_keys((array) $definition->getArguments()), function ($argument) use ($definition, $given_arguments) {
      return !array_key_exists($argument, $given_arguments) && $definition->getArgument($argument)->isRequired();
    });

    if (count($missing_arguments) > 0)
      throw new RuntimeException(sprintf('Not enough arguments (missing: "%s").', join(', ', $missing_arguments)));
  }

  /**
   * Check if input is interactive.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if input is interactive.
   */
  public function isInteractive() {
    return $this->interactive;
  }

  /**
   * Set input interactivity.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $interactive If input should be interactive.
   */
  public function setInteractive($interactive) {
    $this->interactive = (bool) $interactive;
  }

  /**
   * Retrieve argument values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Array of argument values.
   */
  public function getArguments() {
    return array_merge($this->definition->getArgumentDefaults(), $this->arguments);
  }

  /**
   * Retrieve argument value for given argument name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Argument name.
   * @return mixed Argument value.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if given argument does
   * not exist.
   */
  public function getArgument($name) {
    if (!$this->definition->hasArgument($name))
      throw new InvalidArgumentException(sprintf('Argument "%s" does not exist.', $name));

    return isset($this->arguments[$name]) ? $this->arguments[$name] : $this->definition->getArgument($name)->getDefault();
  }

  /**
   * Set argument value by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Argument name.
   * @param string $value Argument value.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if given argument does
   * not exist.
   */
  public function setArgument($name, $value) {
    if (!$this->definition->hasArgument($name))
      throw new InvalidArgumentException(sprintf('Argument "%s" does not exist.', $name));

    $this->arguments[$name] = $value;
  }

  /**
   * Whether Argument object exists by name or position.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|int $name Argument name or position.
   * @return bool True if Argument object exists, false otherwise.
   */
  public function hasArgument($name) {
    return $this->definition->hasArgument($name);
  }

  /**
   * Retrieve option values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Array of option values.
   */
  public function getOptions() {
    return array_merge($this->definition->getOptionDefaults(), $this->options);
  }

  /**
   * Retrieve option value for given option name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return mixed Option value.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if given option does not
   * exist.
   */
  public function getOption($name) {
    if (!$this->definition->hasOption($name))
      throw new InvalidArgumentException(sprintf('Option "%s" does not exist.', $name));

    return isset($this->options[$name]) ? $this->options[$name] : $this->definition->getOption($name)->getDefault();
  }

  /**
   * Set option value by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @param string $value Option value.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if given option does not
   * exist.
   */
  public function setOption($name, $value) {
    if (!$this->definition->hasOption($name))
      throw new InvalidArgumentException(sprintf('Option "%s" does not exist.', $name));

    $this->options[$name] = $value;
  }

  /**
   * Whether Option object exists by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return bool True if Option object exists, false otherwise.
   */
  public function hasOption($name) {
    return $this->definition->hasOption($name);
  }

  /**
   * Escape token through escapeshellarg if it contains unsafe chars.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $token
   * @return string
   */
  public function escapeToken($token) {
    return preg_match('/^[\w-]+$/', $token) ? $token : escapeshellarg($token);
  }
}
