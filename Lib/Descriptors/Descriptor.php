<?php
/**
 * Freyja CLI Base Descriptor.
 *
 * @package Freyja\CLI\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Descriptors;

use Freyja\CLI\FreyjaCLI;
use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Output\OutputInterface;
use Freyja\Exceptions\InvalidArgumentException;

/**
 * Base descriptor class.
 *
 * @package Freyja\CLI\Descriptors
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 * @abstract
 */
abstract class Descriptor implements DescriptorInterface {
  /**
   * Associated Output instance.
   *
   * @since 1.0.0
   * @access protected
   * @var OutputInterface
   */
  protected $output;

  /**
   * {@inheritdoc}
   */
  public function describe(OutputInterface $output, $object, array $options = array()) {
    $this->output = $output;

    switch (true) {
      case $object instanceof Argument:
        $this->describeInputArgument($object, $options);
        break;
      case $object instanceof Option:
        $this->describeInputOption($object, $options);
        break;
      case $object instanceof Definition:
        $this->describeInputDefinition($object, $options);
        break;
      case $object instanceof Command:
        $this->describeCommand($object, $options);
        break;
      default:
        throw new InvalidArgumentException(sprintf('Object of type "%s" is not describable.', get_class($object)));
    }
  }

  /**
   * Write content to output.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param string $content
   * @param bool $decorated Optional. Default false.
   */
  protected function write($content, $decorated = false) {
    $this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
  }

  /**
   * Describe Argument instance.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param Argument $argument
   * @param array $options
   *
   * @return string|mixed
   */
  abstract protected function describeInputArgument(Argument $argument, array $options = array());

  /**
   * Describe Option instance.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param Option $option
   * @param array $options
   *
   * @return string|mixed
   */
  abstract protected function describeInputOption(Option $option, array $options = array());

  /**
   * Describe Definition instance.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param Definition $definition
   * @param array $options
   *
   * @return string|mixed
   */
  abstract protected function describeInputDefinition(Definition $definition, array $options = array());

  /**
   * Describe Command instance.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param Command $command
   * @param array $options
   *
   * @return string|mixed
   */
  abstract protected function describeCommand(Command $command, array $options = array());
}
