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

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clima Tempo</title>
</head>
<body>
    <h1>Informações atuais do clima</h1>
    <hr>
    <br>
    <div>
        <p>Está de dia? - <span><?php echo $data_current['current']['is_day'] == 1 ? 'Dia' : 'Noite' ?></span></p>
        <p>Chuva atual - <span><?php echo $data_current['current']['precipitation'] . ' ' . $data_current['current_units']['precipitation'] ?></span></p>
        <p>Código do clima - <span><?php echo $data_current['current']['weather_code'] ?></span></p>
        <p>Velocidade do Vento Atual - <span><?php echo $data_current['current']['wind_speed_10m'] . ' ' . $data_current['current_units']['wind_speed_10m'] ?></span></p>
    </div>

    <p>---------------------------------------</p>

    <div>
        <p>Chace de chuva hoje - <span><?php echo $data_current['daily']['precipitation_probability_max'][0] . ' ' . $data_current['daily_units']['precipitation_probability_max'] ?></span></p>
        <p>Temperatura de hoje - <span><?php echo $data_current['daily']['temperature_2m_max'][0] . ' ' . $data_current['daily_units']['temperature_2m_max'] . ' - ' . $data_current['daily']['temperature_2m_min'][0] . ' ' . $data_current['daily_units']['temperature_2m_min'] ?></span></p>
        
        <?php
            $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
            $time1 = $dateTime1->format('H:i');

            $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
            $time2 = $dateTime2->format('H:i');
        ?>

        <p>Nascer e Por do Sol - <span><?php echo $time1 . ' ' . $time2 ?></span></p>
        <p>Uv - <span><?php echo $data_current['daily']['uv_index_max'][0] ?></span></p>
        <p>Velocidade do Vento Max. - <span><?php echo $data_current['daily']['wind_speed_10m_max'][0] . ' ' . $data_current['daily_units']['wind_speed_10m_max'] ?></span></p>
        <p>Direção do Vento - <span><?php echo $data_current['daily']['wind_direction_10m_dominant'][0] . ' ' . $data_current['daily_units']['wind_direction_10m_dominant'] ?></span></p>
    </div>
</body>
</html>