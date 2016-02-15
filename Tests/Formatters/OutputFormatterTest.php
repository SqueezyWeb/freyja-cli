<?php
/**
 * Freyja CLI Output Formatter Test.
 *
 * @package Freyja\CLI\Tests\Formatters
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Tests\Formatters;

use Freyja\CLI\Formatters\OutputFormatter;
use Freyja\CLI\Formatters\Styles\Style;

/**
 * Test OutputFormatter class.
 *
 * @package Freyja\CLI\Tests\Formatters
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
class OutputFormatterTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test empty tag.
   *
   * @since 1.0.0
   * @access public
   */
  public function testEmptyTag() {
    $formatter = new OutputFormatter(true);
    $this->assertEquals('foo<>bar', $formatter->format('foo<>bar'));
  }

  /**
   * Test < character escaping.
   *
   * @since 1.0.0
   * @access public
   */
  public function testLGCharEscaping() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals('foo<bar', $formatter->format('foo\\<bar'));
    $this->assertEquals('<info>some info</info>', $formatter->format('\\<info>some info\\</info>'));
    $this->assertEquals('\\<info>some info\\</info>', OutputFormatter::escape('<info>some info</info>'));

    $this->assertEquals(
      "\033[33mFreyja\\CLI\\FreyjaCLI does work very well!\033[39m",
      $formatter->format('<comment>Freyja\CLI\FreyjaCLI does work very well!</comment>')
    );
  }

  /**
   * Test bundled styles.
   *
   * @since 1.0.0
   * @access public
   */
  public function testBundledStyles() {
    $formatter = new OutputFormatter(true);

    $this->assertTrue($formatter->hasStyle('error'));
    $this->assertTrue($formatter->hasStyle('info'));
    $this->assertTrue($formatter->hasStyle('warning'));
    $this->assertTrue($formatter->hasStyle('comment'));
    $this->assertTrue($formatter->hasStyle('question'));

    $this->assertEquals(
      "\033[37;41msome error\033[39;49m",
      $formatter->format('<error>some error</error>')
    );
    $this->assertEquals(
      "\033[32msome info\033[39m",
      $formatter->format('<info>some info</info>')
    );
    $this->assertEquals(
      "\033[33msome warning\033[39m",
      $formatter->format('<warning>some warning</warning>')
    );
    $this->assertEquals(
      "\033[33msome comment\033[39m",
      $formatter->format('<comment>some comment</comment>')
    );
    $this->assertEquals(
      "\033[30;46msome question\033[39;49m",
      $formatter->format('<question>some question</question>')
    );
  }

  /**
   * Test nested styles.
   *
   * @since 1.0.0
   * @access public
   */
  public function testNestedStyles() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "\033[37;41msome \033[32msome info\033[37;41m error\033[39;49m",
      $formatter->format('<error>some <info>some info</info> error</error>')
    );
  }

  /**
   * Test adjacent styles.
   *
   * @since 1.0.0
   * @access public
   */
  public function testAdjacentStyles() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "\033[37;41msome error\033[39;49m\033[32msome info\033[39m",
      $formatter->format('<error>some error</error><info>some info</info>')
    );
  }

  /**
   * Test style matching not greedy.
   *
   * @since 1.0.0
   * @access public
   */
  public function testStyleMatchingNotGreedy() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "(\033[32m>=2.0,<2.3\033[39m)",
      $formatter->format('(<info>>=2.0,<2.3</info>)')
    );
  }

  /**
   * Test style escaping.
   *
   * @since 1.0.0
   * @access public
   */
  public function testStyleEscaping() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "(\033[32mz>=2.0,<a2.3\033[39m)",
      $formatter->format('(<info>'.$formatter->escape('z>=2.0,<a2.3').'</info>)')
    );

    $this->assertEquals(
      "\033[32m<error>some error</error>\033[39m",
      $formatter->format('<info>'.$formatter->escape('<error>some error</error>').'</info>')
    );
  }

  /**
   * Test deep nested styles.
   *
   * @since 1.0.0
   * @access public
   */
  public function testDeepNestedStyles() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "\033[37;41merror\033[32minfo\033[33mcomment\033[32m\033[37;41merror\033[39;49m",
      $formatter->format('<error>error<info>info<comment>comment</comment></info>error</error>')
    );
  }

  /**
   * Test new style.
   *
   * @since 1.0.0
   * @access public
   */
  public function testNewStyle() {
    $formatter = new OutputFormatter(true);

    $style = new Style('blue', 'white');
    $formatter->setStyle('test', $style);

    $this->assertEquals($style, $formatter->getStyle('test'));
    $this->assertNotEquals($style, $formatter->getStyle('info'));

    $style = new Style('blue', 'white');
    $formatter->setStyle('b', $style);

    $this->assertEquals(
      "\033[34;47msome \033[34;47mcustom\033[34;47m msg\033[39;49m",
      $formatter->format('<test>some <b>custom</b> msg</test>')
    );
  }

  /**
   * Test redefine style.
   *
   * @since 1.0.0
   * @access public
   */
  public function testRedefineStyle() {
    $formatter = new OutputFormatter(true);

    $style = new Style('blue', 'white');
    $formatter->setStyle('info', $style);

    $this->assertEquals("\033[34;47msome custom msg\033[39;49m", $formatter->format('<info>some custom msg</info>'));
  }

  /**
   * Test non-style tag.
   *
   * @since 1.0.0
   * @access public
   */
  public function testNonStyleTag() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(
      "\033[32msome <tag> <setting=value> styled <p>single-char tag</p>\033[39m",
      $formatter->format('<info>some <tag> <setting=value> styled <p>single-char tag</p></info>')
    );
  }

  /**
   * Test format() with long long string.
   *
   * @since 1.0.0
   * @access public
   */
  public function testFormatLongString() {
    $formatter = new OutputFormatter(true);
    $long = str_repeat('\\', 14000);
    $this->assertEquals("\033[37;41msome error\033[39;49m".$long, $formatter->format('<error>some error</error>'.$long));
  }

  /**
   * Test format() with object that has __toString() method.
   *
   * @since 1.0.0
   * @access public
   */
  public function testFormatToStringObject() {
    $formatter = new OutputFormatter(false);
    $this->assertEquals(
      'some info',
      $formatter->format(new TableCell())
    );
  }

  /**
   * Test non decorated formatter.
   *
   * @since 1.0.0
   * @access public
   */
  public function testNonDecoratedFormatter() {
    $formatter = new OutputFormatter(false);

    $this->assertTrue($formatter->hasStyle('error'));
    $this->assertTrue($formatter->hasStyle('info'));
    $this->assertTrue($formatter->hasStyle('warning'));
    $this->assertTrue($formatter->hasStyle('comment'));
    $this->assertTrue($formatter->hasStyle('question'));

    $this->assertEquals(
      'some error', $formatter->format('<error>some error</error>')
    );
    $this->assertEquals(
      'some info', $formatter->format('<info>some info</info>')
    );
    $this->assertEquals(
      'some comment', $formatter->format('<comment>some comment</comment>')
    );
    $this->assertEquals(
      'some question', $formatter->format('<question>some question</question>')
    );

    $formatter->setDecorated(true);

    $this->assertEquals(
      "\033[37;41msome error\033[39;49m", $formatter->format('<error>some error</error>')
    );
    $this->assertEquals(
      "\033[32msome info\033[39m", $formatter->format('<info>some info</info>')
    );
    $this->assertEquals(
      "\033[33msome comment\033[39m", $formatter->format('<comment>some comment</comment>')
    );
    $this->assertEquals(
      "\033[30;46msome question\033[39;49m", $formatter->format('<question>some question</question>')
    );
  }

  /**
   * Test content with line breaks.
   *
   * @since 1.0.0
   * @access public
   */
  public function testContentWithLineBreaks() {
    $formatter = new OutputFormatter(true);

    $this->assertEquals(<<<EOF
\033[32m
some text\033[39m
EOF
      , $formatter->format(<<<'EOF'
<info>
some text</info>
EOF
    ));

    $this->assertEquals(<<<EOF
\033[32msome text
\033[39m
EOF
      , $formatter->format(<<<'EOF'
<info>some text
</info>
EOF
        ));

    $this->assertEquals(<<<EOF
\033[32m
some text
\033[39m
EOF
      , $formatter->format(<<<'EOF'
<info>
some text
</info>
EOF
    ));

    $this->assertEquals(<<<EOF
\033[32m
some text
more text
\033[39m
EOF
      , $formatter->format(<<<'EOF'
<info>
some text
more text
</info>
EOF
    ));
  }
}

class TableCell {
  public function __toString() {
    return '<info>some info</info>';
  }
}
