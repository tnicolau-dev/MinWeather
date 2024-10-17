<?php

$error_message = '';

function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Erro ao tentar obter dados da API.');
    }

    curl_close($ch);
    return $response;
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
        $error_message = 'Token da API não encontrado.';
    } else {
        try {
            $ip = @file_get_contents('https://ifconfig.me');

            if ($ip === false) {
                throw new Exception('Erro ao tentar obter o IP.');
            }

            $url = "https://ipinfo.io/{$ip}?token={$access_key}";

            $json = @file_get_contents($url);
            if ($json === false) {
                throw new Exception('Erro ao tentar obter os detalhes do IP.');
            }

            $details_loc = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
            }

            if (!isset($details_loc['loc'])) {
                throw new Exception('Localização não encontrada nos detalhes do IP.');
            }

            $loc = explode(',', $details_loc['loc']);
            $latitude = $loc[0] ?? null;
            $longitude = $loc[1] ?? null;

            if (!$latitude || !$longitude) {
                throw new Exception('Latitude ou longitude não encontrada.');
            }

            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------

            $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current=temperature_2m,is_day,precipitation,weather_code,wind_speed_10m&hourly=temperature_2m,uv_index&daily=temperature_2m_max,temperature_2m_min,sunrise,sunset,snowfall_sum,precipitation_probability_max,wind_speed_10m_max,wind_direction_10m_dominant&timezone=America%2FSao_Paulo&forecast_days=1";
            $json = fetchUrl($url);

            $data_current = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
            }

            //-------------------------------------------------------------------------------------

            $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&hourly=temperature_2m,precipitation_probability,weather_code&timezone=America%2FSao_Paulo&forecast_days=3";
            $json = fetchUrl($url);

            $data_current_hr = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
            }

            if (!isset($data_current["current"]["time"])) {
                throw new Exception('Hora atual não localizada no retorno da API atual.');
            }

            $dateTime_c = new DateTime($data_current["current"]["time"]);
            $dateTime_c->setTime($dateTime_c->format('H'), 0);
            $hora_at_r = $dateTime_c->format('Y-m-d\TH:i');

            if (!isset($data_current_hr["hourly"]["time"])) {
                throw new Exception('Hora atual não localizada no retorno da API por hora.');
            }
                
            $index_s = array_search($hora_at_r, $data_current_hr["hourly"]["time"]);

            if ($index_s !== false) {

                $data_current_hr_at = [];
                $data_current_hr_at_gr = [];
        
                for ($i = $index_s; $i < $index_s + 13 && $i < count($data_current_hr['hourly']['time']); $i++) {

                    if (!isset($data_current_hr['hourly']['weather_code'][$i])) {
                        throw new Exception('Index código do clima não localizado.');
                    }

        
                    $weather_c = $data_current_hr['hourly']['weather_code'][$i];


                    if (!isset($data_current_hr['hourly']['time'][$i])) {
                        throw new Exception('Index hora não localizado.');
                    }
                    if (!isset($data_current_hr['hourly']['temperature_2m'][$i])) {
                        throw new Exception('Index temperatura não localizado.');
                    }
                    if (!isset($data_current_hr['hourly']['precipitation_probability'][$i])) {
                        throw new Exception('Index chuva não localizado.');
                    }
        
                    $data_current_hr_at[] = [
                        'time' => $data_current_hr['hourly']['time'][$i],
                        'temperature_2m' => $data_current_hr['hourly']['temperature_2m'][$i],
                        'precipitation_probability' => $data_current_hr['hourly']['precipitation_probability'][$i],
                        'weather_code' => $weather_c,
                    ];
        
                    $dateTime_gr = new DateTime($data_current_hr['hourly']['time'][$i]);

                    if (!isset($data_current['daily']['sunrise'][0])) {
                        throw new Exception('Dados do nascer do sol não localizado.');
                    }
                    if (!isset($data_current['daily']['sunset'][0])) {
                        throw new Exception('Dados do pôr do sol não localizado.');
                    }
        
                    $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
                    $time1 = $dateTime1->format('H');
                    $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
                    $time2 = $dateTime2->format('H');
        
                    $current_day_time = (intval($dateTime_gr->format('H')) >= $time2 or intval($dateTime_gr->format('H')) <= $time1) ? 'night' : 'day';
        

                    if (!isset($data_current_hr['hourly']['temperature_2m'][$i])) {
                        throw new Exception('Index temperatura não localizado.');
                    }
                    if (!isset($weather_codes_translated[$weather_c][$current_day_time]["image"])) {
                        throw new Exception('Index imagem não localizado.');
                    }
                    if (!isset($data_current_hr['hourly']['precipitation_probability'][$i])) {
                        throw new Exception('Index probabilidade de chuva não localizado.');
                    }

                    $data_current_hr_at_gr['time'][$i] = $dateTime_gr->format('H');
                    $data_current_hr_at_gr['temperature_2m'][$i] = $data_current_hr['hourly']['temperature_2m'][$i];
                    $data_current_hr_at_gr['image'][$i] = $weather_codes_translated[$weather_c][$current_day_time]["image"];
                    $data_current_hr_at_gr['precipitation'][$i] = $data_current_hr['hourly']['precipitation_probability'][$i];
                }

                //-------------------------------------------------------------------------------------

                $url = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max&timezone=America%2FSao_Paulo";
                $json = fetchUrl($url);

                $data_current_week = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
                }

            } else{
                throw new Exception('Hora atual não localizada.');
            }

            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------------

        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

?>