<?php

namespace Gtk\EloquentTracking\Tests;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->db = new DB;

        $this->db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->db->setEventDispatcher(new Dispatcher(new Container));

        $this->db->setAsGlobal();

        $this->db->bootEloquent();
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
