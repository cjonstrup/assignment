<?php

namespace Tests;

use App\UsersToCsvCommand;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    private $command;

    public function setUp()
    {
        $this->command = new UsersToCsvCommand();
    }

    public function tearDown()
    {
        $this->command = null;
    }

    public function testSafeQuery()
    {
        $safeResponse = '';

        //test with correct format
        $this->assertTrue($this->command->isSafeQuery('select * from users', $safeResponse));

        //test with wrong table
        $this->assertFalse($this->command->isSafeQuery('select * from demo', $safeResponse));

        //test with wrong sql word
        $this->assertFalse($this->command->isSafeQuery('delete from users', $safeResponse));

        //test with wrong sql word
        $this->assertFalse($this->command->isSafeQuery('drop table users', $safeResponse));

        //test with wrong sql word
        $this->assertFalse($this->command->isSafeQuery('update users set id = \'1\'', $safeResponse));
    }

    public function testWriteToCsv()
    {
        $file = 'unittest.csv';

        $data = [
            ['id' => '1'],
        ];

        $exception = '';
        $result = false;

        try {
            $result = $this->command->writeToCsv($file, $data, true);
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        //test with correct format
        $this->assertFalse($result);
        $this->assertEquals('Division by zero', $exception);
    }
}
