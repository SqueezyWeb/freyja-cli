<?php
/**
 * Freyja CLI Input Public API definition.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

/**
 * Interface implemented by all input classes.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface InputInterface {
  /**
   * Retrieve first argument from the raw parameters (not parsed).
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Value of the first argument or null otherwise.
   */
  public function getFirstArgument();

  /**
   * Whether raw parameters (not parsed) contain a value.
   *
   * This method is to be used to introspect the input parameters before they
   * have been validated. It must be used carefully.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $values Values to look for in the raw parameters (can
   * be an array).
   * @param bool $only_params Optional. Only check real parameters, skip those
   * following an end of options (--) signal. Default false.
   * @return bool True if the value is contained in the raw parameters.
   */
  public function hasParameterOption($values, $only_params = false);

  /**
   * Retrieve value of a raw option (not parsed).
   *
   * This method is to be used to introspect the input parameters before they
   * have been validated. It must be used carefully.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $values Value(s) to look for in the raw parameters (can
   * be an array).
   * @param mixed $default Optional. Default value to return if no result is
   * found. Default false.
   * @param bool $only_params Optional. Only check real params, skip those
   * following an end of options (--) signal.
   * @return mixed Option value.
   */
  public function getParameterOption($values, $default = false, $only_params = false);

  /**
   * Bind current Input instance with the given arguments and options.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Definition $definition Input definition instance.
   */
  public function bind(Definition $definition);

  /**
   * Validate arguments.
   *
   * @since 1.0.0
   * @access public
   *
   * @throws \RuntimeException if not enough arguments are given.
   */
  public function validate();

  /**
   * Retrieve all the given arguments merged with the default values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getArguments();

  /**
   * Retrieve argument by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Name of the argument.
   * @return mixed
   */
  public function getArgument($name);

  /**
   * Set argument value by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Argument name.
   * @param string $value Argument value.
   *
   * @throws InvalidArgumentException if given argument does not exist.
   */
  public function setArgument($name, $value);

  /**
   * Whether an Argument object exists by name or position.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|int $name Argument name or position.
   * @return bool Whether the Argument object exists.
   */
  public function hasArgument($name);

  /**
   * Retrieve all the given options merged with default values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getOptions();

  /**
   * Retrieve option by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return mixed Option value.
   */
  public function getOption($name);

  /**
   * Set option value by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @param string|bool $value Option value.
   *
   * @throws InvalidArgumentException if option given does not exist.
   */
  public function setOption($name, $value);

  /**
   * Whether Option object exists by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return bool Whether the Option object exists.
   */
  public function hasOption($name);

  /**
   * Whether this input is interactive.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool
   */
  public function isInteractive();

  /**
   * Set input interactivity.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $interactive If the input should be interactive.
   */
  public function setInteractive($interactive);
}
