<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'url', 'icon', 'color', 'order_index', 'is_active'])]
class QuickLink extends Model
{
    //
}
