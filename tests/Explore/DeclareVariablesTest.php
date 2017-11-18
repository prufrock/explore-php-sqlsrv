<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeclareVariablesTest extends TestCase
{

    public function testVarCharVariable()
    {

        $sql = <<<sql
DECLARE @author VARCHAR(128)
SET @author = N'author'

  SELECT author = @author;
sql;

        $result = DB::select($sql);
        $this->assertEquals('author', $result[0]->author);
    }

    public function testVarCharVariableWithUnicodeValue()
    {

        $sql = <<<sql
DECLARE @author VARCHAR(128)
SET @author = N'作者'

  SELECT author = @author;
sql;

        $result = DB::select($sql);
        $this->assertEquals('??', $result[0]->author);
    }

    public function testUnicodeVarCharVariable()
    {

        $sql = <<<sql
DECLARE @author NVARCHAR(128)
SET @author = N'作者'

  SELECT author = @author;
sql;

        $result = DB::select($sql);
        $this->assertEquals('作者', $result[0]->author);
    }
}
