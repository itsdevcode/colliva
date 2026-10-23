<?php

namespace App\Models;

use Database\Factories\CampaignPlatformFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['campaign_id', 'platform'])]
class CampaignPlatform extends Model
{
    /** @use HasFactory<CampaignPlatformFactory> */
    use HasFactory;

    public $timestamps = false;
}
