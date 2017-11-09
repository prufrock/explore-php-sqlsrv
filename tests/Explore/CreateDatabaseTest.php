<?php

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase {

  public function testCreateDatabase() {

    DB::unprepared('CREATE DATABASE test');

    $sql =<<<sql
DECLARE @dbname nvarchar(128)
SET @dbname = N'test'

IF (EXISTS (SELECT name
            FROM master.dbo.sysdatabases
            WHERE ('[' + name + ']' = @dbname
                   OR name = @dbname)))

  SELECT out = 'db exists';
sql;

    $result = DB::select($sql);
    $this->assertEquals('db exists', $result[0]->out);
  }

  public function tearDown() {

    DB::unprepared('DROP DATABASE test');
  }
}
