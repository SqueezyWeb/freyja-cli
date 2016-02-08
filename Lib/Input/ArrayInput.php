<?php
/**
 * Freyja CLI Array Input.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

use Freyja\CLI\Exceptions\InvalidArgumentException;
use Freyja\CLI\Exceptions\InvalidOptionException;

/**
 * Represent input provided as array.
 *
 * Usage:
 *
 * $input = new ArrayInput(array('name' => 'foo', '--bar' => 'foobar'));
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ArrayInput extends Input {
  /**
   * Input parameters.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $parameters;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $parameters Array of parameters.
   * @param Definition $definition Optional. Definition instance. If null, the
   * default Definition will be used. Default null.
   */
  public function __construct(array $parameters, Definition $definition = null) {
    $this->parameters = $parameters;

    parent::__construct($definition);
  }

  /**
   * Retrieve first argument from raw parameters (not parsed).
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Value of the first argument or null otherwise.
   */
  public function getFirstArgument() {
    foreach ($this->parameters as $key => $value) {
      if ($key && '-' === $key[0])
        continue;

      return $value;
    }
  }

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
   * @param bool $only_params Optional. Whether to only check real parameters
   * and skip those following an end of options (--) signal. Default false.
   *
   * @return bool True if value is contained in the raw parameters.
   */
  public function hasParameterOption($values, $only_params = false) {
    $values = (array) $values;

    foreach ($this->parameters as $k => $v) {
      if (!is_int($k))
        $v = $k;

      if ($only_params && $v === '--')
        return false;

      if (in_array($v, $values))
        return true;
    }

    return false;
  }

  /**
   * Retrieve value of raw option (not parsed).
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
   * @param bool $only_params Optional. Only check real parameters, skip those
   * following an end of options (--) signal. Default false.
   *
   * @return mixed Option value.
   */
  public function getParameterOption($values, $default = false, $only_params = false) {
    $values = (array) $values;

    foreach ($this->parameters as $k => $v) {
      if ($only_params && ($k === '--' || (is_int($k) && $v === '--')))
        return false;

      if (is_int($k))
        if (in_array($v, $values))
          return true;
      elseif (in_array($k, $values))
        return $v;
    }

    return $default;
  }

  /**
   * Retrieve stringified representation of args passed to the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string
   */
  public function __toString() {
    $params = array();

    foreach ($this->parameters as $param => $val) {
      if ($param && '-' === $param[0])
        $params[] = $param.('' != $val ? '='.$this->escapeToken($val) : '');
      else
        $params[] = $this->escapeToken($val);
    }

    return join(' ', $params);
  }

  /**
   * Process command line arguments.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function parse() {
    foreach ($this->parameters as $key => $value) {
      if ($key === '--')
        return;

      if (0 === strpos($key, '--'))
        $this->addLongOption(substr($key, 2), $value);
      elseif ('-' === $key[0])
        $this->addShortOption(substr($key, 1), $value);
      else
        $this->addArgument($key, $value);
    }
  }

  /**
   * Add short option value.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $shortcut Short option key.
   * @param mixed $value Option value.
   *
   * @throws Freyja\CLI\Exceptions\InvalidOptionException if given option does not
   * exist.
   */
  private function addShortOption($shortcut, $value) {
    if (!$this->definition->hasShortcut($shortcut))
      throw InvalidOptionException::notFound($shortcut);

    $this->addLongOption($this->definition->getOptionByShortcut($shortcut)->getName(), $value);
  }

  /**
   * Add long option value.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $name Long option key.
   * @param mixed $value Option value.
   *
   * @throws Freyja\CLI\Exceptions\InvalidOptionException if given option doesn't exist.
   * @throws Freyja\CLI\Exceptions\InvalidOptionException if required value is missing.
   */
  private function addLongOption($name, $value) {
    if (!$this->definition->hasOption($name))
      throw InvalidOptionException::notFound($name);

    $option = $this->definition->getOption($name);

    if (null === $value) {
      if ($option->isValueRequired())
        throw InvalidOptionException::valueRequired($name);

      $value = $option->isValueOptional() ? $option->getDefault() : true;
    }

    $this->options[$name] = $value;
  }

  /**
   * Add argument value.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $name Argument name.
   * @param mixed $value Argument value.
   *
   * @throws Freyja\CLI\Exceptions\InvalidArgumentException if given argument
   * doesn't exist.
   */
  private function addArgument($name, $value) {
    if (!$this->definition->hasArgument($name))
      throw InvalidArgumentException::notFound($name);

    $this->arguments[$name] = $value;
  }
}
