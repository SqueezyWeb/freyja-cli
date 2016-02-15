<?php
/**
 * Freyja CLI Abstract Descriptor Test.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Descriptors;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Output\BufferedOutput;

/**
 * Abstract Descriptor Test to be extended by other tests of the same type.
 *
 * @package Freyja\CLI\Tests\Descriptors
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 * @abstract
 */
abstract class AbstractDescriptorTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test describeInputArgument().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider getDescribeInputArgumentTestData
   */
  public function testDescribeInputArgument(Argument $argument, $expected_description) {
    $this->assertDescription($expected_description, $argument);
  }

  /**
   * Test describeInputOption().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider getDescribeInputOptionTestData
   */
  public function testDescribeInputOption(Option $option, $expected_description) {
    $this->assertDescription($expected_description, $option);
  }

  /**
   * Test describeInputDefinition().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider getDescribeInputDefinitionTestData
   */
  public function testDescribeInputDefinition(Definition $definition, $expected_description) {
    $this->assertDescription($expected_description, $definition);
  }

  /**
   * Test describeCommand().
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider getDescribeCommandTestData
   */
  public function testDescribeCommand(Command $command, $expected_description) {
    $this->assertDescription($expected_description, $command);
  }

  /**
   * Retrieve data for describeInputArgument().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getDescribeInputArgumentTestData() {
    return $this->getDescriptionTestData(ObjectsProvider::getArguments());
  }

  /**
   * Retrieve data for describeInputOption().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getDescribeInputOptionTestData() {
    return $this->getDescriptionTestData(ObjectsProvider::getOptions());
  }

  /**
   * Retrieve data for describeInputDefinition().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getDescribeInputDefinitionTestData() {
    return $this->getDescriptionTestData(ObjectsProvider::getDefinitions());
  }

  /**
   * Retrieve data for describeCommand().
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getDescribeCommandTestData() {
    return $this->getDescriptionTestData(ObjectsProvider::getCommands());
  }

  /**
   * Retrieve descriptor.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @return Freyja\CLI\Descriptors\DescriptorInterface
   */
  abstract protected function getDescriptor();

  /**
   * Retrieve format.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string
   */
  abstract protected function getFormat();

  /**
   * Retrieve description test data.
   *
   * @since 1.0.0
   * @access private
   *
   * @param array $objects
   *
   * @return array
   */
  private function getDescriptionTestData(array $objects) {
    $data = array();
    foreach ($objects as $name => $object) {
      $description = file_get_contents(sprintf('%s/../Fixtures/%s.%s', __DIR__, $name, $this->getFormat()));
      $data[] = array($object, $description);
    }

    return $data;
  }

  /**
   * Assert description.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param string $expected_description
   * @param object $described_object
   */
  protected function assertDescription($expected_description, $described_object) {
    $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true);
    $this->getDescriptor()->describe($output, $described_object, array('raw_output' => true));
    $this->assertEquals(trim($expected_description), trim(str_replace(PHP_EOL, "\n", $output->fetch())));
  }
}
