<?php
/**
 * Freyja CLI Command Tester.
 *
 * @package Freyja\CLI\Testers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Testers;

use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\ArrayInput;
use Freyja\CLI\Output\StreamOutput;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;

/**
 * Ease testing of console commands.
 *
 * @package Freyja\CLI\Testers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class CommandTester {
  /**
   * Command to test.
   *
   * @since 1.0.0
   * @access private
   * @var Command
   */
  private $command;

  /**
   * Command input.
   *
   * @since 1.0.0
   * @access private
   * @var InputInterface
   */
  private $input;

  /**
   * Command output.
   *
   * @since 1.0.0
   * @access private
   * @var OutputInterface
   */
  private $output;

  /**
   * Command status code.
   *
   * @since 1.0.0
   * @access private
   * @var int
   */
  private $status_code;

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Command $command Command instance to test.
   */
  public function __construct(Command $command) {
    $this->command = $command;
  }

  /**
   * Execute command.
   *
   * Available execution options:
   * - interactive: sets the input interactive flag
   * - decorated: sets the output decorated flag
   * - verbosity: sets the output verbosity flag
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $input Array of command arguments and options.
   * @param array $options Optional. Array of execution options. Default empty.
   *
   * @return int Command exit code.
   */
  public function execute(array $input, array $options = array()) {
    // Set command name automatically if the application requires this argument
    // and no command name was passed.
    if (!isset($input['command']) && $this->command->getDefinition()->hasArgument('command'))
      $input = array_merge(array('command' => $this->command->getName()), $input);

    $this->input = new ArrayInput($input);

    if (isset($options['interactive']))
      $this->input->setInteractive($options['interactive']);

    $this->output = new StreamOutput(fopen('php://memory', 'w', false));
    if (isset($options['decorated']))
      $this->output->setDecorated($options['decorated']);

    if (isset($options['verbosity']))
      $this->output->setVerbosity($options['verbosity']);

    return $this->status_code = $this->command->run($this->input, $this->output);
  }

  /**
   * Retrieve display returned by the last execution of the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $normalize Optional. Whether to normalize end of lines to \n or
   * not. Default false.
   *
   * @return string The display.
   */
  public function getDisplay($normalize = false) {
    rewind($this->output->getStream());

    $display = stream_get_contents($this->output->getStream());

    if ($normalize)
      $display = str_replace(PHP_EOL, "\n", $display);

    return $display;
  }

  /**
   * Retrieve input instance used by the last execution of the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return InputInterface Current input instance.
   */
  public function getInput() {
    return $this->input;
  }

  /**
   * Retrieve output instance used by the last execution of the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return OutputInterface Current output instance.
   */
  public function getOutput() {
    return $this->output;
  }

  /**
   * Retrieve status code returned by last execution of application.
   *
   * @since 1.0.0
   * @access public
   *
   * @return int Status code.
   */
  public function getStatusCode() {
    return $this->status_code;
  }
}
