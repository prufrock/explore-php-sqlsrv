<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateTableTest extends TestCase
{

    public function setUp()
    {

        parent::setUp();

        DB::statement('CREATE DATABASE library;');
    }

    public function testCreateTable()
    {
        $sql = <<<sql
USE library;

CREATE TABLE just_id (
    id INT NOT NULL,
);
sql;

        DB::statement($sql);

        $sql = <<<sql
DECLARE @message VARCHAR(128);
IF OBJECT_ID(N'dbo.just_id', N'U') IS NOT NULL
BEGIN
  SET @message = N'Table Exists';
END
SELECT message = @message; 
sql;

        $result = DB::select($sql);

        $this->assertEquals('Table Exists', $result[0]->message);
    }

    public function tearDown()
    {

        DB::statement('use master; DROP DATABASE library');

        parent::tearDown();
    }

}
