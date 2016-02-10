<?php
/**
 * Freyja CLI Help Command.
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
use Freyja\CLI\FreyjaCLI;
use Freyja\Exceptions\InvalidArgumentException;

/**
 * Display help for given command.
 *
 * @package Freyja\CLI\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class HelpCommand extends Command {
  /**
   * Command for which help is requested.
   *
   * @since 1.0.0
   * @access private
   * @var Freyja\CLI\Commands\Command
   */
  private $command;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->ignoreValidationErrors();

    $this->setName('help')
      ->setDefinition(array(
        new Argument('command_name', Argument::OPTIONAL, 'The command name', 'help'),
        new Option('format', null, Option::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
        new Option('raw', null, Option::VALUE_NONE, 'To output raw command help')
      ))
      ->setDescription('Displays help for a command')
      ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
      );
  }

  /**
   * Set command.
   *
   * @since 1.0.0
   * @access public
   *
   * @param Freyja\CLI\Commands\Command $command Command to set.
   */
  public function setCommand(Command $command) {
    $this->command = $command;
  }

  /**
   * {@inheritdoc}
   *
   * @throws Freyja\Exceptions\InvalidArgumentException if command does not exist.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if (is_null($this->command)) {
      $command = $input->getArgument('command_name');
      if (FreyjaCLI::commandExists($command))
        $this->command = FreyjaCLI::getCommand($command);
      else
        throw new InvalidArgumentException(sprintf('Command "%s" does not exist.', $command));
    }

    $helper = new DescriptorHelper;
    $helper->describe($output, $this->command, array(
      'format' => $input->getOption('format'),
      'raw_text' => $input->getOption('raw')
    ));

    $this->command = null;
  }
}
