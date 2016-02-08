<?php
/**
 * Freyja CLI Invalid Option Exception.
 *
 * @package Freyja\CLI\Exceptions
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Exceptions;

/**
 * Exception when input option is invalid.
 *
 * @package Freyja\CLI\Exceptions
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class InvalidOptionException extends Freyja\Exceptions\InvalidArgumentException {
  /**
   * Return exception when option does not exist.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param string $name Option name.
   *
   * @return InvalidOptionException
   */
  public static function notFound($name) {
    $message = '';
    if (1 === strlen($name))
      $message = 'Option "-%s" does not exist.';
    else
      $message = 'Option "--%s" does not exist.';

    return new self(sprintf($message, $name));
  }

  /**
   * Return exception when option requires value and it is not given.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param string $name Option name.
   *
   * @return InvalidOptionException
   */
  public static function valueRequired($name) {
    return new self(sprintf('Option "--%s" requires a value', $name));
  }
}
