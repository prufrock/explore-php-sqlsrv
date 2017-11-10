<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase {

  public function testCreateDatabase() {

    DB::unprepared('CREATE DATABASE test');

    $sql =<<<sql
IF (EXISTS (SELECT name
            FROM master.dbo.sysdatabases
            WHERE ('[' + name + ']' = 'test'
                   OR name = 'test')))

  SELECT out = 'db exists';
sql;

    $result = DB::select($sql);
    $this->assertEquals('db exists', $result[0]->out);
  }

  public function tearDown() {

    DB::unprepared('DROP DATABASE test');
  }
}
