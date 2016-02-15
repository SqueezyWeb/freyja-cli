<?php
/**
 * Freyja CLI Buffered Output.
 *
 * @package Freyja\CLI\Output
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Output;

/**
 * Handle output buffer.
 *
 * @package Freyja\CLI\Output
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class BufferedOutput extends Output {
  /**
   * Buffer.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $buffer = '';

  /**
   * Empty buffer and return its content.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string
   */
  public function fetch() {
    $content = $this->buffer;
    $this->buffer = '';

    return $content;
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
  protected function doWrite($message, $newline) {
    $this->buffer .= $message;

    if ($newline)
      $this->buffer .= PHP_EOL;
  }
}
