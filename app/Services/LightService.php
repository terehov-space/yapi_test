<?php

namespace App\Services;

use GuzzleHttp\RequestOptions;

class LightService
{
    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function getDevicesList($token)
    {
        $response = $this->client->get('https://api.iot.yandex.net/v1.0/user/info', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $responseData = json_decode((string)$response->getBody(), true);

        $deviceIds = [];

        foreach ($responseData['devices'] as $device) {
            if ($device['type'] == 'devices.types.light') {
                $deviceIds[] = $device;
            }
        }

        return $deviceIds;
    }

    public function setDevicesColor($token, $devices = [], $r = 0, $g = 0, $b = 0)
    {
        $color = $this->rgbToHsv($r, $g, $b);

        $command = [
            'devices' => [],
        ];

        foreach ($devices as $device) {
            $command['devices'][] = [
                'id' => $device['id'],
                'actions' => [
                    [
                        'type' => 'devices.capabilities.color_setting',
                        'state' => [
                            'instance' => 'hsv',
                            'value' => $color,
                        ],
                    ],
                ],
            ];
        }

        $this->client->post('https://api.iot.yandex.net/v1.0/devices/actions', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            RequestOptions::JSON => $command,
        ]);
    }

    public function rgbToHsv($r, $g, $b)
    {
        $r = ($r / 255);
        $g = ($g / 255);
        $b = ($b / 255);

        $maxRGB = max($r, $g, $b);
        $minRGB = min($r, $g, $b);
        $chroma = $maxRGB - $minRGB;

        $computedV = 100 * $maxRGB;

        if ($chroma == 0)
            return array(0, 0, $computedV);

        $computedS = 100 * ($chroma / $maxRGB);

        if ($r == $minRGB)
            $h = 3 - (($g - $b) / $chroma);
        elseif ($b == $minRGB)
            $h = 1 - (($r - $g) / $chroma);
        else // $g == $minRGB
            $h = 5 - (($b - $r) / $chroma);

        $computedH = 60 * $h;

        return ['h' => round($computedH), 's' => round($computedS), 'v' => round($computedV)];
    }
}
