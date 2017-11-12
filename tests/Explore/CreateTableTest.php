<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateTableTest extends TestCase {
  
  public function setUp() {
    
    parent::setUp();
    
    DB::unprepared('CREATE DATABASE library;');
  }
  
  public function testCreateTable() {
    $sql = <<<sql
USE library;

CREATE TABLE just_id (
    id int NOT NULL,
);
sql;

    DB::unprepared($sql);
    
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
  
  public function tearDown() {

    DB::unprepared('use master; DROP DATABASE library');
    
    parent::tearDown();
  }

}
