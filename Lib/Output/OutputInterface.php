<?php
/**
 * Freyja CLI Output Public API definition.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

use Freyja\CLI\Formatters\OutputFormatterInterface;

/**
 * Interface implemented by all Output classes.
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface OutputInterface {
  /**
   * Verbosity.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const VERBOSITY_QUIET = 16;
  const VERBOSITY_NORMAL = 32;
  const VERBOSITY_VERBOSE = 64;
  const VERBOSITY_VERY_VERBOSE = 128;
  const VERBOSITY_DEBUG = 256;

  /**
   * Output mode.
   *
   * @since 1.0.0
   * @access public
   * @var int
   */
  const OUTPUT_NORMAL = 1;
  const OUTPUT_RAW = 2;
  const OUTPUT_PLAIN = 4;

  /**
   * Write message to the output.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $messages The message as an array of lines or a single string.
   * @param string $type Optional. Type of message to output. Default null.
   * @param bool $newline Optional. Whether to add a new line. Default false.
   * @param int $options Optional. Bitmask of options (one of the OUTPUT or
   * VERBOSE constants), 0 is considered the same as self::OUTPUT_NORMAL |
   * self::VERBOSITY_NORMAL.
   */
  public function write($messages, $type = null, $newline = false, $options = 0);

  /**
   * Write message to the output and add newline at the end.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $messages Message as an array of lines or a single string.
   * @param string $type Optional. Type of message to output. Default null.
   * @param int $options Optional. Bitmask of options (one of the OUTPUT or
   * VERBOSE constants), 0 is considered the same as self::OUTPUT_NORMAL |
   * self::VERBOSITY_NORMAL.
   */
  public function writeln($messages, $type = null, $options = 0);

  /**
   * Set verbosity of the output.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $level Level of verbosity (one of the VERBOSITY constants).
   */
  public function setVerbosity($level);

  /**
   * Get current verbosity of the output.
   *
   * @since 1.0.0
   * @access public
   *
   * @return int Current level of verbosity (one of the VERBOSITY constants).
   */
  public function getVerbosity();

  /**
   * Whether verbosity is quiet (-q).
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if verbosity is set to VERBOSITY_QUIET, false otherwise.
   */
  public function isQuiet();

  /**
   * Whether verbosity is verbose (-v).
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if verbosity is set to VERBOSITY_VERBOSE, false otherwise.
   */
  public function isVerbose();

  /**
   * Whether verbosity is very verbose (-vv).
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if verbosity is set to VERBOSITY_VERY_VERBOSE, false otherwise.
   */
  public function isVeryVerbose();

  /**
   * Whether verbosity is debug (-vvv).
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if verbosity is set to VERBOSITY_DEBUG, false otherwise.
   */
  public function isDebug();

  /**
   * Set the decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $decorated Whether to decorate messages.
   */
  public function setDecorated($decorated);

  /**
   * Retrieve decorated flag.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool True if the output will decorate messages, false otherwise.
   */
  public function isDecorated();

  /**
   * Set output formatter.
   *
   * @since 1.0.0
   * @access public
   *
   * @param OutputFormatterInterface $formatter
   */
  public function setFormatter(OutputFormatterInterface $formatter);

  /**
   * Retrieve current output formatter instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @return OutputFormatterInterface
   */
  public function getFormatter();
}
