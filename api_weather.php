<?php

include "./components/timezones.php";

$error_message = '';

function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Erro ao tentar obter dados do clima.');
    }

    curl_close($ch);
    return $response;
}

function busca_timezone($sigla){
    global $timeZones;
    return isset($timeZones[$sigla]) ? $timeZones[$sigla] : 'America/Sao_Paulo';
}

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------

if (isset($_GET['latitude']) and isset($_GET['latitude'])) {

    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];
    $details_loc["region"] = $_GET['region'];
    $details_loc["country"] = $_GET['country_code'];
    
    if($_GET['city'] != ''){
        $details_loc["city"] = $_GET['city'];
    } else if($_GET['county'] != ''){
        $details_loc["city"] = $_GET['county'];
    } else if($_GET['region'] != ''){
        $details_loc["city"] = $_GET['region'];
    } else {
        $details_loc["city"] = $_GET['country'];
    }

    $timezone_l = busca_timezone($details_loc["country"]);

}

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------


if (!isset($latitude)) {

    require_once __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $access_key = $_ENV['API_TOKEN'] ?? null;

    if (!$access_key) {
        $error_message = 'Erro ao tentar obter credenciais';
    } else {
        try {
            $ip = @file_get_contents('https://ifconfig.me');

            if ($ip === false) {
                throw new Exception('Erro ao tentar obter dados da localização.');
            }

            $url = "https://ipinfo.io/{$ip}?token={$access_key}";

            $json = @file_get_contents($url);
            if ($json === false) {
                throw new Exception('Erro ao tentar obter dados da localização.');
            }

            $details_loc = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Dados inválidos - #J001');
            }

            if (!isset($details_loc['loc'])) {
                throw new Exception('Localização não encontrada.');
            }

            $loc = explode(',', $details_loc['loc']);
            $latitude = $loc[0] ?? null;
            $longitude = $loc[1] ?? null;

            if (!$latitude || !$longitude) {
                throw new Exception('Dados da localização não encontrados.');
            }

            if (!isset($details_loc["country"])) {
                throw new Exception('Dados da localização não encontrados.');
            }

            $timezone_l = busca_timezone($details_loc["country"]);
            //$timezone_l = 'America/Sao_Paulo';

            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------

            valida:

            date_default_timezone_set($timezone_l);

            $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current=temperature_2m,is_day,precipitation,weather_code,wind_speed_10m&hourly=temperature_2m,uv_index&daily=temperature_2m_max,temperature_2m_min,sunrise,sunset,snowfall_sum,precipitation_probability_max,wind_speed_10m_max,wind_direction_10m_dominant&timezone=$timezone_l&forecast_days=1";
            $json = fetchUrl($url);

            $data_current = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Dados inválidos - #J002');
            }

            //-------------------------------------------------------------------------------------

            $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&hourly=temperature_2m,precipitation_probability,weather_code&timezone=$timezone_l&forecast_days=3";
            $json = fetchUrl($url);

            $data_current_hr = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Dados inválidos - #J003');
            }

            if (!isset($data_current["current"]["time"])) {
                throw new Exception('Dados inválidos - #001.');
            }

            $dateTime_c = new DateTime();
            $dateTime_c->setTime($dateTime_c->format('H'), 0);
            $hora_at_r = $dateTime_c->format('Y-m-d\TH:i');


            if (!isset($data_current_hr["hourly"]["time"])) {
                throw new Exception('Dados inválidos - #002.');
            }
                
            $index_s = array_search($hora_at_r, $data_current_hr["hourly"]["time"]);

            if ($index_s !== false) {

                $data_current_hr_at = [];
                $data_current_hr_at_gr = [];
        
                for ($i = $index_s; $i < $index_s + 13 && $i < count($data_current_hr['hourly']['time']); $i++) {

                    if (!isset($data_current_hr['hourly']['weather_code'][$i])) {
                        throw new Exception('Dados inválidos - #003.');
                    }

        
                    $weather_c = $data_current_hr['hourly']['weather_code'][$i];


                    if (!isset($data_current_hr['hourly']['time'][$i])) {
                        throw new Exception('Dados inválidos - #004.');
                    }
                    if (!isset($data_current_hr['hourly']['temperature_2m'][$i])) {
                        throw new Exception('Dados inválidos - #005.');
                    }
                    if (!isset($data_current_hr['hourly']['precipitation_probability'][$i])) {
                        throw new Exception('Dados inválidos - #006.');
                    }
        
                    $data_current_hr_at[] = [
                        'time' => $data_current_hr['hourly']['time'][$i],
                        'temperature_2m' => $data_current_hr['hourly']['temperature_2m'][$i],
                        'precipitation_probability' => $data_current_hr['hourly']['precipitation_probability'][$i],
                        'weather_code' => $weather_c,
                    ];
        
                    $dateTime_gr = new DateTime($data_current_hr['hourly']['time'][$i]);

                    if (!isset($data_current['daily']['sunrise'][0])) {
                        throw new Exception('Dados inválidos - #007.');
                    }
                    if (!isset($data_current['daily']['sunset'][0])) {
                        throw new Exception('Dados inválidos - #008.');
                    }
        
                    $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
                    $time1 = $dateTime1->format('H');
                    $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
                    $time2 = $dateTime2->format('H');
        
                    $current_day_time = (intval($dateTime_gr->format('H')) >= $time2 or intval($dateTime_gr->format('H')) <= $time1) ? 'night' : 'day';
        

                    if (!isset($data_current_hr['hourly']['temperature_2m'][$i])) {
                        throw new Exception('Dados inválidos - #009.');
                    }
                    if (!isset($weather_codes_translated[$weather_c][$current_day_time]["image"])) {
                        throw new Exception('Dados inválidos - #010.');
                    }
                    if (!isset($data_current_hr['hourly']['precipitation_probability'][$i])) {
                        throw new Exception('Dados inválidos - #011.');
                    }

                    $data_current_hr_at_gr['time'][$i] = $dateTime_gr->format('H');
                    $data_current_hr_at_gr['temperature_2m'][$i] = $data_current_hr['hourly']['temperature_2m'][$i];
                    $data_current_hr_at_gr['image'][$i] = $weather_codes_translated[$weather_c][$current_day_time]["image"];
                    $data_current_hr_at_gr['precipitation'][$i] = $data_current_hr['hourly']['precipitation_probability'][$i];
                }

                //-------------------------------------------------------------------------------------

                $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max&timezone=$timezone_l";
                $json = fetchUrl($url);

                $data_current_week = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Dados inválidos - #J003');
                }

            } else{
                throw new Exception('Dados inválidos - #012.');
            }

            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------

        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }

} else {
    
    goto valida;
}

?>