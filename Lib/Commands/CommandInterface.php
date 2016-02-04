<?php
/**
 * Freyja CLI Command Public API definition.
 *
 * @package Freyja\CLI\Commands
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Commands;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Output\OutputInterface;
use Freyja\Exceptions\InvalidArgumentException;

/**
 * Freyja CLI Command Interface.
 *
 * @package Freyja\CLI\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface CommandInterface {
  /**
   * Ignore validation errors.
   *
   * This is mainly useful for the help command.
   *
   * @since 1.0.0
   * @access public
   */
  public function ignoreValidationErrors();

  /**
   * Whether command is enabled or not in the current environment.
   *
   * This should return false if the command can not run properly under the
   * current conditions.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool
   */
  public function isEnabled();

  /**
   * Run the command.
   *
   * The code to execute is defined in the execute() method.
   *
   * @see CommandInterface::execute()
   *
   * @since 1.0.0
   * @access public
   *
   * @param InputInterface $input Command input.
   * @param OutputInterface $output Command output.
   * @return int Command exit code.
   *
   * @throws \Exception
   */
  public function run(InputInterface $input, OutputInterface $output);

  /**
   * Set array of argument and option instances.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array|InputDefinition $definition Aray of argument and option
   * instances or a definition instance.
   * @return CommandInterface The current instance.
   */
  public function setDefinition($definition);

  /**
   * Retrieve InputDefinition attached to this Command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return InputDefinition Input definition of this Command.
   */
  public function getDefinition();

  /**
   * Add argument.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Argument name.
   * @param int $mode Optional. Argument mode: Argument::REQUIRED,
   * Argument::OPTIONAL, or Argument::IS_ARRAY. If null, defaults to
   * Argument::OPTIONAL. Default null.
   * @param string $description Optional. Description text. Default empty.
   * @param mixed $default Optional. Default value (not for Argument::REQUIRED
   * mode). Default null.
   * @return CommandInterface The current instance.
   */
  public function addArgument($name, $mode = null, $description = '', $default = null);

  /**
   * Add option.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Option name.
   * @param string $shortcut Optional. Option shortcut. Default null.
   * @param int $mode Optional. Option mode. One of the Option::VALUE_* constants.
   * If null, Option::VALUE_OPTIONAL is used. Default null.
   * @param string $description Optional. Description text. Default empty.
   * @param mixed $default Optional. Default value. Must be null for
   * Option::VALUE_REQUIRED or Option::VALUE_NONE. Default null.
   * @return CommandInterface The current instance.
   */
  public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null);

  /**
   * Set name of command.
   *
   * This method can set both the namespace and the name if you separate them
   * by a colon (:).
   *
   * @example
   * $command->setName('foo:bar');
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $name Command name.
   * @return CommandInterface The current instance.
   *
   * @throws InvalidArgumentException if the name is invalid.
   */
  public function setName($name);

  /**
   * Set process title of the command.
   *
   * This feature should be used only when creating a long process command, like a daemon.
   *
   * PHP 5.5+ or the proctitle PECL library is required.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $title Process title.
   * @return CommandInterface The current instance.
   */
  public function setProcessTitle($title);

  /**
   * Retrieve command name.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Command name.
   */
  public function getName();

  /**
   * Set description for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $description Description for the command.
   * @return CommandInterface The current instance.
   */
  public function setDescription($description);

  /**
   * Retrieve command description.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Command description.
   */
  public function getDescription();

  /**
   * Set help for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $help Help for the command.
   * @return CommandInterface The current instance.
   */
  public function setHelp($help);

  /**
   * Retrieve help for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Help for the command.
   */
  public function getHelp();

  /**
   * Set aliases for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string[] $aliases Array of aliases for the command.
   * @return CommandInterface The current instance.
   *
   * @throws InvalidArgumentException if an alias is invalid.
   */
  public function setAliases($aliases);

  /**
   * Retrieve aliases for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Array of aliases for the command.
   */
  public function getAliases();

  /**
   * Retrieve synopsis for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param bool $short Whether to show the short version of the synopsis (with
   * options folded) or not.
   * @return string The synopsis.
   */
  public function getSynopsis($short = false);

  /**
   * Add command usage example.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $usage The usage, it'll be prefixed with the command name.
   * @return CommandInterface The current instance.
   */
  public function addUsage($usage);

  /**
   * Retrieve alternative usages of the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function getUsages();
}
