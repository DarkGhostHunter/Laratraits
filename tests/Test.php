<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class Test extends TestCase
{
    public function tests_sample()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Post::forceCreate(['title' => 'Cool!', 'user_id' => 1]);

        Gate::policy(Post::class, PostPolicy::class);

        $this->actingAs(User::forceCreate(['name' => 'john']));

        Route::get('post/{post}', fn(Post $post) => 'ok')
            ->middleware('web', 'can:see,post');

        $this->get('post/1')->assertOk();
    }
}

class User extends Authenticatable
{

}

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class PostPolicy
{
    public function see(User $user, Post $post)
    {
        return true;
    }
}
