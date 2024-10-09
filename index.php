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
include "cities_code.php";


$times = array_values($data_current_hr_at_gr['time']);
$temperatures = array_values($data_current_hr_at_gr['temperature_2m']);
$images = array_values($data_current_hr_at_gr['image']);
$precipitation = array_values($data_current_hr_at_gr['precipitation']);

$times_json = json_encode($times);
$temperatures_json = json_encode($temperatures);
$images_json = json_encode($images);
$precipitation_json = json_encode($precipitation);

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

        <?php   
        
        foreach ($estados as $estado) {
            if ($estado["nome"] == $details_loc["region"]) {
                $sigla = $estado["sigla"];
                break;
            }
        }
        
        ?>

        <p>Região - <span><?php echo $details_loc["city"] . " - " . $sigla ?></span></p>
        <p>Data e hora - <span><?php echo date('d/m H:i'); ?></span></p>
        <p>Dia da semana - <span><?php echo diaDaSemanaEmPortugues(date('l')); ?></span></p>
        <p>Temperatura atual - <span><?php echo $data_current['current']['temperature_2m'] . ' ' . $data_current['current_units']['temperature_2m'];  ?></span></p>
        <p>Está de dia? - <span><?php echo $data_current['current']['is_day'] == 1 ? 'Dia' : 'Noite'; ?></span></p>
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

        <p>Precipitação hoje - <span><?php echo $data_current_week['daily']['precipitation_sum'][0] . ' ' . $data_current_week['daily_units']['precipitation_sum']; ?></p>

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
    <h1>Informações do clima por hora - Gráfico</h1>
    <hr>
    <br>
    <div style="height: 400px; width: 100%">
        <canvas id="temperatureChart" style="height: 100%; width: 100%"></canvas>
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
                            <p>Temperatura: ". round($data_current_week['daily']['temperature_2m_max'][$a]) . ' ' . $data_current_week['daily_units']['temperature_2m_max'] . ' - ' . round($data_current_week['daily']['temperature_2m_min'][$a]) . ' ' . $data_current_week['daily_units']['temperature_2m_min'] ."</p>
                            <p>Chace de chuva: ". $data_current_week['daily']['precipitation_probability_max'][$a] . ' ' . $data_current_week['daily_units']['precipitation_probability_max'] . "</p>
                        </div>";
            }

        ?>

    </div>

    <script>

    var times = <?php echo $times_json; ?>;
    var temperatures = <?php echo $temperatures_json; ?>;
    var images = <?php echo $images_json; ?>;
    var precipitation = <?php echo $precipitation_json; ?>;

    var max_temp = <?php echo round($data_current_week['daily']['temperature_2m_max'][0]) ?>;
    var min_temp = <?php echo round($data_current_week['daily']['temperature_2m_min'][0]) ?>;

    var ctx = document.getElementById('temperatureChart').getContext('2d');

    // Criando o gradiente
    var gradient = ctx.createLinearGradient(0, 0, 0, 400); // Direção do gradiente
    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.8)');  // Cor no topo (mais forte)
    gradient.addColorStop(1, 'rgba(153, 102, 255, 0.2)'); // Cor no final (mais clara)


    var customLabelsPlugin = {
    id: 'customLabelsPlugin',
    afterDatasetsDraw: function(chart) {
        var ctx = chart.ctx;
        
        ctx.fillStyle = 'black';

        chart.data.labels.forEach(function(label, index) {
            var x = chart.scales.x.getPixelForValue(index);
            var y = chart.scales.y.bottom + 15;

            var img = new Image();
            img.src = images[index];
            img.onload = function() {
                ctx.drawImage(img, x - 30, y - 20, 60, 60);

                ctx.font = '16px Arial';
                ctx.fillText(precipitation[index] + '%', x, y + 40);

                ctx.font = '24px Arial';
                ctx.fillText(times[index] + 'h', x, y + 80);
            };
        });
    }
};


    var topValuesPlugin = {
        id: 'topValuesPlugin',
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
            chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                meta.data.forEach(function(point, index) {
                    var value = dataset.data[index];
                    var x = point.x;
                    var y = point.y;
                    ctx.fillStyle = 'black';
                    ctx.font = '24px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText(Math.round(value)+"°", x, 40);
                });
            });
        }
    };

    var splineAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: times,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatures,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: gradient,
                borderWidth: 5,
                tension: 0.4,
                fill: true,
                pointRadius: 0
                //pointRadius: 3,
                //pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                //pointBorderWidth: 1
            }]
        },
        options: {
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 60,
                    bottom: 0
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                    },
                    ticks: {
                        padding: 30,
                        font: {
                            size: 24
                        },
                        callback: function(value, index, values) {
                            return '';
                        }
                    }
                },
                y: {
                    display: true,
                    ticks: {
                        display: false
                    },
                    title: {
                        display: true,
                    },
                    beginAtZero: false,
                    grid: {
                        display: false
                    },
                    min: min_temp,
                    max: max_temp
                }
            }
        },
        plugins: [topValuesPlugin, customLabelsPlugin]
    });
</script>
</body>

</html>