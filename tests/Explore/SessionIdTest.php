<?php namespace Tests\Explore;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class SessionIdTest extends TestCase {

  public function testGetSessionId() {

    $sql =<<<SQL
SELECT @@SPID spid;
SQL;

    $rows = DB::select($sql);
    
    $this->assertObjectHasAttribute('spid', $rows[0]);
    $this->assertTrue(is_string($rows[0]->spid));
    $this->assertRegExp('/^[0-9]+$/', $rows[0]->spid);
  }
}
