<?php
/**
 * Freyja CLI ArgvInput Test.
 *
 * @package Freyja\CLI\Tests\Input
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Input;

use Freyja\CLI\Input\ArgvInput;
use Freyja\CLI\Input\Definition;
use Freyja\CLI\Input\Argument;
use Freyja\CLI\Input\Option;

/**
 * Test ArgvInput class.
 *
 * @package Freyja\CLI\Tests\Input
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class ArgvInputTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test constructor.
   *
   * @since 1.0.0
   * @access public
   */
  public function testConstructor() {
    $_SERVER['argv'] = array('cli.php', 'foo');
    $input = new ArgvInput;
    $r = new \ReflectionObject($input);
    $p = $r->getProperty('tokens');
    $p->setAccessible(true);

    $this->assertEquals(
      array('foo'),
      $p->getValue($input),
      '->__construct() automatically get its input from the argv server variable'
    );
  }

  /**
   * Test arguments parsing.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseArguments() {
    $input = new ArgvInput(array('cli.php', 'foo'));
    $input->bind(new Definition(array(new Argument('name'))));
    $this->assertEquals(
      array('name' => 'foo'),
      $input->getArguments(),
      '->parse() parses required arguments'
    );

    $input->bind(new Definition(array(new Argument('name'))));
    $this->assertEquals(
      array('name' => 'foo'),
      $input->getArguments(),
      '->parse() is stateless'
    );
  }

  /**
   * Test options parsing.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideOptions
   */
  public function testParseOptions($input, $options, $expected_options, $message) {
    $input = new ArgvInput($input);
    $input->bind(new Definition($options));

    $this->assertEquals($expected_options, $input->getOptions(), $message);
  }

  /**
   * Provide options for other test methods.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Parameters to be passed to test methods.
   */
  public function provideOptions() {
    return array(
      array(
        array('cli.php', '--foo'),
        array(new Option('foo')),
        array('foo' => true),
        '->parse() parses long options without a value'
      ),
      array(
        array('cli.php', '--foo=bar'),
        array(new Option('foo', 'f', Option::VALUE_REQUIRED)),
        array('foo' => 'bar'),
        '->parse() parses long options with a required value (with a = separator)'
      ),
      array(
        array('cli.php', '--foo', 'bar'),
        array(new Option('foo', 'f', Option::VALUE_REQUIRED)),
        array('foo' => 'bar'),
        '->parse() parses long options with a required value (with a space separator)'
      ),
      array(
        array('cli.php', '-f'),
        array(new Option('foo', 'f')),
        array('foo' => true),
        '->parse() parses short options without a value'
      ),
      array(
        array('cli.php', '-fbar'),
        array(new Option('foo', 'f', Option::VALUE_REQUIRED)),
        array('foo' => 'bar'),
        '->parse() parses short options with a required value (with no separator)'
      ),
      array(
        array('cli.php', '-f', 'bar'),
        array(new Option('foo', 'f', Option::VALUE_REQUIRED)),
        array('foo' => 'bar'),
        '->parse() parses short options with a required value (with a space separator)'
      ),
      array(
        array('cli.php', '-f', ''),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL)),
        array('foo' => ''),
        '->parse() parses short options with an optional empty value'
      ),
      array(
        array('cli.php', '-f', '', 'foo'),
        array(new Argument('name'), new Option('foo', 'f', Option::VALUE_OPTIONAL)),
        array('foo' => ''),
        '->parse() parses short options with an optional empty value followed by an argument'
      ),
      array(
        array('cli.php', '-f', '', '-b'),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL), new Option('bar', 'b')),
        array('foo' => '', 'bar' => true),
        '->parse() parses short options with an optional empty value followed by an option'
      ),
      array(
        array('cli.php', '-f', '-b', 'foo'),
        array(new Argument('name'), new Option('foo', 'f', Option::VALUE_OPTIONAL), new Option('bar', 'b')),
        array('foo' => null, 'bar' => true),
        '->parse() parses short options with an optional value which is not present'
      ),
      array(
        array('cli.php', '-fb'),
        array(new Option('foo', 'f'), new Option('bar', 'b')),
        array('foo' => true, 'bar' => true),
        '->parse() parses short options when they are aggregated as a single one'
      ),
      array(
        array('cli.php', '-fb', 'bar'),
        array(new Option('foo', 'f'), new Option('bar', 'b', Option::VALUE_REQUIRED)),
        array('foo' => true, 'bar' => 'bar'),
        '->parse() parses short options when they are aggregated as a single one and the last one has a required value'
      ),
      array(
        array('cli.php', '-fb', 'bar'),
        array(new Option('foo', 'f'), new Option('bar', 'b', Option::VALUE_OPTIONAL)),
        array('foo' => true, 'bar' => 'bar'),
        '->parse() parses short options when they are aggregated as a single one and the last one has an optional value'
      ),
      array(
        array('cli.php', '-fbbar'),
        array(new Option('foo', 'f'), new Option('bar', 'b', Option::VALUE_OPTIONAL)),
        array('foo' => true, 'bar' => 'bar'),
        '->parse() parses short options when they are aggregated as a single one and the last one has an optional value with no separator'
      ),
      array(
        array('cli.php', '-fbbar'),
        array(new Option('foo', 'f', Option::VALUE_OPTIONAL), new Option('bar', 'b', Option::VALUE_OPTIONAL)),
        array('foo' => 'bbar', 'bar' => null),
        '->parse() parses short options when they are aggregated as a single one and one of them takes a value'
      )
    );
  }

  /**
   * Test invalid input.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideInvalidInput
   */
  public function testInvalidInput($argv, $definition, $expected_exception_message) {
    $this->setExpectedException('Freyja\Exceptions\RuntimeException', $expected_exception_message);

    $input = new ArgvInput($argv);
    $input->bind($definition);
  }

  /**
   * Provide invalid input.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideInvalidInput() {
    return array(
      array(
        array('cli.php', '--foo'),
        new Definition(array(new Option('foo', 'f', Option::VALUE_REQUIRED))),
        'Option "--foo" requires a value.'
      ),
      array(
        array('cli.php', '-f'),
        new Definition(array(new Option('foo', 'f', Option::VALUE_REQUIRED))),
        'Option "--foo" requires a value.'
      ),
      array(
        array('cli.php', '-ffoo'),
        new Definition(array(new Option('foo', 'f', Option::VALUE_NONE))),
        'Option "-o" does not exist.'
      ),
      array(
        array('cli.php', '--foo=bar'),
        new Definition(array(new Option('foo', 'f', Option::VALUE_NONE))),
        'Option "--foo" does not accept a value.'
      ),
      array(
        array('cli.php', 'foo', 'bar'),
        new Definition,
        'Too many arguments.'
      ),
      array(
        array('cli.php', '--foo'),
        new Definition,
        'Option "--foo" does not exist.'
      ),
      array(
        array('cli.php', '-f'),
        new Definition,
        'Option "-f" does not exist.'
      ),
      array(
        array('cli.php', '-1'),
        new Definition(array(new Argument('number'))),
        'Option "-1" does not exist.'
      )
    );
  }

  /**
   * Test parsing array argument.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseArrayArgument() {
    $input = new ArgvInput(array('cli.php', 'foo', 'bar', 'baz', 'bat'));
    $input->bind(new Definition(array(new Argument('name', Argument::IS_ARRAY))));

    $this->assertEquals(
      array('name' => array('foo', 'bar', 'baz', 'bat')),
      $input->getArguments(),
      '->parse() parses array arguments'
    );
  }

  /**
   * Test parsing array option.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseArrayOption() {
    $input = new ArgvINput(array('cli.php', '--name=foo', '--name=bar', '--name=baz'));
    $input->bind(new Definition(array(new Option('name', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY))));
    $this->assertEquals(
      array('name' => array('foo', 'bar', 'baz')),
      $input->getOptions(),
      '->parse() parses array options ("--option=value" syntax)'
    );

    $input = new ArgvInput(array('cli.php', '--name', 'foo', '--name', 'bar', '--name', 'baz'));
    $input->bind(new Definition(array(new Option('name', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY))));
    $this->assertEquals(
      array('name' => array('foo', 'bar', 'baz')),
      $input->getOptions(),
      '->parse() parses empty array options ("--option value" syntax)'
    );

    $input = new ArgvInput(array('cli.php', '--name=foo', '--name=bar', '--name='));
    $input->bind(new Definition(array(new Option('name', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY))));
    $this->assertSame(
      array('name' => array('foo', 'bar', null)),
      $input->getOptions(),
      '->parse() parses empty array options as null ("--option=value" syntax)'
    );

    $input = new ArgvINput(array('cli.php', '--name', 'foo', '--name', 'bar', '--name', '--anotherOption'));
    $input->bind(new Definition(array(
      new Option('name', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY),
      new Option('anotherOption', null, Option::VALUE_NONE)
    )));
    $this->assertSame(
      array('name' => array('foo', 'bar', null), 'anotherOption' => true),
      $input->getOptions(),
      '->parse() parses empty array options as null ("--option value" syntax)'
    );
  }

  /**
   * Test parsing negative number after double dash.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseNegativeNumberAfterDoubleDash() {
    $input = new ArgvInput(array('cli.php', '--', '-1'));
    $input->bind(new Definition(array(new Argument('number'))));
    $this->assertEquals(
      array('number' => '-1'),
      $input->getArguments(),
      '->parse() parses arguments with leading dashes as arguments after having encountered a double-dash sequence'
    );

    $input = new ArgvINput(array('cli.php', '-f', 'bar', '--', '-1'));
    $input->bind(new Definition(array(new Argument('number'), new Option('foo', 'f', Option::VALUE_OPTIONAL))));
    $this->assertEquals(
      array('foo' => 'bar'),
      $input->getOptions(),
      '->parse() parses arguments with leading dashes as options before having encountered a double-dash sequence'
    );
    $this->assertEquals(
      array('number' => '-1'),
      $input->getArguments(),
      '->parse() parses arguments with leading dashes as arguments after having encountered a double-dash sequence'
    );
  }

  /**
   * Test parsing empty string argument.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseEmptyStringArgument() {
    $input = new ArgvInput(array('cli.php', '-f', 'bar', ''));
    $input->bind(new Definition(array(new Argument('empty'), new Option('foo', 'f', Option::VALUE_OPTIONAL))));

    $this->assertEquals(
      array('empty' => ''),
      $input->getArguments(),
      '->parse() parses empty string arguments'
    );
  }

  /**
   * Test get first argument.
   *
   * @since 1.0.0
   * @access public
   */
  public function testGetFirstArgument() {
    $input = new ArgvInput(array('cli.php', '-fbbar'));
    $this->assertNull($input->getFirstArgument(), '->getFirstArgument() returns null when there is no arguments');

    $input = new ArgvInput(array('cli.php', '-fbbar', 'foo'));
    $this->assertEquals(
      'foo',
      $input->getFirstArgument(),
      '->getFirstArgument() returns the first argument from the raw input'
    );
  }

  /**
   * Test has parameter option.
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasParameterOption() {
    $input = new ArgvInput(array('cli.php', '-f', 'foo'));
    $this->assertTrue($input->hasParameterOption('-f'), '->hasParameterOption() returns true if the given short option is in the raw input');

    $input = new ArgvInput(array('cli.php', '--foo', 'foo'));
    $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if the given short option is in the raw input');

    $input = new ArgvInput(array('cli.php', 'foo'));
    $this->assertFalse($input->hasParameterOption('--foo'), '->hasParameterOption() returns false if the given short option is not in the raw input');

    $input = new ArgvInput(array('cli.php', '--foo=bar'));
    $this->assertTrue($input->hasParameterOption('--foo'), '->hasParameterOption() returns true if the given option with provided value is in the raw input');
  }

  /**
   * Test has parameter option with only options flag.
   *
   * @since 1.0.0
   * @access public
   */
  public function testHasParameterOptionOnlyOptions() {
    $input = new ArgvInput(array('cli.php', '-f', 'foo'));
    $this->assertTrue($input->hasParameterOption('-f', true), '->hasParameterOption() returns true if given short option is in the raw input');

    $input = new ArgvInput(array('cli.php', '--foo', '--', 'foo'));
    $this->assertTrue($input->hasParameterOption('--foo', true), '->hasParameterOption() returns true if the given long option is in the raw input');

    $input = new ArgvInput(array('cli.php', '--foo=bar', 'foo'));
    $this->assertTrue($input->hasParameterOption('--foo', true), '->hasParameterOption() returns true if the given long option with provided value is in the raw input');

    $input = new ArgvInput(array('cli.php', '--', '--foo'));
    $this->assertFalse($input->hasParameterOption('--foo', true), '->hasParameterOption() returns false if the given option is in the raw input but after the end of options signal');
  }

  /**
   * Test __toString magic method.
   *
   * @since 1.0.0
   * @access public
   */
  public function testToString() {
    $input = new ArgvInput(array('cli.php', '-f', 'foo'));
    $this->assertEquals('-f foo', (string) $input);

    $input = new ArgvInput(array('cli.php', '-f', '--bar=foo', 'a b c d', "A\nB'C"));
    $this->assertEquals(
      '-f --bar=foo '.escapeshellarg('a b c d'). ' '.escapeshellarg("A\nB'C"),
      (string) $input
    );
  }

  /**
   * Test get parameter option with equal sign.
   *
   * @since 1.0.0
   * @access public
   *
   * @dataProvider provideGetParameterOptionValues
   */
  public function testGetParameterOptionEqualSign($argv, $key, $only_params, $expected) {
    $input = new ArgvInput($argv);
    $this->assertEquals($expected, $input->getParameterOption($key, false, $only_params), '->getParameterOption() returns the expected value');
  }

  /**
   * Provide option values for testGetParameterOptionEqualSign.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array
   */
  public function provideGetParameterOptionValues() {
    return array(
      array(array('app/console', 'foo:bar', '-e', 'dev'), '-e', false, 'dev'),
      array(array('app/console', 'foo:bar', '--env=dev'), '--env', false, 'dev'),
      array(array('app/console', 'foo:bar', '-e', 'dev'), array('-e', '--env'), false, 'dev'),
      array(array('app/console', 'foo:bar', '--env=dev'), array('-e', '--env'), false, 'dev'),
      array(array('app/console', 'foo:bar', '--env=dev', '--en=1'), array('--en'), false, '1'),
      array(array('app/console', 'foo:bar', '--env=dev', '', '--en=1'), array('--en'), false, '1'),
      array(array('app/console', 'foo:bar', '--env', 'val'), '--env', false, 'val'),
      array(array('app/console', 'foo:bar', '--env', 'val', '--dummy'), '--env', false, 'val'),
      array(array('app/console', 'foo:bar', '--', '--env=dev'), '--env', false, 'dev'),
      array(array('app/console', 'foo:bar', '--', '--env=dev'), '--env', true, false)
    );
  }

  /**
   * Test parsing single dash as argument.
   *
   * @since 1.0.0
   * @access public
   */
  public function testParseSingleDashAsArgument() {
    $input = new ArgvInput(array('cli.php', '-'));
    $input->bind(new Definition(array(new Argument('file'))));
    $this->assertEquals(
      array('file' => '-'),
      $input->getArguments(),
      '->parse() parses single dash as an argument'
    );
  }
}
