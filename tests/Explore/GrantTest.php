<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateLoginTest extends TestCase {

  public function setUp() {

    parent::setUp();

    DB::unprepared('CREATE DATABASE bookshop');

    $sql =<<<SQL
use bookshop;
CREATE TABLE new_books(
  name NVARCHAR(128)
);
INSERT INTO new_books VALUES('Patternmaster');
SQL;

    DB::unprepared($sql);
  }

  public function testSelectWithoutPermission() {

    $sql =<<<SQL
CREATE LOGIN mary WITH PASSWORD = 'supersecret1!';
CREATE USER MaryContrary FOR LOGIN mary;
EXECUTE AS USER = 'MaryContrary';
SQL;

    DB::statement($sql);

    $sql =<<<SQL
SELECT * FROM new_books;
SQL;
    
    $this->expectExceptionCode(42000);
    $this->expectExceptionMessage('SQLSTATE[42000]: [Microsoft][ODBC Driver 13 for SQL Server][SQL Server]The SELECT permission was denied on the object \'new_books\', database \'bookshop\', schema \'dbo\'. (SQL: SELECT * FROM new_books;)');
    
    $rows = DB::select($sql);

    $this->assertArraySubset([
      (object)['name' => 'Patternmaster'],
    ],
      $rows
    );
  }

  public function testGrantSelect() {

    $sql =<<<SQL
CREATE LOGIN mary WITH PASSWORD = 'supersecret1!';
CREATE USER MaryContrary FOR LOGIN mary;
GRANT SELECT TO MaryContrary;
EXECUTE AS USER = 'MaryContrary';
SQL;

    DB::statement($sql);
    
    $sql =<<<SQL
SELECT * FROM new_books;
SQL;

    $rows = DB::select($sql);

    $this->assertArraySubset([
      (object)['name' => 'Patternmaster'],
    ],
      $rows
    );
  }

  public function testShowPlanWithoutPermission() {

    $sql =<<<SQL
CREATE LOGIN mary WITH PASSWORD = 'supersecret1!';
CREATE USER MaryContrary FOR LOGIN mary;
GRANT SELECT TO MaryContrary;
EXECUTE AS USER = 'MaryContrary';
SQL;

    DB::statement($sql);

    $sql =<<<SQL
SET STATISTICS XML ON;
SELECT * FROM new_books;
SQL;

    $this->expectExceptionCode(42000);
    $this->expectExceptionMessage('SQLSTATE[42000]: [Microsoft][ODBC Driver 13 for SQL Server][SQL Server]SHOWPLAN permission denied in database \'bookshop\'. (SQL: SET STATISTICS XML ON;
SELECT * FROM new_books;)');

    $rows = DB::select($sql);

    $this->assertArraySubset([
      (object)['name' => 'Patternmaster'],
    ],
      $rows
    );
  }

  public function testShowPlanWithPermission() {

    $sql =<<<SQL
CREATE LOGIN mary WITH PASSWORD = 'supersecret1!';
CREATE USER MaryContrary FOR LOGIN mary;
GRANT SELECT TO MaryContrary;
GRANT SHOWPLAN TO MaryContrary;
EXECUTE AS USER = 'MaryContrary';
SQL;

    DB::statement($sql);

    $sql =<<<SQL
SET STATISTICS XML ON;
SELECT * FROM new_books;
SET STATISTICS XML OFF;
SQL;

    $rows = DB::select($sql);

    $this->assertArraySubset([
      (object)['name' => 'Patternmaster'],
    ],
      $rows
    );
  }
  
  public function tearDown() {

    $sql =<<<SQL
REVERT; -- Revert to the original user. Docs refer to this as the "execution context".
DROP LOGIN mary;
DROP USER MaryContrary;
use master;
DROP DATABASE bookshop;
SQL;

    DB::statement($sql);
  }
}
