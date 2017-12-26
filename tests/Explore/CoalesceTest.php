<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * TechNet Documentation: https://docs.microsoft.com/en-us/sql/t-sql/language-elements/coalesce-transact-sql
 */
class CoalesceTest extends TestCase
{

    public function testCoalesceTwoValuesWithNullFirst()
    {
        $sql =<<<SQL
SELECT COALESCE(NULL, 'Peter') name;
SQL;
        $rows = DB::select($sql);

        $this->assertEquals('Peter', $rows[0]->name);
    }

    public function testCoalesceTwoValuesWithValueFirst()
    {
        $sql =<<<SQL
SELECT COALESCE('June', NULL) name;
SQL;
        $rows = DB::select($sql);

        $this->assertEquals('June', $rows[0]->name);
    }

    public function testCoalesceEvaluatesLeftToRightTakingTheFirstNotNullValue()
    {
        $sql =<<<SQL
SELECT COALESCE(NULL, 'Franz', 'Hector', NULL) name;
SQL;
        $rows = DB::select($sql);

        $this->assertEquals('Franz', $rows[0]->name);
    }
    
    public function testCoalesceOnlyEvaluatesExpressionsThatAreNotNull()
    {
        $sql =<<<SQL
SELECT COALESCE(NULL + 2, 3 * 4) name;
SQL;
        $rows = DB::select($sql);

        $this->assertEquals(12, $rows[0]->name); 
    }
}
