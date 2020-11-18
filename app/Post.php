<?php

namespace App;

use App\User;
use App\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'user_id', 'title', 'slug' ,'body', 'cover_image', 'tag_id', 'status', 'views'
    ];
    protected $casts = [
        'created_at' => 'datetime:M d, Y'
    ];

    /**
     * Getters
     */
    public function getCoverImageAttribute($value)
    {
        return 'http://admin.innoutproperty.com.ng/storage/' . $value;
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function tag()
    {
    	return $this->belongsTo(Tag::class);
    }
}
