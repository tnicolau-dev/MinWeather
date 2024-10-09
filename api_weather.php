<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//$ip = $_SERVER['REMOTE_ADDR']; // Obtém o IP do usuário
$access_key = $_ENV['API_TOKEN'];

$ip = file_get_contents('https://ifconfig.me');
//$ip = '128.201.136.0';
$url = "https://ipinfo.io/{$ip}?token={$access_key}";

$json = file_get_contents($url);
$details_loc = json_decode($json, true);

$loc = explode(',', $details_loc['loc']);
$latitude = $loc[0];
$longitude = $loc[1];


date_default_timezone_set($details_loc['timezone']);

//echo "Latitude: $latitude, Longitude: $longitude";

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

//variáveis do clima atual

$url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current=temperature_2m,is_day,precipitation,weather_code,wind_speed_10m&hourly=temperature_2m&daily=temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max,snowfall_sum,precipitation_probability_max,wind_speed_10m_max,wind_direction_10m_dominant&timezone=America%2FSao_Paulo&forecast_days=1";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    echo "Erro na requisição: " . curl_error($ch);
} else {
    $data_current = json_decode($response, true);
}

curl_close($ch);

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

//variáveis do clima por hora

$url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&hourly=temperature_2m,precipitation_probability,weather_code&timezone=America%2FSao_Paulo&forecast_days=3";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    echo "Erro na requisição: " . curl_error($ch);
} else {
    $data_current_hr = json_decode($response, true);

    $dateTime_c = new DateTime($data_current["current"]["time"]);
    $dateTime_c->setTime($dateTime_c->format('H'), 0);
    $hora_at_r = $dateTime_c->format('Y-m-d\TH:i');

    $index_s = array_search($hora_at_r, $data_current_hr["hourly"]["time"]);

    if ($index_s !== false) {

        $data_current_hr_at = [];
        $data_current_hr_at_gr = [];

        for ($i = $index_s; $i < $index_s + 12 && $i < count($data_current_hr['hourly']['time']); $i++) {

            $weather_c = $data_current_hr['hourly']['weather_code'][$i];

            $data_current_hr_at[] = [
                'time' => $data_current_hr['hourly']['time'][$i],
                'temperature_2m' => $data_current_hr['hourly']['temperature_2m'][$i],
                'precipitation_probability' => $data_current_hr['hourly']['precipitation_probability'][$i],
                'weather_code' => $weather_c,
            ];

            $dateTime_gr = new DateTime($data_current_hr['hourly']['time'][$i]);

            $data_current_hr_at_gr['time'][$i] = $dateTime_gr->format('H');
            $data_current_hr_at_gr['temperature_2m'][$i] = $data_current_hr['hourly']['temperature_2m'][$i];
            $data_current_hr_at_gr['image'][$i] = $weather_codes_translated[$weather_c]["day"]["image"];
            $data_current_hr_at_gr['precipitation'][$i] = $data_current_hr['hourly']['precipitation_probability'][$i];
        }
    }
}

curl_close($ch);

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

//variáveis do clima da semana

$url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max&timezone=America%2FSao_Paulo";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    echo "Erro na requisição: " . curl_error($ch);
} else {
    $data_current_week = json_decode($response, true);
}

curl_close($ch);

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

function diaDaSemanaEmPortugues($diaEmIngles) {
    switch (strtolower($diaEmIngles)) {
        case 'monday':
            return 'Segunda';
        case 'tuesday':
            return 'Terça';
        case 'wednesday':
            return 'Quarta';
        case 'thursday':
            return 'Quinta';
        case 'friday':
            return 'Sexta';
        case 'saturday':
            return 'Sábado';
        case 'sunday':
            return 'Domingo';
        default:
            return 'Dia inválido';
    }
}

?>