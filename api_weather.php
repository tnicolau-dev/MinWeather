<?php

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

//variáveis do clima atual

$url = "https://api.open-meteo.com/v1/forecast?latitude=-23.6489&longitude=-46.8522&current=temperature_2m,is_day,precipitation,weather_code,wind_speed_10m&daily=temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max,precipitation_probability_max,wind_speed_10m_max,wind_direction_10m_dominant&timezone=America%2FSao_Paulo&forecast_days=1";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    echo "Erro na requisição: " . curl_error($ch);
} else {
    $data_current = json_decode($response, true);
    //print_r($data);
}

curl_close($ch);

//var_export($data_current);

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

?>