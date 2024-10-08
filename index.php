<?php

include "weather_codes.php";
include "api_weather.php";

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

        <?php
            $current_day_time = $data_current['current']['is_day'] == 1 ? 'day' : 'night';
            $code_c = $data_current['current']['weather_code'];

            if (isset($weather_codes_translated[$code_c][$current_day_time])) {
                $desc_c = $weather_codes_translated[$code_c][$current_day_time]["description"];
            } else {
                $desc_c = "Código ou período do dia não encontrado.";
            }
        ?>

        <p>Descrição do clima - <span><?php echo $desc_c ?></span></p>

        <p>Imagem clima</p>
        <img src="<?php echo $weather_codes_translated[$code_c][$current_day_time]["image"]; ?>" alt="">

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