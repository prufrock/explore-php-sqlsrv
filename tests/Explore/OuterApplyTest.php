<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OuterApplyTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        DB::statement('CREATE DATABASE bookstore;');
        $sql = <<<sql
USE bookstore;

CREATE TABLE users (
    id INT NOT NULL,
    name NVARCHAR(128)
);

INSERT INTO users VALUES(1, 'Gwen');

CREATE TABLE point_cards (
   id INT NOT NULL,
   user_id INT NOT NULL,
   initial_point_balance INT
);
INSERT INTO point_cards VALUES(1, 1, 100);
INSERT INTO point_cards VALUES(1, 2, 100);

CREATE TABLE purchases (
  id INT NOT NULL,
  point_card_id INT,
  points_spent INT
);
INSERT INTO purchases VALUES(1, 1, 10);
INSERT INTO purchases VALUES(2, 1, 10);
INSERT INTO purchases VALUES(3, 2, 20);
sql;
        DB::statement($sql);
    }

    /**
     * Outer Apply creates a "table valued expression", an in memory table, for each left input, in the example below that
     * means each row returned from "point_cards". This means that you can join multiple rows to a single row in the source
     * table or perform an aggregate calculation, as below. The other awesomeness of Apply is that it is run before "select"
     * which means you make up columns, as below, and use them in the list of columns selected from.
     */
    public function testOuterApplyByCalculatingASumInARelatedTable()
    {

        $sql = <<<sql
    SELECT 
      id, 
      point_balance = (initial_point_balance - sp.total_points_spent)
    FROM point_cards pc
    OUTER APPLY (SELECT SUM(points_spent) total_points_spent FROM purchases p WHERE pc.id = p.point_card_id) sp
    WHERE user_id = 1;
sql;

        $rows = DB::select($sql);
        $this->assertEquals(
            [
                (object)[
                    'id' => 1,
                    'point_balance' => 80
                ]
            ],
            $rows
        );
    }

    public function tearDown()
    {
        $sql = <<<sql
USE master;
DROP DATABASE bookstore;
sql;
        DB::statement($sql);

        parent::tearDown();
    }
}
