<?php namespace Tests\Explore\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ModelsAndNamingTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Schema::create('books', function ($table) {
            $table->increments('id');
            $table->string('title');
        });
    }

    /**
     * Laravel determines the table by generating the snake case plural name of the class. This means the model "Book"
     * matches to the table "books".
     * Documentation: https://laravel.com/docs/5.5/eloquent#defining-models
     */
    public function testDefiningAModelWithAutomaticTableName()
    {
        $book = new Book();
        $book->title = 'Patternmaster';
        $book->save();

        $this->assertTrue(true);
        $this->assertEquals('Patternmaster', Book::where('title', '=', 'Patternmaster')->first()->title);
    }

    /**
     * I got curious. If the table name is generated does it require that the model name be singular? Nope.
     */
    public function testDefiningAModelWithPluralNameThatReliesOnAutomaticTableName()
    {
        $book = new Books();
        $book->title = 'Patternmaster';
        $book->save();

        $this->assertTrue(true);
        $this->assertEquals('Patternmaster', Books::where('title', '=', 'Patternmaster')->first()->title);
    }

    /**
     * You can override the Laravel's assumption about the table name by setting the "$table" variable.
     */
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

class Books extends Model
{
    public $timestamps = false;
}

class Literature extends Model
{
    public $timestamps = false;

    protected $table = 'books';
}
