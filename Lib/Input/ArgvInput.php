<?php
/**
 * Freyja CLI Argv Input.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

use Freyja\Exceptions\RuntimeException;

/**
 * Represent input coming from CLI arguments.
 *
 * Usage:
 * $input = new ArgvInput();
 *
 * By default the `$_SERVER['argv']` array is used for the input values.
 * This can be overridden by explicitly passing the input values in the
 * constructor:
 *
 * Usage:
 * $input = new ArgvInput($_SERVER['argv']);
 *
 * If you pass it yourself, don't forget that the first element of the array
 * is the name of the running application.
 *
 * When passing an argument to the constructor, be sure that it respects the
 * same rules as the argv one. It's almost always better to use the `StringInput`
 * when you want to provide your own input.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 *
 * @see http://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html
 * @see http://www.opengroup.org/onlinepubs/009695399/basedefs/xbd_chap12.html#tag_12_02
 */
class ArgvInput extends Input {
  /**
   * Input Tokens.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $tokens;

  /**
   * Parsed input.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $parsed;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $argv Optional. Array of parameters from the CLI (in the argv
   * format). When null, takes input values from `$_SERVER['argv']`. Default null.
   * @param Definition $definition Optional. Input Definition instance. When null,
   * instantiates a new Definition instance. Default null.
   */
  public function __construct(array $argv = null, Definition $definition = null) {
    if (is_null($argv))
      $argv = $_SERVER['argv'];

    // Strip the application name.
    array_shift($argv);

    $this->tokens = $argv;

    parent::__construct($definition);
  }

  /**
   * Set command line arguments tokens.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param array $tokens Array of command line arguments tokens.
   */
  protected function setTokens(array $tokens) {
    $this->tokens = $tokens;
  }

  /**
   * Process command line arguments.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function parse() {
    $parse_options = true;
    $this->parsed = $this->tokens;

    while (null !== $token = array_shift($this->parsed)) {
      if ($parse_options && empty($token))
        $this->parseArgument($token);
      elseif ($parse_options && '--' == $token)
        $parse_options = false;
      elseif ($parse_options && 0 === strpos($token, '--'))
        $this->parseLongOption($token);
      elseif ($parse_options && '-' === $token[0] && '-' !== $token)
        $this->parseShortOption($token);
      else
        $this->parseArgument($token);
    }
  }

  /**
   * Parse short option.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $token Current token.
   */
  private function parseShortOption($token) {
    $name = substr($token, 1);

    if (strlen($name) > 1) {
      if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionByShortcut($name[0])->acceptValue()) {
        // Option with a value (with no space).
        $this->addShortOption($name[0], substr($name, 1));
      } else {
        // Option without value.
        // Add it, then continue parsing the token.
        $this->addShortOption($name[0], null);
        $this->parseShortOption($name);
      }
    } else {
      $this->addShortOption($name, null);
    }
  }

  /**
   * Parse long option.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $token Current token.
   */
  private function parseLongOption($token) {
    $name = substr($token, 2);

    if (false !== $pos = strpos($name, '='))
      $this->addLongOption(substr($name, 0, $pos), substr($name, $pos + 1));
    else
      $this->addLongOption($name, null);
  }

  /**
   * Parse argument.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $token Current token.
   *
   * @throws Freyja\Exceptions\RuntimeException if too many arguments are given.
   */
  private function parseArgument($token) {
    $c = count($this->arguments);

    // If input is expecting another argument, add it.
    if ($this->definition->hasArgument($c)) {
      $arg = $this->definition->getArgument($c);
      $this->arguments[$arg->getName()] = $arg->isArray() ? array($token) : $token;
    // If last argument isArray(), append token to last argument.
    } elseif ($this->definition->hasArgument($c - 1) && $this->definition->getArgument($c - 1)->isArray()) {
      $arg = $this->definition->getArgument($c - 1);
      $this->arguments[$arg->getName()][] = $token;
    // Unexpected argument.
    } else {
      throw new RuntimeException('Too many arguments.');
    }
  }

  /**
   * Add short option value.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $shortcut Shortcut option key.
   * @param mixed $value Option value.
   *
   * @throws Freyja\Exceptions\RuntimeException if given option doesn't exist.
   */
  private function addShortOption($shortcut, $value) {
    if (!$this->definition->hasShortcut($shortcut))
      throw new RuntimeException(sprintf('Option "-%s" does not exist.', $shortcut));

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
   * @throws Freyja\Exceptions\RuntimeException if given option doesn't exist.
   */
  private function addLongOption($name, $value) {
    if (!$this->definition->hasOption($name))
      throw new RuntimeException(sprintf('Option "--%s" does not exist.', $name));

    $option = $this->definition->getOption($name);

    // Convert empty values to null.
    // Can't test with `empty()` here, because 0, 0.0, and "0" are considered empty.
    if (!isset($value[0]))
      $value = null;

    if (!is_null($value) && !$option->acceptValue())
      throw new RuntimeException(sprintf('Option "--%s" does not accept a value.', $name));

    if (is_null($value) && $option->acceptValue() && count($this->parsed)) {
      // If option accepts an optional or mandatory argument, let's see if there
      // is one provided.
      $next = array_shift($this->parsed);

      if (isset($next[0]) && '-' !== $next[0] || empty($next))
        $value = $next;
      else
        array_unshift($this->parsed, $next);
    }

    if (null === $value) {
      if ($option->isValueRequired())
        throw new RuntimeException(sprintf('Option "--%s" requires a value.', $name));
      if (!$option->isArray())
        $value = $option->isValueOptional() ? $option->getDefault() : true;
    }

    if ($option->isArray())
      $this->options[$name][] = $value;
    else
      $this->options[$name] = $value;
  }

  /**
   * Retrieve first argument from raw parameters (not parsed).
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Value for the first argument or null otherwise.
   */
  public function getFirstArgument() {
    foreach ($this->tokens as $token) {
      if ($token && '-' === $token[0])
        continue;
      return $token;
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
   * @param string|array $values Value(s) to look for in the raw parameters (can
   * be an array).
   * @param bool $only_params Optional. Only check real parameters, skip those
   * following an and of options (--) signal. Default false.
   *
   * @return bool True if the value is contained in the raw parameters.
   */
  public function hasParameterOption($values, $only_params = false) {
    $values = (array) $values;

    foreach ($this->tokens as $token) {
      if ($only_params && $token === '--')
        return false;
      foreach ($values as $value)
        if ($token === $value || 0 === strpos($token, $value.'='))
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
    $tokens = $this->tokens;

    while (0 < count($tokens)) {
      $token = array_shift($tokens);
      if ($only_params && $token === '--')
        return false;

      foreach ($values as $value) {
        if ($token === $value || 0 === strpos($token, $value.'=')) {
          if (false !== $pos = strpos($token, '='))
            return substr($token, $pos + 1);
          return array_shift($tokens);
        }
      }
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
    $tokens = array_map(array($this, 'toStringCallback'), $this->tokens);

    return join(' ', $tokens);
  }

  /**
   * Callback for __toString() magic method.
   *
   * Using `$this` inside a closure is not supported in PHP 5.3 and raises a
   * PHP Fatal Error: Using $this when not in object context.
   *
   * In order to provide support for PHP 5.3, we use this method as a callback
   * for the `array_map()` called in ArgvInput::__toString().
   *
   * @link(Issue #17, https://github.com/SqueezyWeb/freyja-cli/issues/17)
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $token Token to escape.
   *
   * @return string Escaped token.
   */
  private function toStringCallback($token) {
    if (preg_match('/^(-[^=]+=)(.+)/', $token, $match))
      return $match[1].$this->escapeToken($match[2]);
    if ($token && $token[0] !== '-')
      return $this->escapeToken($token);
    return $token;
  }
}
