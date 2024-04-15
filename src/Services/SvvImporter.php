<?php

namespace Ragnarok\Svv\Services;

use Illuminate\Support\Carbon;
use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Svv\Models\TrafficData;

class SvvImporter
{
    use LogPrintf;

    /**
     * Traffic registration points.
     *
     * @var array
     */
    protected $regPoints = [];

    public function __construct(string $folder = null)
    {
        $this->logPrintfInit('[SvvImporter]: ');
        if (!$folder) return;

        // Load traffic registration points.
        $file = sprintf('%s/trafficRegistrationPoints.json', $folder);
        $regPoints = json_decode(file_get_contents($file))->data->trafficRegistrationPoints;
        foreach ($regPoints as $point) {
            $id = $point->id;
            $this->regPoints[$id]['name'] =                 $point->name;
            $this->regPoints[$id]['county_name'] =          $point->location->county->name;
            $this->regPoints[$id]['county_number'] =        $point->location->county->number;
            $this->regPoints[$id]['municipality_name'] =    $point->location->municipality->name;
            $this->regPoints[$id]['municipality_number'] =  $point->location->municipality->number;
            $this->regPoints[$id]['latitude'] =             $point->location->coordinates->latLon->lat;
            $this->regPoints[$id]['longitude'] =            $point->location->coordinates->latLon->lon;
            $this->regPoints[$id]['traffic_reg_type'] =     $point->trafficRegistrationType;
            $this->regPoints[$id]['reg_frequency'] =        $point->registrationFrequency;
            $this->regPoints[$id]['operational_status'] =   $point->operationalStatus;
        }
    }

    /**
     * Importing traffic volume data from json file.
     *
     * @param string $chunkId Chunk ID. Date on format YYYY-MM-DD
     * @param string $file Path to source file
     *
     * @return integer Number of records imported
     */
    public function import(string $chunkId, string $file)
    {
        if (strpos($file, 'trafficRegistrationPoints') === false) {
            $id = basename($file, '.json');
            if (!isset($this->regPoints[$id])) {
                $this->error('No registration point data with ID %s loaded!', $id);
                return 0;
            }
            $data = json_decode(file_get_contents($file))->data->trafficData->volume->byDay;
            $node = null;
            if (count($data->edges) > 0) {
                $node = $data->edges[0]->node;
            }
            TrafficData::create([
                'chunk_date'            => new Carbon($chunkId),
                'point_id'              => $id,
                'point_name'            => $this->regPoints[$id]['name'],
                'municipality_name'     => $this->regPoints[$id]['municipality_name'],
                'municipality_number'   => $this->regPoints[$id]['municipality_number'],
                'county_name'           => $this->regPoints[$id]['county_name'],
                'county_number'         => $this->regPoints[$id]['county_number'],
                'latitude'              => $this->regPoints[$id]['latitude'],
                'longitude'             => $this->regPoints[$id]['longitude'],
                'traffic_reg_type'      => $this->regPoints[$id]['traffic_reg_type'],
                'reg_frequency'         => $this->regPoints[$id]['reg_frequency'],
                'operational_status'    => $this->regPoints[$id]['operational_status'],
                'total_volume'          => $node?->total->volumeNumbers->volume,
                'total_coverage'        => $node?->total->coverage->percentage,
                'less_than_5,6m'        => $this->getVolumeByVehicleLength('..,5.6', $node?->byLengthRange),
                'more_than_5,6m'        => $this->getVolumeByVehicleLength('5.6,..', $node?->byLengthRange),
                '5,6_to_7,6m'           => $this->getVolumeByVehicleLength('5.6,7.6', $node?->byLengthRange),
                '7,6_to_12,5m'          => $this->getVolumeByVehicleLength('7.6,12.5', $node?->byLengthRange),
                '12,5_to_16,0m'         => $this->getVolumeByVehicleLength('12.5,16.0', $node?->byLengthRange),
                '16,0_to_24,0m'         => $this->getVolumeByVehicleLength('16.0,24.0', $node?->byLengthRange),
                'more_than_24,0m'       => $this->getVolumeByVehicleLength('24.0,..', $node?->byLengthRange),
            ]);
            return 1;
        }
        return 0;
    }

    protected function getVolumeByVehicleLength(string $id, array|null $ranges): int|null
    {
        if (isset($ranges)) {
            foreach ($ranges as $range) {
                if (strpos($range->lengthRange->representation, $id) === 1) {
                    return $range->total->volumeNumbers->volume;
                }
            }
        }
        return null;
    }

    /**
     * Deleting imported transactions with the specified chunk ID/date.
     *
     * @param string $chunkId Chunk ID. Date on format YYYY-MM-DD
     *
     * @return $this
     */
    public function deleteImport(string $chunkId)
    {
        $count = TrafficData::whereDate('chunk_date', $chunkId)->delete();
        $this->debug('Deleted %d records with chunk date %s', $count, $chunkId);
        return $this;
    }
}
