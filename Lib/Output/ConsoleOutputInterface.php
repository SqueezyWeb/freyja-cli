<?php
/**
 * Freyja CLI Console Output definition.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

/**
 * Interface implemented by ConsoleOutput class.
 *
 * Adds information about stderr output stream.
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface ConsoleOutputInterface extends OutputInterface {
  /**
   * Retrieve OutputInterface for errors.
   *
   * @since 1.0.0
   * @access public
   *
   * @return OutputInterface
   */
  public function getErrorOutput();

  /**
   * Set OutputInterface used for errors.
   *
   * @since 1.0.0
   * @access public
   *
   * @param OutputInterface $error
   */
  public function setErrorOutput(OutputInterface $error);
}
