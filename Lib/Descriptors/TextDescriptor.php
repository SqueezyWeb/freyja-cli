<?php
/**
 * Freyja CLI Text Descriptor.
 *
 * @package Freyja\CLI\Descriptors
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Descriptors;

use Freyja\CLI\FreyjaCLI;
use Freyja\CLI\Commands\Command;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Option;

/**
 * Text descriptor.
 *
 * @package Freyja\CLI\Descriptors
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class TextDescriptor extends Descriptor {
  /**
   * {@inheritdoc}
   */
  protected function describeInputArgument(Argument $argument, array $options = array()) {
    if (null !== $argument->getDefault() && (!is_array($argument->getDefault()) || count($argument->getDefault())))
      $default = sprintf('<comment> [default: %s]</comment>', $this->formatDefaultValue($argument->getDefault()));
    else
      $default = '';

    $total_width = isset($options['total_width']) ? $options['total_width'] : strlen($argument->getName());
    $spacing_width = $total_width - strlen($argument->getName()) + 2;

    $this->writeText(sprintf('  <info>%s</info>%s%s%s',
      $argument->getName(),
      str_repeat(' ', $spacing_width),
      // + 17 = 2 spaces + <info> + </info> + 2 spaces
      preg_replace('/\s*[\r\n]\s*/', "\n".str_repeat(' ', $total_width + 17), $argument->getDescription()),
      $default
    ), $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function describeInputOption(Option $option, array $options = array()) {
    if ($option->acceptValue() && null !== $option->getDefault() && (!is_array($option->getDefault()) || count($option->getDefault())))
      $default = sprintf('<comment> [default: %s]</comment>', $this->formatDefaultValue($option->getDefault()));
    else
      $default = '';

    $value = '';
    if ($option->acceptValue()) {
      $value = '='.strtoupper($option->getName());

      if ($option->isValueOptional())
        $value = '['.$value.']';
    }

    $total_width = isset($options['total_width']) ? $options['total_width'] : $this->calculateTotalWidthForOptions(array($option));
    $synopsis = sprintf(
      '%s%s',
      $option->getShortcut() ? sprintf('-%s, ', $option->getShortcut()) : '    ',
      sprintf('--%s%s', $option->getName(), $value)
    );

    $spacing_width = $total_width - strlen($synopsis) + 2;

    $this->writeText(sprintf(
      '  <info>%s</info>%s%s%s%s',
      $synopsis,
      str_repeat(' ', $spacing_width),
      // + 17 = 2 spaces + <info> + </info> + 2 spaces
      preg_replace('/\s*[\r\n]\s*/', "\n".str_repeat(' ', $total_width + 17), $option->getDescription()),
      $default,
      $option->isArray() ? '<comment> (multiple values allowed)</comment>' : ''
    ), $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function describeInputDefinition(Definition $definition, array $options = array()) {
    $total_width = $this->calculateTotalWidthForOptions($definition->getOptions());
    foreach ($definition->getArguments() as $argument)
      $total_width = max($total_width, strlen($argument->getName()));

    if ($definition->getArguments()) {
      $this->writeText('<comment>Arguments:</comment>', $options);
      $this->writeNewLine();
      foreach ($definition->getArguments() as $argument) {
        $this->describeInputArgument($argument, array_merge($options, array('total_width' => $total_width)));
        $this->writeNewLine();
      }
    }

    if ($definition->getArguments() && $definition->getOptions())
      $this->writeNewLine();

    $later_options = array();
    if ($definition->getOptions()) {
      $this->writeText('<comment>Options:</comment>', $options);
      foreach ($definition->getOptions() as $option) {
        if (strlen($option->getShortcut()) > 1) {
          $later_options[] = $option;
          continue;
        }
      }
      $this->writeNewLine();
      $this->describeInputOption($option, array_merge($options, array('total_width' => $total_width)));
    }
    foreach ($later_options as $option) {
      $this->writeNewLine();
      $this->describeInputOption($option, array_merge($options, array('total_width' => $total_width)));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function describeCommand(Command $command, array $options = array()) {
    $command->getSynopsis(true);
    $command->getSynopsis(false);

    $this->writeText('<comment>Usage:</comment>', $options);
    foreach (array_merge(array($command->getSynopsis(true)), $command->getAliases(), $command->getUsages()) as $usage) {
      $this->writeNewLine();
      $this->writeText('  '.$usage, $options);
    }
    $this->writeNewLine();

    $definition = $command->getDefinition();
    if ($definition->getOptions() || $definition->getArguments()) {
      $this->writeNewLine();
      $this->describeInputDefinition($definition, $options);
      $this->writeNewLine();
    }

    if ($help = $command->getProcessedHelp()) {
      $this->writeNewLine();
      $this->writeText('<comment>Help:</comment>', $options);
      $this->writeNewLine();
      $this->writeText(' '.str_replace("\n", "\n ", $help), $options);
      $this->writeNewLine();
    }
  }

  /**
   * {@inheritdoc}
   */
  private function writeText($content, array $options = array()) {
    $this->write(
      isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
      isset($options['raw_output']) ? !$options['raw_output'] : true
    );
  }

  /**
   * Write new line.
   *
   * @since 1.0.0
   * @access private
   */
  private function writeNewLine() {
    $this->writeText("\n");
  }

  /**
   * Format input option/argument default value.
   *
   * @internal Note that the behavior with PHP 5.3 this method may not behave
   * as expected for multibyte unicode characters, as the constants
   * `JSON_UNESCAPED_SLASHES` and `JSON_UNESCAPED_UNICODE` are available since
   * PHP 5.4. We unescape slashes with `str_replace()` in PHP <5.4, but don't
   * yet do anything about unicode characters.
   *
   * @since 1.0.0
   * @access private
   *
   * @param mixed $default
   *
   * @return string
   */
  private function formatDefaultValue($default) {
    $replacements = array(
      '\\\\' => '\\',
      '\/' => '/'
    );
    if (version_compare(PHP_VERSION, '5.4.0', '<'))
      return str_replace(array_keys($replacements), array_values($replacements), json_encode($default));
    return str_replace('\\\\', '\\', json_encode($default, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
  }

  /**
   * Get column width for commands.
   *
   * @since 1.0.0
   * @access private
   *
   * @param Command[] $commands Array of commands.
   *
   * @return int
   */
  private function getColumnWidth(array $commands) {
    $widths = array();

    foreach ($commands as $command) {
      $widths[] = strlen($command->getName());
      foreach ($command->getAliases() as $alias)
        $widths[] = strlen($alias);
    }
    return max($widths) + 2;
  }

  /**
   * Calculate total width for options.
   *
   * @since 1.0.0
   * @access private
   *
   * @param Option[] $options Array of options.
   *
   * @return int
   */
  private function calculateTotalWidthForOptions($options) {
    $total_width = 0;
    foreach ($options as $option) {
      // "-" + shortcut + ", --" + name
      $name_length = 1 + max(strlen($option->getShortcut()), 1) + 4 + strlen($option->getName());

      if ($option->acceptValue()) {
        $value_length = 1 + strlen($option->getName()); // = + value
        $value_length += $option->isValueOptional() ? 2 : 0; // [ + ]

        $name_length += $value_length;
      }
      $total_width = max($total_width, $name_length);
    }

    return $total_width;
  }
}
