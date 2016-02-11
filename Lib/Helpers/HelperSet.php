<?php
/**
 * Freyja CLI Helper Set.
 *
 * @package Freyja\CLI\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Helpers;

use Freyja\CLI\Commands\Command;
use Freyja\Exceptions\InvalidArgumentException;

/**
 * Represents a set of helpers to be used with a command.
 *
 * @package Freyja\CLI\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class HelperSet implements \IteratorAggregate {
  /**
   * List of helpers.
   *
   * @since 1.0.0
   * @access private
   * @var HelperInterface[]
   */
  private $helpers = array();

  /**
   * Associated command.
   *
   * @since 1.0.0
   * @access private
   * @var Freyja\CLI\Commands\Command
   */
  private $command;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param HelperInterface[] $helpers Optional. Array of helper instances. Default null.
   */
  public function __construct(array $helpers = array()) {
    foreach ($helpers as $alias => $helper)
      $this->set($helper, is_int($alias) ? null : $alias);
  }

  /**
   * Set helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @param HelperInterface $helper Helper instance.
   * @param string $alias Optional. Helper alias. Default null.
   */
  public function set(HelperInterface $helper, $alias = null) {
    $this->helpers[$helper->getName()] = $helper;

    if (!is_null($alias))
      $this->helpers[$alias] = $helper;

    $helper->setHelperSet($this);
  }

  /**
   * Whether helper is defined.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Helper name.
   *
   * @return bool True if the helper is defined, false otherwise.
   */
  public function has($name) {
    return isset($this->helpers[$name]);
  }

  /**
   * Retrieve helper value.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Helper name.
   *
   * @return HelperInterface Helper instance.
   *
   * @throws Freyja\CLI\Exceptions\InvalidArgumentException if helper is not
   * defined.
   */
  public function get($name) {
    if (!$this->has($name))
      throw new InvalidArgumentException(sprintf('Helper "%s" is not defined.', $name));

    return $this->helpers[$name];
  }

  /**
   * Set command associated with this helper set.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Command $command Optional. Command instance. Default null.
   */
  public function setCommand(Command $command = null) {
    $this->command = $command;
  }

  /**
   * Retrieve command associated with this helper set.
   *
   * @since 1.0.0
   * @access public
   *
   * @return Command Command instance
   */
  public function getCommand() {
    return $this->command;
  }

  /**
   * Retrieve iterator.
   *
   * @since 1.0.0
   * @access public
   *
   * @return \ArrayIterator
   */
  public function getIterator() {
    return new \ArrayIterator($this->helpers);
  }
}
