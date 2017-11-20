<?php namespace Tests\Explore\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DefiningModelsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Schema::create('books', function ($table) {
            $table->increments('id');
            $table->string('title');
        });
    }

    public function testDefiningAModelWithAutomaticTableName()
    {
        $book = new Book();
        $book->title = 'Patternmaster';
        $book->save();

        $this->assertTrue(true);
        $this->assertEquals('Patternmaster', Book::where('title', '=', 'Patternmaster')->first()->title);
    }

    public function testDefiningAModelWithDeclaredTableName()
    {
        $literature = new Literature();
        $literature->title = 'Patternmaster';
        $literature->save();

        $this->assertTrue(true);
        $this->assertEquals('Patternmaster', Literature::where('title', '=', 'Patternmaster')->first()->title);
    }

    public function tearDown()
    {
        Schema::drop('books');

        parent::tearDown();
    }
}

class Book extends Model
{
    public $timestamps = false;
}

class Literature extends Model
{
    public $timestamps = false;

    protected $table = 'books';
}
