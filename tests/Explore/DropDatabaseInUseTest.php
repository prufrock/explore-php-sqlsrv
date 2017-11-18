<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DropDatabaseInUseTest extends TestCase
{

    public function testDropDatabaseInUse()
    {

        DB::statement('CREATE DATABASE ball;');
        DB::statement('USE ball;');

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionCode('42000');
        $this->expectExceptionMessage('SQLSTATE[42000]: [Microsoft][ODBC Driver 13 for SQL Server][SQL Server]Cannot drop database "ball" because it is currently in use. (SQL: DROP DATABASE ball)');

        DB::statement('DROP DATABASE ball');
    }

    public function tearDown()
    {

        DB::statement('use master; DROP DATABASE ball');
        parent::tearDown();
    }
}
