<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Hugo\Vsd\VsDumper;

class VsDumperTest extends TestCase
{
    protected function setUp(): void
    {
        // Set the environment variable for all tests
        putenv('VSD_HOST=127.0.0.1');
    }

    public function testDumpCreatesSocketConnection(): void
    {
        $this->expectNotToPerformAssertions();

        // Call the dump method
        VsDumper::dump('test data');

        // Assertions can be added if a mock socket server is implemented
    }

    public function testDumpHandlesEmptyInput(): void
    {
        $this->expectNotToPerformAssertions();

        // Call the dump method with no arguments
        VsDumper::dump();

        // Assertions can be added if a mock socket server is implemented
    }

    public function testNestedObject()
    {
        $object = new \stdClass();
        $object->property1 = 'value1';
        $object->property2 = 'value2';
        $object->nested = new \stdClass();
        $object->nested->property1 = 'nestedValue1';
        $object->nested->property2 = 'nestedValue2';
        $object->nested->array = ['item1', 'item2', 'item3'];
        $object->nested->array2 = ['item4', 'item5', 'item6'];
        $object->nested->array3 = ['item7', 'item8', 'item9'];
        $object->nested->array4 = ['item10', 'item11', 'item12'];
        $object->nested->array5 = ['item13', 'item14', 'item15'];

        VsDumper::dump($object);
        $this->expectNotToPerformAssertions();
    }

    public function testNestedArray()
    {
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
            'nested' => [
                'key1' => 'nestedValue1',
                'key2' => 'nestedValue2',
                'array' => ['item1', 'item2', 'item3'],
                'array2' => ['item4', 'item5', 'item6'],
                'array3' => ['item7', 'item8', 'item9'],
                'array4' => ['item10', 'item11', 'item12'],
                'array5' => ['item13', 'item14', 'item15'],
            ],
        ];

        VsDumper::dump($array);
        $this->expectNotToPerformAssertions();
    }



    public function testNestedFunctionCalls(): void
    {
        $this->expectNotToPerformAssertions();

        // Call a nested function to test stack trace
        $this->outerFunction();
    }

    private function outerFunction(): void
    {
        $this->middleFunction();
    }

    private function middleFunction(): void
    {
        $this->innerFunction();
    }

    private function innerFunction(): void
    {
        VsDumper::dump('nested test data');
    }
}
