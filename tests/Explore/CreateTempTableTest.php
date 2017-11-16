<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateTempTableTest extends TestCase {
  
  public function testCreateTempTableCreatedInTheSameOperation() {
    
    $sql =<<<SQL
CREATE TABLE #TempTable(ID INT);
DECLARE @message VARCHAR(128);
IF OBJECT_ID(N'TempDB.dbo.#TempTable', N'U') IS NOT NULL
  BEGIN
    SET @message = N'Table Exists';
  END
ELSE
  BEGIN
    SET @message = N'Table Does Not Exist';
  END
SELECT message = @message;
SQL;
    
    $result = DB::select($sql);
    
    $this->assertEquals('Table Exists', $result[0]->message);
  }

  public function testAccessTempTableCreatedInADifferentOperationsOnSameConnection() {
    
    $sql =<<<SQL
CREATE TABLE #TempTable(ID INT);
SQL;

    DB::unprepared($sql);

    $sql = <<<SQL
DECLARE @message VARCHAR(128);
IF OBJECT_ID(N'TempDB.dbo.#TempTable', N'U') IS NOT NULL
  BEGIN
    SET @message = N'Table Exists';
  END
ELSE
  BEGIN
    SET @message = N'Table Does Not Exist';
  END
SELECT message = @message;
SQL;

    $result = DB::select($sql);

    $this->assertEquals('Table Exists', $result[0]->message);
  }

  public function testAccessTempTableCreatedInADifferentOperationsOnDifferentConnection() {

    $sql =<<<SQL
CREATE TABLE #TempTable(ID INT);
SQL;

    DB::unprepared($sql);

    $sql = <<<SQL
DECLARE @message VARCHAR(128);
IF OBJECT_ID(N'TempDB.dbo.#TempTable', N'U') IS NOT NULL
  BEGIN
    SET @message = N'Table Exists';
  END
ELSE
  BEGIN
    SET @message = N'Table Does Not Exist';
  END
SELECT message = @message;
SQL;

    $result = DB::connection('sqlsrv_two')->select($sql);

    $this->assertEquals('Table Does Not Exist', $result[0]->message);
  }
  
  public function testAccessTempleInsideATransaction() {
    
    $result = [];
    DB::transaction( function() use (&$result) {
      $sql =<<<SQL
CREATE TABLE #TempTable(ID INT);
SQL;

      DB::unprepared($sql);

      $sql = <<<SQL
DECLARE @message VARCHAR(128);
IF OBJECT_ID(N'TempDB.dbo.#TempTable', N'U') IS NOT NULL
  BEGIN
    SET @message = N'Table Exists';
  END
ELSE
  BEGIN
    SET @message = N'Table Does Not Exist';
  END
SELECT message = @message;
SQL;

      $result = DB::select($sql);      
    });

    $this->assertEquals('Table Exists', $result[0]->message);
  }
}
