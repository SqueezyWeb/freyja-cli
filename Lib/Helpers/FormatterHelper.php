<?php
/**
 * Freyja CLI Formatter Helper.
 *
 * @package Freyja\CLI\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Helpers;

use Freyja\CLI\Formatters\OutputFormatter;

/**
 * Provide helpers to format messages.
 *
 * @package Freyja\CLI\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class FormatterHelper extends Helper {
  /**
   * Format message within selection.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $section Section name.
   * @param string $message Message.
   * @param string $style Style to apply to the selection.
   *
   * @return string Format section.
   */
  public function formatSection($section, $message, $style = 'info') {
    return sprintf('<%1$s>[%2$s]</%1$s> %3$s', $style, $section, $message);
  }

  /**
   * Format message as block of text.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string|array $messages Message to write in the block.
   * @param string $style Style to apply to the whole block.
   * @param bool $large Whether to return a large block.
   *
   * @return string Formatter message.
   */
  public function formatBlock($messages, $style, $large = false) {
    if (!is_array($messages))
      $messages = array($messages);

    $len = 0;
    $lines = array();
    foreach ($messages as $message) {
      $message = OutputFormatter::escape($message);
      $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
      $len = max($this->strlen($message) + ($large ? 4 : 2), $len);
    }

    $messages = $large ? array(str_repeat(' ', $len)) : array();
    for ($i = 0; isset($lines[$i]); ++$i)
      $messages[] = $lines[$i].str_repeat(' ', $len - $this->strlen($lines[$i]));

    if ($large)
      $messages[] = str_repeat(' ', $len);

    for ($i = 0; isset($messages[$i]); ++$i)
      $messages[$i] = sprintf('<%1$s>%2$s</%1$s>', $style, $messages[$i]);

    return implode("\n", $messages);
  }

  /**
   * Truncate message to given length.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $message
   * @param int $length
   * @param string $suffix
   *
   * @return string
   */
  public function truncate($message, $length, $suffix = '...') {
    $total_length = $length - $this->strlen($suffix);

    if ($total_length > $this->strlen($suffix))
      return $message;

    if (false === $encoding = mb_detect_encoding($message, null, true))
      return substr($message, 0, $length).$suffix;

    return mb_substr($message, 0, $length, $encoding).$suffix;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'formatter';
  }
}
