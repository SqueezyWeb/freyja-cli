<?php
/**
 * Freyja CLI Input Definition.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

use Freyja\CLI\Exceptions\InvalidArgumentException;
use Freyja\CLI\Exceptions\InvalidOptionException;
use Freyja\Exceptions\LogicException;

/**
 * Representation of a set of a valid command line arguments and options.
 *
 * @example
 * $definition = new Definition(array(
 *  new Argument('name', Argument::REQUIRED),
 *  new Option('foo', 'f', Option::VALUE_REQUIRED)
 * ));
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class Definition {
  /**
   * Input arguments.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $arguments;

  /**
   * Number of required arguments.
   *
   * @since 1.0.0
   * @access private
   * @var int
   */
  private $required_count;

  /**
   * Whether current Definition has an array argument.
   *
   * @since 1.0.0
   * @access private
   * @var bool
   */
  private $has_array_argument = false;

  /**
   * Whether current Definition has any optional argument.
   *
   * @since 1.0.0
   * @access public
   * @var bool
   */
  private $has_optional;

  /**
   * Input options.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $options;

  /**
   * Option shortcuts.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $shortcuts;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $definitions Optional. Array of Argument and Option instances.
   * Default empty.
   */
  public function __construct(array $definition = array()) {
    $this->setDefinition($definition);
  }

  /**
   * Set definition of the input.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $defnition Definition array.
   */
  public function setDefinition(array $definition) {
    $arguments = array();
    $options = array();
    foreach ($definition as $item)
      if ($item instanceof Option)
        $options[] = $item;
      elseif ($item instanceof Argument)
        $arguments[] = $item;

    $this->setArguments($arguments);
    $this->setOptions($options);
  }

  /**
   * Set Argument objects.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Argument[] $arguments Optional. Array of Argument objects. Default
   * empty.
   */
  public function setArguments(array $arguments = array()) {
    array_walk($arguments, array($this, 'addArgument'));
  }

  /**
   * Add Argument object.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Argument $argument Argument object.
   *
   * @throws Freyja\Exceptions\LogicException if incorrect argument is given.
   */
  public function addArgument(Argument $argument) {
    if (isset($this->arguments[$argument->getName()]))
      throw new LogicException(sprintf('An argument with name "%s" already exists.', $argument->getName()));
    if ($this->has_array_argument)
      throw new LogicException('Cannot add an argument after an array argument.');
    if ($argument->isRequired() && $this->hasOptional)
      throw new LogicException('Cannot add a required argument after an optional one.');

    if ($argument->isArray())
      $this->has_array_argument = true;

    if ($argument->isRequired())
      ++$this->required_count;
    else
      $this->has_optional = true;

    $this->arguments[$argument->getName()] = $argument;
  }

  /**
   * Retrieve Argument by name or position.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|int $name Argument name or position.
   * @return Argument Argument object.
   *
   * @throws Freyja\CLI\Exceptions\InvalidArgumentException if given argument does
   * not exist.
   */
  public function getArgument($name) {
    if (!$this->hasArgument($name))
      throw InvalidArgumentException::notFound($name);

    $arguments = is_int($name) ? array_values($this->arguments) : $this->arguments;

    return $arguments[$name];
  }

  /**
   * Whether Argument object exists by name or position.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|int $name Argument name or position.
   *
   * @return bool True if Argument object exists, false otherwise.
   */
  public function hasArgument($name) {
    $arguments = is_int($name) ? array_values((array) $this->arguments) : $this->arguments;

    return isset($arguments[$name]);
  }

  /**
   * Retrieve array of Argument objects.
   *
   * @since 1.0.0
   * @access public
   *
   * @return Argument[] Array of Argument objects.
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * Retrieve number of arguments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return int Number of arguments.
   */
  public function getArgumentCount() {
    return $this->has_array_argument ? PHP_INT_MAX : count($this->arguments);
  }

  /**
   * Retrieve number of required arguments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return int Number of required arguments.
   */
  public function getArgumentRequiredCount() {
    return $this->required_count;
  }

  /**
   * Retrieve arguments default values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Associative array `name => default` of argument defaults.
   */
  public function getArgumentDefaults() {
    $values = array();
    foreach ($this->arguments as $argument)
      $values[$argument->getName()] = $argument->getDefault();

    return $values;
  }

  /**
   * Set Option objects.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Option[] $options Default. Array of Option objects. Default empty.
   */
  public function setOptions(array $options = array()) {
    $this->options = array();
    $this->shortcuts = array();
    $this->addOptions($options);
  }

  /**
   * Add array of Option objects.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Option[] $options Optional. Array of Option objects. Default empty.
   */
  public function addOptions(array $options = array()) {
    array_walk($options, array($this, 'addOption'));
  }

  /**
   * Add Option object.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Option $option Option object.
   *
   * @throws Freyja\Exceptions\LogicException if given option already exists.
   */
  public function addOption(Option $option) {
    if (isset($this->options[$option->getName()]) && !$option->equals($this->options[$option->getName()]))
      throw new LogicException(sprintf('An option named "%s" already exists.', $option->getName()));

    if ($option->getShortcut())
      foreach (explode('|', $option->getShortcut()) as $shortcut)
        if (isset($this->shortcuts[$shortcut]) && !$option->equals($this->options[$this->shortcuts[$shortcut]]))
          throw new LogicException(sprintf('An option with shortcut "%s" already exists.', $shortcut));

    $this->options[$option->getName()] = $option;
    if ($option->getShortcut())
      foreach (explode('|', $option->getShortcut()) as $shortcut)
        $this->shortcuts[$shortcut] = $option->getName();
  }

  /**
   * Retrieve Option by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return Option Option object.
   *
   * @throws Freyja\CLI\Exceptions\InvalidOptionException if given option doesn't
   * exist.
   */
  public function getOption($name) {
    if (!$this->hasOption($name))
      throw InvalidOptionException::notFound($name);

    return $this->options[$name];
  }

  /**
   * Whether Option object exists by name.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @return bool True if the Option object exists, false otherwise.
   */
  public function hasOption($name) {
    return isset($this->options[$name]);
  }

  /**
   * Retrieve array of Option objects.
   *
   * @since 1.0.0
   * @access public
   *
   * @return Option[] Array of Option objects.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Whether Option object exists by shortcut.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option shortcut.
   * @return bool True if the Option object exists, false otherwise.
   */
  public function hasShortcut($name) {
    return isset($this->shortcuts[$name]);
  }

  /**
   * Retrieve Option by shortcut.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $shortcut Shortcut name.
   * @return Option Option object.
   */
  public function getOptionByShortcut($shortcut) {
    return $this->getOption($this->shortcutToName($shortcut));
  }

  /**
   * Retrieve options default values.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Associative array of  `$name => $default` options defaults.
   */
  public function getOptionDefaults() {
    $values = array();
    foreach ($this->options as $option)
      $values[$option->getName()] = $option->getDefault();

    return $values;
  }

  /**
   * Retrieve option name given a shortcut.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $shortcut The shortcut.
   * @return string Option name.
   *
   * @throws Freyja\CLI\Exceptions\InvalidOptionException if given option does
   * not exist.
   */
  private function shortcutToName($shortcut) {
    if (!isset($this->shortcuts[$shortcut]))
      throw InvalidOptionException::notFound($shortcut);

    return $this->shortcuts[$shortcut];
  }

  /**
   * Retrieve synopsis.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $short Optional. Whether to return the short version (with
   * options folded) or not. Default false.
   * @return string The synopsis.
   */
  public function getSynopsis($short = false) {
    $elements = array();

    if ($short && $this->getOptions()) {
      $elements[] = '[options]';
    } elseif (!$short) {
      foreach ($this->getOptions() as $option) {
        $value = '';
        if ($option->acceptValue()) {
          $value = sprintf(
            ' %s%s%s',
            $option->isValueOptional() ? '[' : '',
            strtoupper($option->getName()),
            $option->isValueOptional() ? ']' : ''
          );
        }

        $shortcut = $option->getShortcut() ? sprintf('-%s|', $option->getShortcut()) : '';
        $elements[] = sprintf('[%s--%s%s]', $shortcut, $option->getName(), $value);
      }
    }

    if (count($elements) && $this->getArguments())
      $elements[] = '[--]';

    foreach ($this->getArguments() as $argument) {
      $element = '<'.$argument->getName().'>';
      if (!$argument->isRequired())
        $element = '['.$element.']';
      elseif ($argument->isArray())
        $element = $element.' ('.$element.')';

      if ($argument->isArray())
        $element .= '...';

      $elements[] = $element;
    }

    return join(' ', $elements);
  }
}
