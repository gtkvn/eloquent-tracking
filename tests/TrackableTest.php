<?php

namespace Gtk\EloquentTracking\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Events\Dispatcher;
use Gtk\EloquentTracking\Trackable;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class TrackableTest extends TestCase
{
    public function setUp()
    {
        $db = new DB;

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $db->setEventDispatcher(new Dispatcher(new Container));

        $db->setAsGlobal();

        $db->bootEloquent();

        DB::schema()->create('foos', function ($table) {
            $table->increments('id');
            $table->string('zonda');
            $table->timestamps();
        });

        DB::schema()->create('model_tracking_logs', function ($table) {
            $table->increments('id');
            $table->bigInteger('trackable_id')->unsigned();
            $table->string('trackable_type');
            $table->string('action');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_can_log_model_change_tracking()
    {
        Auth::shouldReceive('id')
            ->once()
            ->andReturn(1);

        $foo = Foo::forceCreate([
            'zonda' => 'some text',
        ]);

        $this->assertDatabaseHas('model_tracking_logs', [
            'trackable_id' => $foo->id,
            'trackable_type' => 'Gtk\EloquentTracking\Tests\Foo',
            'action' => 'create',
            'before' => '[]',
            'after' => $foo->toJson(),
            'user_id' => 1,
        ]);

        $foo->zonda = 'some text updated';
        $foo->save();

        $this->assertDatabaseHas('model_tracking_logs', [
            'trackable_id' => $foo->id,
            'trackable_type' => 'Gtk\EloquentTracking\Tests\Foo',
            'action' => 'update',
            'before' => json_encode(['zonda' => 'some text']),
            'after' => json_encode(['zonda' => 'some text updated']),
            'user_id' => 1,
        ]);

        $foo->delete();

        $this->assertDatabaseHas('model_tracking_logs', [
            'trackable_id' => $foo->id,
            'trackable_type' => 'Gtk\EloquentTracking\Tests\Foo',
            'action' => 'delete',
            'before' => $foo->toJson(),
            'after' => null,
            'user_id' => 1,
        ]);
    }

    public function assertDatabaseHas($table, $data)
    {
        $instance = DB::table($table);

        foreach ($data as $key => $value) {
            $instance = $instance->where($key, $value);
        }

        $this->assertCount(1, $instance->get());
    }
}

class Foo extends Model
{
    use Trackable;

    protected $table = 'foos';
}
