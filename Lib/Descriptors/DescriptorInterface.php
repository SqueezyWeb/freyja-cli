<?php
/**
 * Freyja CLI Descriptor Interface.
 *
 * @package Freyja\CLI\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Descriptors;

use Freyja\CLI\Output\OutputInterface;

/**
 * Descriptor interface.
 *
 * @package Freyja\CLI\Descriptors
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface DescriptorInterface {
  /**
   * Describe Argument instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @param OutputInterface $output
   * @param object $object
   * @param array $options
   */
  public function describe(OutputInterface $output, $object, array $options = array());
}
