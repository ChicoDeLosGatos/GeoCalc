<?php
class GeoCalc 
{
    private $city;
    private $zipcode;
    private $latitude;
    private $longitude;
    private $unit;
    
    public const MILES = 69.05482;
    public const KILOMETRES = 111.13384;
    public const N_MILES = 59.97662;
     
    /*
GeoCalc constructor.
This function constructs the GeoCalc object with the name or the zipcode of an existent city, and sets the unit constant, it can be km, miles or nautical miles. 
It will throw an exception in case of null name and null zipcode.
@params String city NULLABLE, Integer zipcode NULLABLE, Double unit.
*/
    public __construct($city = null, $zipcode = null, $unit){
        $this->city = $city;
        $this->zipcode = $zipcode;
        if ($this->name) $latlon = $this->ws_get_lat_lon_city_name($this->name);
        else if ($this->zipcode)  $latlon = $this->ws_get_lat_lon_zipcode($this->zipcode);
            else throw new Exception('No city provided in the constructor.');
            $this->latitude = $latlon[0];
        $this->longitude = $latlon[1];
        $this->unit = $unit;
    }
    
    /*
Gets the latitude of the city targeted in the GeoCalc Object.
@return Double latitude
*/
    public function getLatitude(){
        return $this->latitude;
    }
    
    /*
Gets the longitude of the city targeted in the GeoCalc Object.
@return Double latitude
*/
    public function getLongitude(){
        return $this->longitude;
    }
    
    /*
Gets the numerical value of KM between another GeoCalc object.
@params GeoCalc geocity
@return Double distanceKM
*/
    public function getDistanceFrom(GeoCalc $geocity){
        return $this->calc_dist_between_two_points($this->latitude, $this->longitude, $geocity->getLatitude(), $geocity->getLongitude());
    }
    
    /*
Gets true if the distanceKM of the given GeoCalc object is under or equal the max distance given value. Otherwise returns false.
@params GeoCalc geocity, Double max_distance
@return Boolean near.
*/
    public function isNear(GeoCalc $geocity, $max_distance)
    {
        return ($this->getDistanceFrom($geocity) <= $max_distance);
    }
    
    /*
Sends a WebService query to https://nominatim.openstreetmap.org/ to get the latitude and the longitude of the city that references the given zipcode.
@params Integer zipcode
@return Array[Double latitude, Double longitude].
*/
    private function ws_get_lat_lon_zipcode($zipcode)
    {
        ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
        $url = 'https://nominatim.openstreetmap.org/search?q=' . $zipcode . ',ES&format=xml&addressdetails=0';
        $response = file_get_contents($url);
        $new_zc = $zipcode;
        while (strlen($response) <= 338) {
            $new_zc = $new_zc + 1;
            $url = 'https://nominatim.openstreetmap.org/search?q=' . $new_zc . ',ES&format=xml&addressdetails=0';
            $response = file_get_contents($url);
        }
        $xml = simplexml_load_string($response);
        return array('latitude' => $xml->place['lat'][0],
                     'longitude' => $xml->place['lon'][0],
                    );
    }
    
    /*
Sends a WebService query to https://geocode-maps.yandex.ru/ to get the latitude and the longitude of the city that references the given city name.
@params String city_name
@return Array[Double latitude, Double longitude].
*/
    private function ws_get_lat_lon_city_name($city_name)
    {
        $url = 'https://geocode-maps.yandex.ru/1.x/?geocode=' . $city_name . '&lang=es-ES';
        $response = file_get_contents($url);
        $xml = simplexml_load_string($response);
        $position = (array) $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos[0];
        $latlon = explode(' ', $position[0]);
        return array('latitude' => $latlon[1],
                     'longitude' => $latlon[0],
                    );
    }
    
    /*
Calcs the numerical value of the distance between two points given
@params Double latitude_city_1, Double laongitude_city_1, Double latitude_city_2, Double longitude_city_2
@return Double distance
*/
    private function calc_dist_between_two_points($point1_lat, $point1_lon, $point2_lat, $point2_lon)
    {
        $degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_lon - $point2_lon)))));
        $distance = $degrees * $this->unit * 1.1515 * 1.609344;
        return $distance;
    }
}

?>
