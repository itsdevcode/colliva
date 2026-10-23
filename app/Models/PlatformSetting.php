<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[Fillable(['key', 'value'])]
class PlatformSetting extends Model
{
/** @use HasFactory<\Database\Factories\PlatformSettingFactory> */
use HasFactory;
}
