<?php
/**
 * Freyja CLI Stream Output.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

use Freyja\Exceptions\InvalidArgumentException;
use Freyja\Exceptions\RuntimeException;
use Freyja\CLI\Formatters\OutputFormatterInterface;

/**
 * Write output to given stream.
 *
 * Usage:
 *
 * $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * As `StreamOutput` can use any stream, you can also use a file:
 *
 * $output = new StreamOutput(fopen('/path/to/output.log', 'a', false));
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class StreamOutput extends Output {
  /**
   * Output stream.
   *
   * @since 1.0.0
   * @access private
   * @var resource
   */
  private $stream;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param resource $stream Stream resource.
   * @param int $verbosity Optional. Verbosity level (one of the VERBOSITY
   * constants in OutputInterface). Default OutputInterface::VERBOSITY_NORMAL.
   * @param bool|null $decorated Optional. Whether to decorate messages (null
   * for auto-guessing). Default null.
   * @param OutputFormatterInterface|null Optional. Output formatter instance.
   * Null to use default OutputFormatter. Default null.
   *
   * @throws InvalidArgumentException if the first argument is not a real stream.
   */
  public function __construct($stream, $verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null) {
    if (!is_resource($stream) || 'stream' !== get_resource_type($stream))
      throw new InvalidArgumentException('The StreamOutput class needs a stream as its first argument.');

    $this->stream = $stream;

    if (null === $decorated)
      $decorated = $this->hasColorSupport();

    parent::__construct($verbosity, $decorated, $formatter);
  }

  /**
   * Retrieve stream attached to this StreamOutput instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @return resource Stream resource.
   */
  public function getStream() {
    return $this->stream;
  }

  /**
   * {@inheritdoc}
   *
   * @param string $message Message to write.
   * @param bool $newline Whether to print newline at the end of $message.
   */
  protected function doWrite($message, $newline) {
    if (false === @fwrite($this->stream, $message.($newline ? PHP_EOL : '')))
      // Should never happen.
      throw new RuntimeException('Unable to write to output.');

    fflush($this->stream);
  }

  /**
   * Whether stream supports colorization.
   *
   * Colorization is disabled if not supported by the stream:
   * - Windows without Ansicon, ConEmu, or Mintty
   * - non tty consoles
   *
   * @since 1.0.0
   * @access protected
   *
   * @return bool True if the stream supports colorization, false otherwise.
   */
  protected function hasColorSupport() {
    if (DIRECTORY_SEPARATOR === '\\')
      return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI') || 'xterm' === getenv('TERM');

    return function_exists('posix_isatty') && @posix_isatty($this->stream);
  }
}
