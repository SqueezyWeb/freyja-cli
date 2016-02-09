<?php
/**
 * Freyja CLI Console Output.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

use Freyja\CLI\Formatters\OutputFormatterInterface;

/**
 * Default class for all CLI output. Uses STDOUT.
 *
 * This class is a convenient wrapper around `StreamOutput`.
 *
 * $output = new ConsoleOutput;
 *
 * This is equivalent to:
 *
 * $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ConsoleOutput extends StreamOutput implements ConsoleOutputInterface {
  /**
   * Error stream.
   *
   * @since 1.0.0
   * @access private
   * @var StreamOutput
   */
  private $stderr;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $verbosity Optional. Verbosity level (one of the VERBOSITY
   * constants in OutputInterface). Default self::VERBOSITY_NORMAL.
   * @param bool|null $decorated Optional. Whether to decorate messages (null
   * for auto-guessing). Default null.
   * @param Freyja\CLI\Formatters\OutputFormatterInterface|null Optional. Output
   * formatter instance (null to use default OutputFormatter). Default null.
   */
  public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null) {
    parent::__construct($this->openOutputStream(), $verbosity, $decorated, $formatter);

    $actual_decorated = $this->isDecorated();
    $this->stderr = new StreamOutput($this->openErrorStream(), $verbosity, $decorated, $this->getFormatter);

    if (is_null($decorated))
      $this->setDecorated($actual_decorated && $this->stderr->isDecorated());
  }

  /**
   * {@inheritdoc}
   */
  public function setDecorated($decorated) {
    parent::setDecorated($decorated);
    $this->stderr->setDecorated($decorated);
  }

  /**
   * {@inheritdoc}
   */
  public function setFormatter(OutputFormatterInterface $formatter) {
    parent::setFormatter($formatter);
    $this->stderr->setFormatter($formatter);
  }

  /**
   * {@inheritdoc}
   */
  public function setVerbosity($level) {
    parent::setVerbosity($level);
    $this->stderr->setVerbosity($level);
  }

  /**
   * {@inheritdoc}
   */
  public function getErrorOutput() {
    return $this->stderr;
  }

  /**
   * {@inheritdoc}
   */
  public function setErrorOutput(OutputInterface $error) {
    $this->stderr = $error;
  }

  /**
   * Whether current environment supports writing console output to STDOUT.
   *
   * @since 1.0.0
   * @access protected
   *
   * @return bool
   */
  protected function hasStdoutSupport() {
    return false === $this->isRunningOS400();
  }

  /**
   * Whether current environment supports writing console output to STDERR.
   *
   * @since 1.0.0
   * @access protected
   *
   * @return bool
   */
  protected function hasStderrSupport() {
    return false === $this->isRunningOS400();
  }

  /**
   * Whether current executing environment is IBM iSeries (OS400).
   *
   * OS400 doesn't properly convert character-encodings between ASCII to EBCDIC.
   *
   * @since 1.0.0
   * @access private
   *
   * @return bool
   */
  private function isRunningOS400() {
    $checks = array(
      function_exists('php_uname') ? php_uname('s') : '',
      getenv('OSTYPE'),
      PHP_OS
    );

    return false !== stripos(implode(';', $checks), 'OS400');
  }

  /**
   * Open output stream.
   *
   * @since 1.0.0
   * @access private
   *
   * @return resource Output stream.
   */
  private function openOutputStream() {
    return $this->openStream('stdout');
  }

  /**
   * Open error stream.
   *
   * @since 1.0.0
   * @access private
   *
   * @return resource Error stream.
   */
  private function openErrorStream() {
    return $this->openStream('stderr');
  }

  /**
   * Open stream.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $type Stream type. One of 'stdout', 'stderr'.
   * @return resource Stream.
   */
  private function openStream($type) {
    $stream = $this->{'has'.ucfirst(strtolower($type)).'Support'}() ? 'php://'.strtolower($type) : 'php://output';

    return fopen($stream, 'w');
  }
}
