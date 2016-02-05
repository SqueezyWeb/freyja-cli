<?php
/**
 * Freyja CLI Input Aware Interface.
 *
 * @package Freyja\CLI\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Input;

/**
 * This interface SHOULD be implemented by classes that depend on CLI Input.
 *
 * @package Freyja\CLI\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface InputAwareInterface {
  /**
   * Set CLI Input.
   *
   * @since 1.0.0
   * @access public
   *
   * @param InputInterface $input
   */
  public function setInput(InputInterface $input);
}
