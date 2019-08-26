<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protexted protected $fillable =[
        'title','date_written','content','features_image',
    'votes_up','votes_down','user_id','category_id'
]
}


