<?php
/**
 * Freyja CLI Base Input.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

use Freyja\CLI\Exceptions\InvalidArgumentException;
use Freyja\CLI\Exceptions\RuntimeException;

/**
 * Base class for all concrete Input classes.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
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
  public function __construct(Defintion $definition = null) {
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

    $missing_arguments = array_filter(array_keys($definition->getArguments()), function ($argument) use ($definition, $given_arguments) {
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
    return array_merge($this->definintion->getArgumentDefaults(), $this->arguments);
  }
}
