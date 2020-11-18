<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
    	'name'
    ];

    public function post()
    {
    	return $this->hasMany(Post::class);
    }
}
