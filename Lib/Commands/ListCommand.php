<?php
/**
 * Freyja CLI List Command.
 *
 * @package Freyja\CLI\Commands
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Commands;

use Freyja\CLI\Helpers\DescriptorHelper;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;
use Freyja\CLI\Input\InputInterface;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Output\OutputInterface;

/**
 * Display list of all available commands for the application.
 *
 * @package Freyja\CLI\Commands
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ListCommand extends Command {
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('list')
      ->setDefinition($this->createDefinition())
      ->setDescription('List commands')
      ->setHelp(<<<'EOF'
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml</info>

It's also possible to get a raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
      ); //' Just an hack for syntax highlighting.
  }

  /**
   * {@inheritdoc}
   */
  public function getNativeDefinition() {
    return $this->createDefinition();
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $helper = new DescriptorHelper;
    // TODO: Replace $application
    $helper->describe($output, $application, array(
      'format' => $input->getOption('format'),
      'raw_text' => $input->getOption('raw'),
      'namespace' => $input->getArgument('namespace')
    ));
  }

  /**
   * {@inheritdoc}
   */
  private function createDefinition() {
    return new Definition(array(
      new Argument('namespace', Argument::OPTIONAL, 'The namespace name'),
      new Option('raw', null, Option::VALUE_NONE, 'To output raw command list'),
      new Option('format', null, Option::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt')
    ));
  }
}
