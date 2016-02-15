<?php
/**
 * Freyja CLI Null Output.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

use Freyja\CLI\Formatters\OutputFormatter;
use Freyja\CLI\Formatters\OutputFormatterInterface;

/**
 * Suppress all output.
 *
 * Usage:
 *
 * $output = new NullOutput();
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class NullOutput extends Output {
  /**
   * Set output formatter.
   *
   * @since 1.0.0
   * @access public
   *
   * @param OutputFormatterInterface $formatter
   */
  public function setFormatter(OutputFormatterInterface $formatter) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function getFormatter() {
    // To comply with the interface we must return a OutputFormatterInterface.
    return new OutputFormatter;
  }

  /**
   * Set the decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $decorated Whether to decorate messages.
   */
  public function setDecorated($decorated) {
    // Do nothing.
  }

  /**
   * Retrieve decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if the output will decorate messages, false otherwise.
   */
  public function isDecorated() {
    return false;
  }

  /**
   * Set verbosity of the output.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $level Level of verbosity (one of the VERBOSITY constants).
   */
  public function setVerbosity($level) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function getVerbosity() {
    return self::VERBOSITY_QUIET;
  }

  /**
   * {@inheritdoc}
   */
  public function isQuiet() {
    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function isVerbose() {
    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function isVeryVerbose() {
    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function isDebug() {
    return false;
  }

  /**
   * Write message to the output and add newline at the end.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $messages Message as an array of lines or a single string.
   * @param int $options Optional. Bitmask of options (one of the OUTPUT or
   * VERBOSE constants), 0 is considered the same as self::OUTPUT_NORMAL |
   * self::VERBOSITY_NORMAL.
   */
  public function writeln($messages, $options = self::OUTPUT_NORMAL) {
    // Do nothing.
  }

  /**
   * Write message to the output.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $messages The message as an array of lines or a single string.
   * @param bool $newline Optional. Whether to add a new line. Default false.
   * @param int $options Optional. Bitmask of options (one of the OUTPUT or
   * VERBOSE constants), 0 is considered the same as self::OUTPUT_NORMAL |
   * self::VERBOSITY_NORMAL.
   */
  public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  protected function doWrite($message, $newline) {
    // Do nothing.
  }
}
