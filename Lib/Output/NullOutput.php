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
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function setDecorated($decorated) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function isDecorated() {
    return false;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function writeln($messages, $options = self::OUTPUT_NORMAL) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
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
