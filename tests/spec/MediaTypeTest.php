<?php

use cebe\openapi\Reader;
use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\Example;

/**
 * @covers \cebe\openapi\spec\MediaType
 * @covers \cebe\openapi\spec\Example
 */
class MediaTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testRead()
    {
        /** @var $mediaType MediaType */
        $mediaType = Reader::readFromYaml(<<<'YAML'
schema:
  $ref: "#/components/schemas/Pet"
examples:
  cat:
    summary: An example of a cat
    value:
      name: Fluffy
      petType: Cat
      color: White
      gender: male
      breed: Persian
  dog:
    summary: An example of a dog with a cat's name
    value:
      name: Puma
      petType: Dog
      color: Black
      gender: Female
      breed: Mixed
  frog:
    $ref: "#/components/examples/frog-example"
YAML
            , MediaType::class);

        $result = $mediaType->validate();
        $this->assertEquals([], $mediaType->getErrors());
        $this->assertTrue($result);

        //$this->assertEquals('schema', $mediaType->name);// TODO support for reference
        $this->assertInternalType('array', $mediaType->examples);
        $this->assertCount(3, $mediaType->examples);
        $this->assertArrayHasKey('cat', $mediaType->examples);
        $this->assertArrayHasKey('dog', $mediaType->examples);
        $this->assertArrayHasKey('frog', $mediaType->examples);
        $this->assertInstanceOf(Example::class, $mediaType->examples['cat']);
        $this->assertInstanceOf(Example::class, $mediaType->examples['dog']);
        $this->assertInstanceOf(Example::class, $mediaType->examples['frog']);

        $this->assertEquals('An example of a cat', $mediaType->examples['cat']->summary);
        $expectedCat = [ // TODO we might actually expect this to be an object of stdClass
            'name' => 'Fluffy',
            'petType' => 'Cat',
            'color' => 'White',
            'gender' => 'male',
            'breed' => 'Persian',
        ];
        $this->assertEquals($expectedCat, $mediaType->examples['cat']->value);

    }
}