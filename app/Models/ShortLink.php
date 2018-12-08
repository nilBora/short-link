<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShortLink extends Model
{
    public static function getMaxSourceID($fieldName = 'id')
    {
        return DB::table('short_links')->max($fieldName);
    }
}
