<?php

namespace Ragnarok\Svv\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficData extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'svv_traffic';
    protected $fillable = [
        'chunk_date',
        'point_id',
        'point_name',
        'municipality_name',
        'municipality_number',
        'county_name',
        'county_number',
        'latitude',
        'longitude',
        'traffic_reg_type',
        'reg_frequency',
        'operational_status',
        'total_volume',
        'total_coverage',
        'length_under_5_6',
        'length_over_5_6',
        'length_5_6__7_6',
        'length_7_6__12_5',
        'length_12_5__16',
        'length_16__24',
        'length_over_24',
    ];
}
