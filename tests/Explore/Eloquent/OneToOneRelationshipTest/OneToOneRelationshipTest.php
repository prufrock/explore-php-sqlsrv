<?php namespace Tests\Explore\Eloquent\OneToOneRelationshipTest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * This test demonstrates how the Laravel's one to one relationship works. 
 * 
 * Documentation: https://laravel.com/docs/5.5/eloquent-relationships#one-to-one 
 */
class OneToOneRelationshipTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Schema::create('books', function ($table) {
            $table->increments('id');
            $table->string('title');
        });

        /**
         * For the sake of this test lets say a book only ever has one ISBN number and that the ISBN number is unique to
         * the book.
         *
         * In order to make that relationship work the id of book has to be on the book table.
         */
        Schema::create('isbns', function ($table) {
            $table->increments('id');
            $table->string('number');
            $table->unsignedInteger('book_id')->nullable(); // needs to be nullable to test withDefault()
        });

        $book = new Book();
        $book->title = 'Wild Seed';
        $book->save();

        // For testing the relationship with book
        $isbn = new Isbn();
        $isbn->number = '0-385-15160-8';
        $isbn->book_id = $book->id;
        $isbn->save();
        
        // For testing withDefault()
        $isbn = new Isbn();
        $isbn->number = '0-385-13385-5';
        $isbn->save();
    }

    public function testHasOne()
    {
        $this->assertEquals('0-385-15160-8', Book::where('title', '=', 'Wild Seed')->first()->isbn->number);
    }

    public function testBelongsTo()
    {
        $this->assertEquals('Wild Seed', Isbn::where('number', '=', '0-385-15160-8')->first()->book->title);
    }
    
    public function testIsbnWithoutABook()
    {
        $this->expectExceptionMessage('Trying to get property of non-object');
        $x = Isbn::where('number', '=', '0-385-13385-5')->first()->book->title;
    }

    public function testIsbnWithNullDefault()
    {
        $this->assertNull(IsbnWithNullDefault::where('number', '=', '0-385-13385-5')->first()->book->title);
    }

    public function testIsbnWithDefined()
    {
        $this->assertEquals('', IsbnWithDefinedDefault::where('number', '=', '0-385-13385-5')->first()->book->title);
    }

    public function testIsbnWithClosureDefault()
    {
        $this->assertEquals('unknown', IsbnWithClosureDefault::where('number', '=', '0-385-13385-5')->first()->book->title);
    }

    public function tearDown()
    {
        Schema::drop('books');
        Schema::drop('isbns');

        parent::tearDown();
    }
}

class Book extends Model
{
    public $timestamps = false;

    public function isbn()
    {
        return $this->hasOne(Isbn::class);
    }
}

class Isbn extends Model
{
    public $timestamps = false;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}

class IsbnWithNullDefault extends Model
{
    public $timestamps = false;
    
    protected $table = 'isbns';

    public function book()
    {
        return $this->belongsTo(Book::class)->withDefault();
    }
}

class IsbnWithDefinedDefault extends Model
{
    public $timestamps = false;

    protected $table = 'isbns';

    public function book()
    {
        return $this->belongsTo(Book::class)->withDefault(['title' => '']);
    }
}

class IsbnWithClosureDefault extends Model
{
    public $timestamps = false;

    protected $table = 'isbns';

    public function book()
    {
        return $this->belongsTo(Book::class)->withDefault(function($book) {
            $book->title = "unknown";
        });
    }
}
