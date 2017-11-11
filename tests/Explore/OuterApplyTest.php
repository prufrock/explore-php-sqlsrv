<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OuterApplyTest extends TestCase {
  
  public function setUp() {
    parent::setUp();

    DB::unprepared('CREATE DATABASE Library;');
    $sql = <<<sql
USE Library;

CREATE TABLE Authors (
    id int NOT NULL,
    editor_id INT NULL,
    author_name NVARCHAR(25) NOT NULL,
    created_at DATETIME
    CONSTRAINT PK_author_id PRIMARY KEY(id)
);
sql;
    
    DB::unprepared($sql);
  }

  public function testOuterApply() {

    $sql = <<<sql
    SELECT * FROM Authors;
sql;

    $result = DB::select($sql);
    $this->assertTrue(true);
  }
  
  public function tearDown() {
    $sql = <<<sql
use master;
DROP DATABASE Library;
sql;
    
    DB::unprepared($sql);
    
    parent::tearDown();
  }
}
