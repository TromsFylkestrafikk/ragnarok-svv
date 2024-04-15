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
        'less_than_5,6m',
        'more_than_5,6m',
        '5,6_to_7,6m',
        '7,6_to_12,5m',
        '12,5_to_16,0m',
        '16,0_to_24,0m',
        'more_than_24,0m',
    ];
}
