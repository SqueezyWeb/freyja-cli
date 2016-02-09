<?php
/**
 * Freyja CLI Invalid Argument Exception.
 *
 * @package Freyja\CLI\Exceptions
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Exceptions;

use Freyja\Exceptions\InvalidArgumentException as InvArgExcp;

/**
 * Exception when input argument is invalid.
 *
 * @package Freyja\CLI\Exceptions
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class InvalidArgumentException extends InvArgExcp {
  /**
   * Return exception when argument does not exist.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param string $name Argument name.
   *
   * @return InvalidArgumentException
   */
  public static function notFound($name) {
    return new self(sprintf('Argument "%s" does not exist.', $name));
  }
}
