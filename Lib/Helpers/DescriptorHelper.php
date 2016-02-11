<?php
/**
 * Freyja CLI Descriptor Helper.
 *
 * @package Freyja\CLI\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Helpers;

use Freyja\CLI\Descriptors\DescriptorInterface;
use Freyja\CLI\Descriptors\TextDescriptor;
use Freyja\CLI\Output\OutputInterface;
use Freyja\Exceptions\InvalidArgumentException;

/**
 * Helper class to describe objects in various formats.
 *
 * @package Freyja\CLI\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class DescriptorHelper extends Helper {
  /**
   * Available descriptors.
   *
   * @since 1.0.0
   * @access private
   * @var DescriptorInterface[]
   */
  private $descriptors = array();

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function __construct() {
    $this->register('txt', new TextDescriptor);
  }

  /**
   * Describe object if supported.
   *
   * Available options are:
   * - format: string, output format name
   * - raw_text: boolean, sets output type as raw
   *
   * @since 1.0.0
   * @access public
   *
   * @param OutputInterface $output
   * @param object $object Object to output
   * @param array $options Optional. Array of options. Default empty.
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if given format is not
   * supported.
   */
  public function describe(OutputInterface $output, $object, array $options = array()) {
    $options = array_merge(array(
      'raw_text' => false,
      'format' => 'txt'
    ), $options);

    if (!isset($this->descriptors[$options['format']]))
      throw new InvalidArgumentException(sprintf('Unsupported format "%s"', $options['format']));

    $descriptor = $this->descriptors[$options['format']];
    $descriptor->describe($output, $object, $options);
  }

  /**
   * Register descriptor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $format
   * @param DescriptorInterface $descriptor
   *
   * @return DescriptorHelper
   */
  public function register($format, DescriptorInterface $descriptor) {
    $this->descriptors[$format] = $descriptor;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'descriptor';
  }
}
