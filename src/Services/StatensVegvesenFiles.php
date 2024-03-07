<?php

namespace Ragnarok\StatensVegvesen\Services;

use GuzzleHttp\Client as HttpClient;
use Ragnarok\Sink\Traits\LogPrintf;

class StatensVegvesenFiles
{
    use LogPrintf;

    public function __construct()
    {
        $this->logPrintfInit('[StatensVegvesenService]: ');
    }

    public function getData(string $id)
    {
        $this->debug('Fetching traffic data for %s', $id);
        $client = new HttpClient();
        $json = $this->fetchTrafficRegistrationPoints($client);
        $data['trafficRegistrationPoints.json'] = $json;

        // Fetch traffic volume data for each registration point.
        $regPoints = json_decode($json)->data->trafficRegistrationPoints;
        $from = sprintf('%sT00:00Z', $id);
        $to = sprintf('%sT00:00Z', date('Y-m-d', strtotime("$id +1day")));
        $operational = 0;
        $total = 0;
        foreach ($regPoints as $point) {
            $pointId = $point->id;
            $filename = sprintf('%s.json', $pointId);
            $data[$filename] = $this->fetchTrafficVolumeData($client, $pointId, $from, $to);
            $operational += ($point->operationalStatus === 'OPERATIONAL') ? 1 : 0;
            $total += 1;
        }
        $this->debug('Fetched volume data for %d registration points. Operational: %d',
            $total,
            $operational,
        );
        return $data;
    }

    /**
     * Fetch traffic registration points.
     *
     * @param GuzzleHttp\Client $client
     *
     * @return string
     */
    protected function fetchTrafficRegistrationPoints(HttpClient $client): string
    {
        $countyNumber = config('ragnarok_statens_vegvesen.county_number');
        $query = <<<GraphQL
            query {
                trafficRegistrationPoints(
                    searchQuery: {countyNumbers: $countyNumber}
                ) {
                    id
                    name
                    trafficRegistrationType
                    registrationFrequency
                    operationalStatus
                    dataTimeSpan {
                        firstData
                    }
                    location {
                        municipality {
                            name
                            number
                        }
                        county {
                            name
                            number
                            countryPart {
                                name
                            }
                        }
                        coordinates {
                            latLon {
                                lat
                                lon
                            }
                        }
                    }
                }
            }
            GraphQL;

        $response = $client->post(config('ragnarok_statens_vegvesen.base_url'), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'query' => $query,
            ],
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * Fetch traffic volume data.
     *
     * @param GuzzleHttp\Client $client
     * @param string $pointId Traffic registration point ID
     * @param string $from Start date (inclusive)
     * @param string $to End date (exclusive)
     *
     * @return string
     */
    protected function fetchTrafficVolumeData(HttpClient $client, string $pointId, string $from, string $to)
    {
        $query = <<<GraphQL
            query {
                trafficData(
                    trafficRegistrationPointId: "$pointId"
                ) {
                    volume {
                        byDay(from: "$from", to: "$to") {
                            edges {
                                node {
                                    from
                                    to
                                    total {
                                        volumeNumbers {
                                            volume
                                        }
                                        coverage {
                                            percentage
                                        }
                                    }
                                    byLengthRange {
                                        lengthRange {
                                            representation
                                        }
                                        total {
                                            volumeNumbers {
                                                volume
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            GraphQL;

        $response = $client->post(config('ragnarok_statens_vegvesen.base_url'), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'query' => $query,
            ],
        ]);
        return $response->getBody()->getContents();
    }
}
