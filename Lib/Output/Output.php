<?php
/**
 * Freyja CLI Output base class.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

use Freyja\CLI\Formatters\OutputFormatterInterface;
use Freyja\CLI\Formatters\OutputFormatter;

/**
 * Base class for output classes.
 *
 * There are five levels of verbosity:
 * - normal: no option passed (normal output)
 * - verbose: -v (more output)
 * - very verbose: -vv (highly extended output)
 * - debug: -vvv (all debug output)
 * - quiet: -q (no output)
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 * @abstract
 */
abstract class Output implements OutputInterface {
  /**
   * Verbosity level.
   *
   * @since 1.0.0
   * @access private
   * @var int
   */
  private $verbosity;

  /**
   * Output formatter.
   *
   * @since 1.0.0
   * @access private
   * @var OutputFormatter
   */
  private $formatter;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $verbosity Optional. Verbosity level (one of the VERBOSITY
   * constants in OutputInterface). Default self::VERBOSITY_NORMAL.
   * @param bool $decorated Optional. Whether to decorate messages. Default false.
   * @param Freyja\CLI\Formatters\OutputFormatterInterface|null $formatter
   * Optional. Output formatter instance (null to use default OutputFormatter).
   * Default null.
   */
  public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = false, OutputFormatterInterface $formatter = null) {
    $this->verbosity = null === $verbosity ? self::VERBOSITY_NORMAL : $verbosity;
    $this->formatter = $formatter ?: new OutputFormatter;
    $this->formatter->setDecorated($decorated);
  }

  /**
   * {@inheritdoc}
   */
  public function setFormatter(OutputFormatterInterface $formatter) {
    $this->formatter = $formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormatter() {
    return $this->formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function setDecorated($decorated) {
    $this->formatter->setDecorated($decorated);
  }

  /**
   * {@inheritdoc}
   */
  public function isDecorated() {
    return $this->formatter->isDecorated();
  }

  /**
   * {@inheritdoc}
   */
  public function setVerbosity($level) {
    $this->verbosity = (int) $level;
  }

  /**
   * {@inheritdoc}
   */
  public function getVerbosity() {
    return $this->verbosity;
  }

  /**
   * {@inheritdoc}
   */
  public function isQuiet() {
    return self::VERBOSITY_QUIET === $this->verbosity;
  }

  /**
   * {@inheritdoc}
   */
  public function isVerbose() {
    return self::VERBOSITY_VERBOSE <= $this->verbosity;
  }

  /**
   * {@inheritdoc}
   */
  public function isVeryVerbose() {
    return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
  }

  /**
   * {@inheritdoc}
   */
  public function isDebug() {
    return self::VERBOSITY_DEBUG <= $this->verbosity;
  }

  /**
   * {@inheritdoc}
   */
  public function writeln($messages, $options = self::OUTPUT_NORMAL) {
    $this->write($messages, $type, true, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL) {
    $messages = (array) $messages;

    $types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
    $type = $types & $options ?: self::OUTPUT_NORMAL;

    $verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
    $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;

    if ($verbosity > $this->getVerbosity())
      return;

    foreach ($messages as $message) {
      switch ($type) {
        case self::OUTPUT_NORMAL:
          $message = $this->formatter->format($message);
          break;
        case self::OUTPUT_RAW:
          break;
        case self::OUTPUT_PLAIN:
          $message = strip_tags($this->formatter->format($message));
          break;
      }

      $this->doWrite($message, $newline);
    }
  }

  /**
   * Write message to the output.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param string $message Message to write to the output.
   * @param bool $newline Whether to add a newline or not.
   */
  abstract protected function doWrite($message, $newline);
}
