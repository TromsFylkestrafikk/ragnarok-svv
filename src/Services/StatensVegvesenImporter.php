<?php

namespace Ragnarok\StatensVegvesen\Services;

use Ragnarok\Sink\Traits\LogPrintf;

class StatensVegvesenImporter
{
    use LogPrintf;

    /**
     * Traffic registration points.
     *
     * @var array
     */
    protected $regPoints = [];

    public function __construct(string $folder)
    {
        $this->logPrintfInit('[StatensVegvesenImporter]: ');

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
            $data = json_decode(file_get_contents($file))->data->trafficData->volume->byDay;
            $hasData = (count($data->edges) > 0);
            return 1;
        }
        return 0;
    }
}
