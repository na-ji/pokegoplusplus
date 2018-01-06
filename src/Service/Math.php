<?php


namespace App\Service;


class Math
{
    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     *
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int   $earthRadius Mean earth radius in [km]
     * @return float Distance between points in [km] (same as earthRadius)
     */
    public static function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371
    )
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Calculates the cooldown to wait depending on the distance
     *
     * @param float $distance Distance in km
     * @return string
     */
    public static function distanceToCooldown($distance)
    {
        $distanceToCooldownArray = [
            ['d' => 1,    't' => '30 sec',    'o' => 30,   'm' => 0],
            ['d' => 5,    't' => '2 mins',    'o' => 120,  'm' => 0],
            ['d' => 10,   't' => '6 mins',    'o' => 360,  'm' => 0],
            ['d' => 25,   't' => '11 mins',   'o' => 660,  'm' => 0],
            ['d' => 30,   't' => '<14 mins',  'o' => 840,  'm' => -1],
            ['d' => 65,   't' => '<22 mins',  'o' => 1320, 'm' => -1],
            ['d' => 81,   't' => '<25 mins',  'o' => 1500, 'm' => -1],
            ['d' => 100,  't' => '<35 mins',  'o' => 2100, 'm' => -1],
            ['d' => 250,  't' => '< 45 mins', 'o' => 2700, 'm' => -1],
            ['d' => 500,  't' => '1h',        'o' => 3600, 'm' => 0],
            ['d' => 750,  't' => '1h15',      'o' => 4500, 'm' => 0],
            ['d' => 1000, 't' => '1h30',      'o' => 5400, 'm' => 0],
            ['d' => 1500, 't' => '2h',        'o' => 7200, 'm' => 0],
        ];

        foreach ($distanceToCooldownArray as $row) {
            if($distance <= $row['d']) {
                return $row['t'];
            }
        }

        return $distanceToCooldownArray[count($distanceToCooldownArray) - 1]['t'];
    }
}
