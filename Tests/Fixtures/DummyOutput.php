<?php
/**
 * Freyja CLI Descriptor Command.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Fixtures;

use Freyja\CLI\Output\BufferedOutput;

/**
 * Dummy output.
 *
 * @package Freyja\CLI\Tests\Fixtures
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class DummyOutput extends BufferedOutput {
  /**
   * Retrieve logs.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getLogs() {
    $logs = array();
    foreach (explode("\n", trim($this->fetch())) as $message) {
      preg_match('/^\[(.*)\] (.*)/', $message, $matches);
      $logs[] = sprintf('%s %s', $matches[1], $matches[2]);
    }

    return $logs;
  }
}
