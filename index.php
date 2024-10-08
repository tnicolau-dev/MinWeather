<?php


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

include "weather_codes.php";
include "api_weather.php";


$times = array_values($data_current_hr_at_gr['time']);
$temperatures = array_values($data_current_hr_at_gr['temperature_2m']);

$times_json = json_encode($times);
$temperatures_json = json_encode($temperatures);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clima Tempo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<style>
    .hora_clima {
        display: flex;
        justify-content: space-around;
    }
</style>

<body>
    <h1>Informações atuais do clima</h1>
    <hr>
    <br>
    <div>
        <p>Temperatura atual - <span><?php echo $data_current['current']['temperature_2m'] . ' ' . $data_current['current_units']['temperature_2m'];  ?></span></p>
        <p>Está de dia? - <span><?php echo $data_current['current']['is_day'] == 1 ? 'Dia' : 'Noite'; ?></span></p>
        <p>Chuva atual - <span><?php echo $data_current['current']['precipitation'] . ' ' . $data_current['current_units']['precipitation']; ?></span></p>
        <p>Código do clima - <span><?php echo $data_current['current']['weather_code']; ?></span></p>

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

        <p>Velocidade do Vento Atual - <span><?php echo $data_current['current']['wind_speed_10m'] . ' ' . $data_current['current_units']['wind_speed_10m']; ?></span></p>
    </div>

    <p>---------------------------------------</p>

    <div>
        <p>Chace de chuva hoje - <span><?php echo $data_current['daily']['precipitation_probability_max'][0] . ' ' . $data_current['daily_units']['precipitation_probability_max']; ?></span></p>
        <p>Temperatura de hoje - <span><?php echo $data_current['daily']['temperature_2m_max'][0] . ' ' . $data_current['daily_units']['temperature_2m_max'] . ' - ' . $data_current['daily']['temperature_2m_min'][0] . ' ' . $data_current['daily_units']['temperature_2m_min']; ?></span></p>
        
        <?php
            $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
            $time1 = $dateTime1->format('H:i');

            $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
            $time2 = $dateTime2->format('H:i');
        ?>

        <p>Nascer e Por do Sol - <span><?php echo $time1 . ' ' . $time2 ?></span></p>
        <p>Uv - <span><?php echo $data_current['daily']['uv_index_max'][0] ?></span></p>
        <p>Velocidade do Vento Max. - <span><?php echo $data_current['daily']['wind_speed_10m_max'][0] . ' ' . $data_current['daily_units']['wind_speed_10m_max']; ?></span></p>
        <p>Direção do Vento - <span><?php echo $data_current['daily']['wind_direction_10m_dominant'][0] . ' ' . $data_current['daily_units']['wind_direction_10m_dominant']; ?></span></p>
    </div>
    <br>
    <br>
    <br>
    <h1>Informações do clima por hora</h1>
    <hr>
    <br>
    <div style="display: flex; justify-content: space-around; gap: 10px; overflow-y: auto;">

        <?php

            for($a = 0; $a < count($data_current_hr_at); $a++){

                $code_c_h = $data_current_hr_at[$a]['weather_code'];

                $dateTime_c = new DateTime($data_current_hr_at[$a]["time"]); 
                $hora_s = $dateTime_c->format('H');
                
                echo "  <div style='display: flex; flex-direction: column; align-items: center; flex: 1; border: 2px solid; border-radius: 10px; padding: 10px;'>
                            <p>Hora: ". $hora_s ."h</p>   
                            <p>Código do clima: ". $code_c_h ."</p>
                            <p>Descrição: ". $weather_codes_translated[$code_c_h][$current_day_time]["description"] ."</p>
                            <img src='" . $weather_codes_translated[$code_c_h][$current_day_time]["image"] . "' alt=''>
                            <p>Temperatura: ". $data_current_hr_at[$a]['temperature_2m'] . ' ' . $data_current_hr['hourly_units']['temperature_2m'] ."</p>
                            <p>Chance de Chuva: ". $data_current_hr_at[$a]['precipitation_probability'] . ' ' . $data_current_hr['hourly_units']['precipitation_probability'] ."</p>
                        </div>";
            }

        ?>

    </div>
    <br>
    <br>
    <br>
    <h1>Informações do clima por hora - Gráfico</h1>
    <hr>
    <br>
    <div>
        <canvas id="temperatureChart" width="800" height="400"></canvas>
    </div>
    <br>
    <br>
    <br>
    <h1>Informações do clima por dia</h1>
    <hr>
    <br>
    <div style="display: flex; justify-content: space-around; gap: 10px;">

        <?php

            for($a = 0; $a < count($data_current_week["daily"]["time"]); $a++){

                $code_c_h = $data_current_week['daily']['weather_code'][$a];

                $dateTime_c = new DateTime($data_current_week['daily']['time'][$a]); 

                $data_d = $dateTime_c->format('d/m');
                $today = new DateTime();

                $data_desc = $dateTime_c->format('l');

                if($dateTime_c->format('Y-m-d') === $today->format('Y-m-d')){
                    $data_desc = "Hoje";
                } else {
                    $data_desc = $dateTime_c->format('l');
                    $data_desc = diaDaSemanaEmPortugues($data_desc);
                }
                
                echo "  <div style='display: flex; flex-direction: column; align-items: center; flex: 1; border: 2px solid; border-radius: 10px; padding: 10px;'>
                            <p>Dia: ".$data_desc."</p>
                            <p>Data: ".$data_d."</p>
                            <p>Código clima: ".$code_c_h."</p>
                            <p>Descrição: ". $weather_codes_translated[$code_c_h][$current_day_time]["description"] ."</p>
                            <img src='" . $weather_codes_translated[$code_c_h][$current_day_time]["image"] . "' alt=''>
                            <p>Temperatura: ". $data_current_week['daily']['temperature_2m_max'][$a] . ' ' . $data_current_week['daily_units']['temperature_2m_max'] . ' - ' . $data_current_week['daily']['temperature_2m_min'][$a] . ' ' . $data_current_week['daily_units']['temperature_2m_min'] ."</p>
                        </div>";
            }

        ?>

    </div>

    <script>
    // Dados vindos do PHP
    var times = <?php echo $times_json; ?>;
    var temperatures = <?php echo $temperatures_json; ?>;

    // Configuração do gráfico
    var ctx = document.getElementById('temperatureChart').getContext('2d');

    // Criando o gradiente
    var gradient = ctx.createLinearGradient(0, 0, 0, 400); // Direção do gradiente
    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.8)');  // Cor no topo (mais forte)
    gradient.addColorStop(1, 'rgba(153, 102, 255, 0.2)'); // Cor no final (mais clara)

    var splineAreaChart = new Chart(ctx, {
        type: 'line', // Usando o tipo 'line'
        data: {
            labels: times,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatures,
                borderColor: 'rgba(75, 192, 192, 1)', // Cor da linha
                backgroundColor: gradient, // Preenchimento da área
                borderWidth: 2,
                tension: 0.4, // Suaviza as curvas (Spline)
                fill: true, // Preenche a área abaixo da linha
                pointRadius: 3,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        //text: 'Hora'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        //text: 'Temperatura (°C)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>

</html>