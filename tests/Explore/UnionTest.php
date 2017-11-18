<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UnionTest extends TestCase
{

    public function setUp()
    {

        parent::setUp();

        DB::statement('CREATE DATABASE bookshop');

        $sql = <<<sql
USE bookshop;
CREATE TABLE new_books(
  name NVARCHAR(128)
);
INSERT INTO new_books VALUES('The Rook');
INSERT INTO new_books VALUES('Ready Player One');

CREATE TABLE used_books(
  name NVARCHAR(128)
);
INSERT INTO used_books VALUES('Patternmaster');
INSERT INTO used_books VALUES('Ready Player One');
sql;

        DB::statement($sql);
    }

    public function testUnion()
    {

        $sql = <<<sql
SELECT * FROM new_books
UNION
SELECT * FROM used_books;
sql;


        $rows = DB::select($sql);

        // The order shouldn't matter here but subset check doesn't seem to work as a set operation.
        $this->assertArraySubset([
            (object)['name' => 'Patternmaster'],
            (object)['name' => 'Ready Player One'],
            (object)['name' => 'The Rook']
        ],
            $rows
        );
    }

    public function testUnionAll()
    {

        $sql = <<<sql
SELECT * FROM new_books
UNION ALL
SELECT * FROM used_books;
sql;


        $rows = DB::select($sql);

        // The order shouldn't matter here but subset check doesn't seem to work as a set operation.
        $this->assertArraySubset([
            (object)['name' => 'The Rook'],
            (object)['name' => 'Ready Player One'],
            (object)['name' => 'Patternmaster'],
            (object)['name' => 'Ready Player One'],
        ],
            $rows
        );
    }

    public function tearDown()
    {

        DB::statement('use master; DROP DATABASE bookshop;');

        parent::tearDown();
    }

}
