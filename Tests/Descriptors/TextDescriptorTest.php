<?php
/**
 * Freyja CLI Text Descriptor Test.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Descriptors;

use Freyja\CLI\Descriptors\TextDescriptor;

/**
 * Test TextDescriptor class.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class TextDescriptorTest extends AbstractDescriptorTest {
  /**
   * {@inheritdoc}
   */
  protected function getDescriptor() {
    return new TextDescriptor;
  }

  /**
   * {@inheritdoc}
   */
  protected function getFormat() {
    return 'txt';
  }
}
