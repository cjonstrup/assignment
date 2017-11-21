<?php

use App\UsersToCsvCommand;

class CommandTest extends \PHPUnit_Framework_TestCase
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
        $safeResponse = "";

        //test with correct format
        $this->assertTrue($this->command->isSafeQuery('select * from users', $safeResponse));

        //test with wrong table
        $this->assertFalse($this->command->isSafeQuery('select * from demo', $safeResponse));

        //test with wrong sql word
        $this->assertFalse($this->command->isSafeQuery('delete from users', $safeResponse));

        //test with wrong sql word
        $this->assertFalse($this->command->isSafeQuery('drop table users', $safeResponse));
    }

    public function testWriteToCsv()
    {
        $data = [
            ['id' => '1']
        ];

        $exception = "";
        $result = false;

        try
        {
           $result = $this->command->writeToCsv('unittest.csv', $data, true);
        }
        catch(\Exception $e)
        {
            $exception = $e->getMessage();
        }

        //test with correct format
        $this->assertFalse($result);
        $this->assertEquals("Division by zero", $exception);

    }

}