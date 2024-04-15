<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('svv_traffic', function (Blueprint $table)
        {
            $table->date('chunk_date')->index()->comment('The dated chunk this transaction belongs to');
            $table->string('point_id')->comment('Traffic registration point ID');
            $table->string('point_name')->comment('Traffic registration point name');
            $table->string('municipality_name')->comment('Municipality name');
            $table->integer('municipality_number')->comment('The ISO 3166-2:NO number of the municipality');
            $table->string('county_name')->comment('County name');
            $table->integer('county_number')->comment('The ISO 3166-2:NO number of the county');
            $table->double('latitude')->comment('Geographical location (latitude) in the WGS 84 reference system');
            $table->double('longitude')->comment('Geographical location (longitude) in the WGS 84 reference system');
            $table->string('traffic_reg_type')->comment('Type of registered traffic');
            $table->string('reg_frequency')->comment('Registration frequency (continuous or periodic)');
            $table->string('operational_status')->comment('Status (operational, retired or temporarily out of service)');
            $table->integer('total_volume')->nullable()->comment('Total traffic volume for this day');
            $table->float('total_coverage')->nullable()->comment('Coverage for the period (percentage)');
            $table->integer('less_than_5,6m')->nullable()->comment('Light vehicles (less than 5.6 meters)');
            $table->integer('more_than_5,6m')->nullable()->comment('Heavy vehicles (more than 5.6 meters)');
            $table->integer('5,6_to_7,6m')->nullable()->comment('Vehicles between 5.6 and 7.6 meters');
            $table->integer('7,6_to_12,5m')->nullable()->comment('Vehicles between 7.6 and 12.5 meters');
            $table->integer('12,5_to_16,0m')->nullable()->comment('Vehicles between 12.5 and 16.0 meters');
            $table->integer('16,0_to_24,0m')->nullable()->comment('Vehicles between 16.0 and 24.0 meters');
            $table->integer('more_than_24,0m')->nullable()->comment('Vehicles longer than 24.0 meters');
            $table->primary([
                'chunk_date',
                'point_id',
            ], 'traffic_volume_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('svv_traffic');
    }
};
