<?php

namespace MysqlRpow;

class ClassifierTest extends \PHPUnit_Framework_TestCase {

  public function getExampleFiles() {
    $files = [
      Classifier::TYPE_READ => __DIR__ . '/examples/reads.sql',
      Classifier::TYPE_WRITE => __DIR__ . '/examples/writes.sql',
      Classifier::TYPE_BUFFER => __DIR__ . '/examples/buffers.sql',
    ];

    $exs = [];
    foreach ($files as $expectOutput => $file) {
      $sqls = \MysqlRpow\ExampleLoader::load($file);
      foreach ($sqls as $sql) {
        $exs[] = [$expectOutput, $sql];
      }
    }
    return $exs;
  }

  public function getExamplesWithStrings() {
    return [
      ['SELECT "@x := 1"', 'SELECT ""'],
      ['SELECT @x := 1', 'SELECT @x := 1'],
      ['SELECT "foo", "bar"', 'SELECT "", ""'],
      ['SELECT "foo \"bar\"", "whiz"', 'SELECT "", ""'],
      ['SELECT foo("bar") AS `whiz`', 'SELECT foo("") AS ``'],
      ['SELECT `foo` AS `foobar` WHERE \'whim\'', 'SELECT `` AS `` WHERE \'\''],
    ];
  }

  /**
   * @dataProvider getExampleFiles
   */
  public function testClassify($expectOutput, $sql) {
    $c = new Classifier();
    $this->assertEquals($expectOutput, $c->classify($sql), "Expect the following expression to be classified as {$expectOutput}: {$sql}");
  }

  /**
   * @dataProvider getExamplesWithStrings
   */
  public function testStripStrings($input, $expected) {
    $c = new Classifier();
    $this->assertEquals($expected, $c->stripStrings($input));
  }

}