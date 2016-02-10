<?php
/**
 * Freyja CLI Command Public API base.
 *
 * @package Freyja\CLI\Commands
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Commands;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Input\Definition as InputDefinition;
use Freyja\CLI\Output\OutputInterface;
use Freyja\Exceptions\ExceptionInterface;
use Freyja\Exceptions\InvalidArgumentException;
use Freyja\Exceptions\LogicException;

/**
 * Freyja CLI Abstract Command.
 *
 * @package Freyja\CLI\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 * @abstract
 */
abstract class Command {
  /**
   * Command name.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $name;

  /**
   * Process title.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $process_title;

  /**
   * Command aliases.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $aliases = array();

  /**
   * Input definition.
   *
   * @since 1.0.0
   * @access private
   * @var InputDefinition
   */
  private $definition;

  /**
   * Command help.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $help;

  /**
   * Command description.
   *
   * @since 1.0.0
   * @access private
   * @var string
   */
  private $description;

  /**
   * Whether to ignore validation errors.
   *
   * @since 1.0.0
   * @access private
   * @var bool
   */
  private $ignoreValidationErrors = false;

  /**
   * Command Synopsis.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $synopsis = array();

  /**
   * Command usages.
   *
   * @since 1.0.0
   * @access private
   * @var array
   */
  private $usages = array();

  /**
   * Constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function __construct() {
    $this->configure();
  }

  /**
   * Ignore validation errors.
   *
   * This is mainly useful for the help command.
   *
   * @since 1.0.0
   * @access public
   */
  public function ignoreValidationErrors() {
    $this->ignoreValidationErrors = true;
  }

  /**
   * Whether command is enabled or not in the current environment.
   *
   * This should return false if the command can not run properly under the
   * current conditions.
   *
   * Override this to implement checks needed by your command.
   *
   * @since 1.0.0
   * @access public
   *
   * @return bool
   */
  public function isEnabled() {
    return true;
  }

  /**
   * Configure the current command.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   */
  abstract protected function configure();

  /**
   * Execute the current command.
   *
   * @since 1.0.0
   * @access protected
   * @abstract
   *
   * @param InputInterface $input InputInterface instance.
   * @param OutputInterface $output OutputInterface instance.
   * @return null|int null or 0 if everything went fine, or an error code.
   */
  abstract protected function execute(InputInterface $input, OutputInterface $output);

  /**
   * Interact with the user.
   *
   * This method is executed before the InputDefinition is validated.
   * This means that this is the only place where the command can interactively
   * ask for values of missing required arguments.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param InputInterface $input InputInterface instance.
   * @param OutputInterface $output OutputInterface instance.
   */
  protected function interact(InputInterface $input, OutputInterface $output) {

  }

  /**
   * Initialize command just after input has been validated.
   *
   * This is mainly useful when a lot of commands extend one main command where
   * some things need to be initialized based on the input arguments and options.
   *
   * @since 1.0.0
   * @access protected
   *
   * @param InputInterface $input InputInterface instance.
   * @param OutputInterface $output OutputInterface instance.
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {

  }

  /**
   * Run the command.
   *
   * The code to execute is defined in the execute() method.
   *
   * @see CommandInterface::execute()
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @param InputInterface $input Command input.
   * @param OutputInterface $output Command output.
   * @return int Command exit code.
   *
   * @throws \Exception
   */
  final public function run(InputInterface $input, OutputInterface $output) {
    // Bind the input against the command specific argument/options.
    try {
      $input->bind($this->definition);
    } catch (ExceptionInterface $e) {
      if (!$this->ignoreValidationErrors)
        throw $e;
    }

    $this->initialize($input, $output);

    if (null !== $this->processTitle) {
      if (function_exists('cli_set_process_title'))
        cli_set_process_title($this->processTitle);
      elseif (function_exists('setproctitle'))
        setproctitle($this->processTitle);
      elseif (OutputInterface::VERBOSITY_VERY_VERBOSE === $output->getVerbosity())
        $output->writeln('<warning>Install the proctitle PECL to be able to change the process title.</warning>');
    }

    if ($input->isInteractive())
      $this->interact($input, $output);

    // The command name argument is often omitted when a command is executed
    // directly with its run() method. It would fail the validation if we didn't
    // make sure the command argument is present, since it's required by the
    // application.
    if ($input->hasArgument('command') && null === $input->getArgument('command'))
      $input->setArgument('command', $this->getName());

    $input->validate();

    $status = $this->execute($input, $output);

    return is_numeric($status) ? (int) $status : 0;
  }

  /**
   * Set array of argument and option instances.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @param array|InputDefinition $definition Aray of argument and option
   * instances or a definition instance.
   * @return CommandInterface The current instance.
   */
  final public function setDefinition($definition) {
    if ($definition instanceof InputDefinition)
      $this->definition = $definition;
    else
      $this->definition->setDefinition($definition);

    return $this;
  }

  /**
   * Retrieve InputDefinition attached to this Command.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return InputDefinition Input definition of this Command.
   */
  final public function getDefinition() {
    return $this->definition;
  }

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
  public function addArgument($name, $mode = null, $description = '', $default = null) {
    $this->definition->addArgument(new Argument($name, $mode, $description, $default));
    return $this;
  }

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
  public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null) {
    $this->definition->addOption(new Option($name, $shortcut, $mode, $description, $default));
    return $this;
  }

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
  public function setName($name) {
    $this->validateName($name);
    $this->name = $name;
    return $this;
  }

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
  public function setProcessTitle($title) {
    $this->process_title = $title;
    return $this;
  }

  /**
   * Retrieve command name.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return string Command name.
   */
  final public function getName() {
    return $this->name;
  }

  /**
   * Set description for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $description Description for the command.
   * @return CommandInterface The current instance.
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * Retrieve command description.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return string Command description.
   */
  final public function getDescription() {
    return $this->description;
  }

  /**
   * Set help for the command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $help Help for the command.
   * @return CommandInterface The current instance.
   */
  public function setHelp($help) {
    $this->help = $help;
    return $this;
  }

  /**
   * Retrieve help for the command.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return string Help for the command.
   */
  final public function getHelp() {
    return $this->help;
  }

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
  public function setAliases($aliases) {
    if (!is_array($aliases) && !$aliases instanceof \Traversable)
      throw new InvalidArgumentException('$aliases must be an array or an instance of \Traversable');

    foreach ($aliases as $alias)
      $this->validateName($alias);

    $this->aliases = $aliases;
    return $this;
  }

  /**
   * Retrieve aliases for the command.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return array Array of aliases for the command.
   */
  final public function getAliases() {
    return $this->aliases;
  }

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
  public function getSynopsis($short = false) {
    $key = $short ? 'short' : 'long';

    if (!isset($this->synopsis[$key]))
      $this->synopsis[$key] = trim(sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));

    return $this->synopsis[$key];
  }

  /**
   * Add command usage example.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $usage The usage, it'll be prefixed with the command name.
   * @return CommandInterface The current instance.
   */
  public function addUsage($usage) {
    if (0 !== strpos($usage, $this->name))
      $usage = sprintf('%s %s', $this->name, $usage);

    $this->usages[] = $usage;

    return $this;
  }

  /**
   * Retrieve alternative usages of the command.
   *
   * @since 1.0.0
   * @access public
   * @final
   *
   * @return array
   */
  final public function getUsages() {
    return $this->usages;
  }

  /**
   * Validate command name.
   *
   * It must be non-empty and parts can optionally be separated by ":".
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $name Command name.
   *
   * @throw InvalidArgumentException if name is invalid.
   */
  private function validateName($name) {
    if (!preg_match('/^[^\: ]++(\:[^\: ]++)*$/', $name))
      throw new InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
  }
}
