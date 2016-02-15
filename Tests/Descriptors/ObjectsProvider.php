<?php
/**
 * Freyja CLI Objects Provider.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Descriptors;

use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Tests\Fixtures\DescriptorCommand1;
use Freyja\CLI\Tests\Fixtures\DescriptorCommand2;

/**
 * Objects Provider.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @author Jean-François Simon <contact@jfsimon.fr>
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ObjectsProvider {
  /**
   * Retrieve Input Arguments.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @return Argument[]
   */
  public static function getArguments() {
    return array(
      'input_argument_1' => new Argument('argument_name', Argument::REQUIRED),
      'input_argument_2' => new Argument('argument_name', Argument::IS_ARRAY, 'argument description'),
      'input_argument_3' => new Argument('argument_name', Argument::OPTIONAL, 'argument description', 'default_value'),
      'input_argument_4' => new Argument('argument_name', Argument::REQUIRED, "multiline\nargument description"),
    );
  }

  /**
   * Retrieve Input Options.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @return Options[]
   */
  public static function getOptions() {
    return array(
      'input_option_1' => new Option('option_name', 'o', Option::VALUE_NONE),
      'input_option_2' => new Option('option_name', 'o', Option::VALUE_OPTIONAL, 'option description', 'default_value'),
      'input_option_3' => new Option('option_name', 'o', Option::VALUE_REQUIRED, 'option description'),
      'input_option_4' => new Option('option_name', 'o', Option::VALUE_IS_ARRAY | Option::VALUE_OPTIONAL, 'option description', array()),
      'input_option_5' => new Option('option_name', 'o', Option::VALUE_REQUIRED, "multiline\noption description"),
      'input_option_6' => new Option('option_name', array('o', 'O'), Option::VALUE_REQUIRED, 'option with multiple shortcuts'),
    );
  }

  /**
   * Retrieve Input Definitions.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @return Definition[]
   */
  public static function getDefinitions() {
    return array(
      'input_definition_1' => new Definition(),
      'input_definition_2' => new Definition(array(new Argument('argument_name', Argument::REQUIRED))),
      'input_definition_3' => new Definition(array(new Option('option_name', 'o', Option::VALUE_NONE))),
      'input_definition_4' => new Definition(array(
        new Argument('argument_name', Argument::REQUIRED),
        new Option('option_name', 'o', Option::VALUE_NONE),
      )),
    );
  }

  /**
   * Retrieve Commands.
   *
   * @since 1.0.0
   * @access public
   * @static
   *
   * @return Command[]
   */
  public static function getCommands() {
    return array(
      'command_1' => new DescriptorCommand1,
      'command_2' => new DescriptorCommand2
    );
  }
}
