<?php

namespace Gtk\EloquentTracking\Tests;

use Gtk\EloquentTracking\HasUpdater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class HasUpdaterTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        DB::schema()->create('foos', function ($table) {
            $table->increments('id');
            $table->string('zonda');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    public function test_it_can_update_updater()
    {
        Auth::shouldReceive('guest')->andReturn(false);

        Auth::shouldReceive('id')->once()->andReturn(1);

        $foo = FooHasUpdater::forceCreate([
            'zonda' => 'some text',
        ]);

        $this->assertEquals(1, $foo->created_by);
        $this->assertEquals(1, $foo->updated_by);

        Auth::shouldReceive('id')->once()->andReturn(2);

        $foo->forceFill([
            'zonda' => 'some text updated',
        ])->save();

        $this->assertEquals(1, $foo->created_by);
        $this->assertEquals(2, $foo->updated_by);
    }
}

class FooHasUpdater extends Model
{
    use HasUpdater;

    protected $table = 'foos';
}
