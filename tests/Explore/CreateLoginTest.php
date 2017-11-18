<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateLoginTest extends TestCase
{

    public function testCreateLogin()
    {

        $sql = <<<sql
CREATE LOGIN mary WITH PASSWORD = 'supersecret1!';
CREATE USER MaryContrary FOR LOGIN mary;
sql;

        $result = DB::statement($sql);
        $this->assertTrue($result);
    }

    public function tearDown()
    {

        $sql = <<<sql
DROP LOGIN mary;
DROP USER MaryContrary;
sql;

        DB::statement($sql);
    }
}
