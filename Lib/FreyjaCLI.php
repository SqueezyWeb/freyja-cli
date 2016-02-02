<?php
/**
 * Freyja CLI main class file.
 *
 * @package SqueezyWeb\Freyja\CLI
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace SqueezyWeb\Freyja\CLI;
use RuntimeException;
use Psr\Log\LoggerInterface;

/**
 * Freyja CLI main class.
 *
 * @package SqueezyWeb\Freyja\CLI
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 0.1.0
 */
class FreyjaCLI {
  /**
   * Built-in commands.
   *
   * @since 0.1.0
   * @access protected
   * @var array
   */
  protected $commands = array();

  /**
   * Commands provided by other packages.
   *
   * @since 0.1.0
   * @access protected
   * @var array
   */
  protected $external_commands = array();

  /**
   * Internal logger instance.
   *
   * This MUST be a PSR-6 compliant logger instance.
   *
   * @since 0.1.0
   * @access protected
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Class constructor.
   *
   * @since 0.1.0
   * @access public
   *
   * @param
   */

  /**
   * Set logger.
   *
   * @since 0.1.0
   * @access public
   *
   * @param \Psr\Log\LoggerInterface $logger Logger instance.
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * Get external commands list.
   *
   * Retrieves a list of commands provided by other packages by looking at
   * the "extra" section of dependencies in composer.lock.
   *
   * @since 0.1.0
   * @access private
   * @static
   *
   * @return array List of external commands specifications.
   */
  private static function getExternalCommands() {
    $commands = array();

    $composer_lock = getcwd().'/composer.lock';
    // Return early if composer.lock does not exist.
    if (!file_exists($composer_lock)) {
      // File a warning to the logger.
      $this->logger->warning('File composer.lock not found.');
      return $commands;
    }

    $composer = json_decode($composer_lock);

    // Return early if the composer.lock file is malformed.
    if (is_null($composer)) {
      $this->logger->warning('File composer.lock could not be decoded.');
      return $commands;
    }
    // Return early if composer.lock does not contain any packages.
    if (!isset($composer['packages']))
      return $commands;

    // Look at each package to see if they expose any Freyja CLI commands.
    foreach ($composer['packages'] as $package) {
      if (
        array_key_exists('extra', $package) &&
        array_key_exists('freyja-cli', $package['extra']) &&
        array_key_exists('commands', $package['extra']['freyja-cli'])
      )
        $commands = array_merge($commands, $package['extra']['freyja-cli']['commands']);
    }

    return $commands;
  }
}
