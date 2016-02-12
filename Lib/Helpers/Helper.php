<?php
/**
 * Freyja CLI Helper class.
 *
 * @package Freyja\CLI\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Helpers;

use Freyja\CLI\Formatters\OutputFormatterInterface;

/**
 * Base class for all helper classes.
 *
 * @package Freyja\CLI\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
abstract class Helper implements HelperInterface {
  /**
   * Associated Helper Set.
   *
   * @since 1.0.0
   * @access protected
   * @var HelperSet
   */
  protected $helper_set = null;

  /**
   * Set helper set associated with this helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @param HelperSet $helper_set Optional. HelperSet instance. Default null.
   */
  public function setHelperSet(HelperSet $helper_set = null) {
    $this->helper_set = $helper_set;
  }

  /**
   * Retrieve helper set associated with this helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @return HelperSet HelperSet instance.
   */
  public function getHelperSet() {
    return $this->helper_set;
  }

  /**
   * Retrieve length of string, using mb_strwidth if available.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param string $string String to check its length.
   *
   * @return int Length of the string.
   */
  public static function strlen($string) {
    if (false === $encoding = mb_detect_encoding($string, null, true))
      return strlen($string);

    return mb_strwidth($string, $encoding);
  }

  /**
   * Format time.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param int $secs Time in seconds.
   *
   * @return string Formatted time.
   */
  public static function formatTime($secs) {
    static $time_formats = array(
      array(0, '< 1 sec'),
      array(2, '1 sec'),
      array(59, 'secs', 1),
      array(60, '1 min', 60),
      array(3600, 'mins', 60),
      array(5400, '1 hr'),
      array(86400, 'hrs', 3600),
      array(129600, '1 day'),
      array(604800, 'days', 86400)
    );

    foreach ($time_formats as $format) {
      if ($secs >= $format[0])
        continue;
      if (2 == count($format))
        return $format[1];
      return ceil($secs / $format[2]).' '.$format[1];
    }
  }

  /**
   * Format memory.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param int $memory Memory in bytes.
   *
   * @return string Formatted memory.
   */
  public static function formatMemory($memory) {
    if ($memory >= 1024 * 1024 * 1024)
      return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
    if ($memory >= 1024 * 1024)
      return sprintf('%.1f MiB', $memory / 1024 / 1024);
    if ($memory >= 1024)
      return sprintf('%d KiB', $memory, $memory / 1024);
    return sprintf('%d B', $memory);
  }

  /**
   * Calculate length of string without decoration.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @param OutputFormatterInterface $formatter
   * @param string $string
   *
   * @return int String length without decoration.
   */
  public static function strlenWithoutDecoration(OutputFormatterInterface $formatter, $string) {
    $is_decorated = $formatter->isDecorated();
    $formatter->setDecorated(false);
    // Remove <...> formatting.
    $string = $formatter->format($string);
    // Remove already formatted characters.
    $string = preg_replace('/\033\[[^m]*m/', '', $string);
    $formatter->setDecorated($is_decorated);

    return self::strlen($string);
  }

  /**
   * {@inheritdoc}
   */
  abstract function getName();
}
