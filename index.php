<?php

include "weather_codes.php";
include "cities_code.php";
include "api_weather.php";

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

//variáveis para o gráfico - canvas

$times = array_values($data_current_hr_at_gr['time']);
$temperatures = array_values($data_current_hr_at_gr['temperature_2m']);
$images = array_values($data_current_hr_at_gr['image']);
$precipitation = array_values($data_current_hr_at_gr['precipitation']);

$times_json = json_encode($times);
$temperatures_json = json_encode($temperatures);
$images_json = json_encode($images);
$precipitation_json = json_encode($precipitation);

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./source/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <div id="main">
        <div id="side_1">

            <div id="header_side_1">
                <?php   

                if(isset($estados)){
                    foreach ($estados as $estado) {
                        if ($estado["nome"] == $details_loc["region"]) {
                            $sigla = ' - ' . $estado["sigla"];
                            break;
                        }
                    }

                    if(!isset($sigla)){
                        $sigla = '';
                    }

                } else {
                    $sigla = '';
                }

                ?>

                <h1><?php echo $details_loc["city"] . $sigla ?></h1>
                <h2 id="relogio"></h2>

                <?php
                    $current_day_time = $data_current['current']['is_day'] == 1 ? 'day' : 'night';
                    $code_c = $data_current['current']['weather_code'];

                    if (isset($weather_codes_translated[$code_c][$current_day_time])) {
                        $desc_c = $weather_codes_translated[$code_c][$current_day_time]["description"];
                    } else {
                        $desc_c = "Código ou período do dia não encontrado.";
                    }
                ?>

                <h3><?php echo diaDaSemanaEmPortugues(date('l')) . ' - ' . $desc_c; ?></h3>
            </div>
            
            <div id="icon_side_1">
                <img src="<?php echo $weather_codes_translated[$code_c][$current_day_time]["image"]; ?>" alt="">
            </div>

            <div id="temp_side_1">
                <span id="temp_s_m"><?php echo round($data_current['current']['temperature_2m']) . ' ' . $data_current['current_units']['temperature_2m'];  ?></span>
                <div id="temp_s_mi_ma" class="item_s_temp">
                    <span><?php echo round($data_current['daily']['temperature_2m_max'][0]) ?>°</span>
                    <span><?php echo round($data_current['daily']['temperature_2m_min'][0]) ?>°</span>
                </div>
            </div>

            <div id="footer_side_1">
                <div>
                    <svg width="42" height="40" viewBox="0 0 42 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.787 0C28.1057 0 32.2575 4.84436 32.8613 10.6948L33.046 10.6946C37.7559 10.6946 41.574 14.5029 41.574 19.2006C41.574 23.8983 37.7559 27.7066 33.046 27.7066L31.0562 27.7075L31.0524 27.7177H30.3755L28.0985 31.621C27.6968 32.3097 26.8128 32.5424 26.1242 32.1406C25.4354 31.739 25.2028 30.855 25.6046 30.1662L27.0397 27.7062L22.1214 27.7075L22.1171 27.7177H21.1362L18.8592 31.621C18.4576 32.3097 17.5736 32.5424 16.8849 32.1406C16.1961 31.739 15.9635 30.855 16.3653 30.1662L17.8003 27.7063L13.1866 27.7075L13.1827 27.7177H11.8972L9.62024 31.621C9.21856 32.3097 8.3346 32.5423 7.64593 32.1406C6.95714 31.7388 6.72455 30.8549 7.12634 30.1662L8.56107 27.7065L8.52804 27.7066C3.81811 27.7066 0 23.8983 0 19.2006C0 14.5029 3.81811 10.6946 8.52804 10.6946L8.71271 10.6948C9.32008 4.8059 13.4683 0 20.787 0ZM20.787 2.88169C15.8658 2.88169 11.5766 6.86371 11.5766 12.3642C11.5766 13.2357 10.8188 13.9195 9.95112 13.9195H8.31046C5.31464 13.9195 2.88594 16.3607 2.88594 19.3721C2.88594 22.3837 5.31464 24.8249 8.31046 24.8249H33.2634C36.2594 24.8249 38.688 22.3837 38.688 19.3721C38.688 16.3607 36.2594 13.9195 33.2635 13.9195H31.6229C30.7552 13.9195 29.9974 13.2357 29.9974 12.3642C29.9974 6.79315 25.7081 2.88169 20.787 2.88169Z" fill="var(--font)"/>
                        <path d="M13.4314 33.1115C14.1201 33.5132 14.3527 34.3971 13.9509 35.0858L11.9298 38.5505C11.5281 39.2393 10.6442 39.4719 9.95551 39.0701C9.26684 38.6684 9.03413 37.7845 9.43592 37.0958L11.457 33.6311C11.8587 32.9423 12.7426 32.7097 13.4314 33.1115Z" fill="var(--font)"/>
                        <path d="M23.1902 35.0858C23.592 34.3971 23.3594 33.5132 22.6707 33.1115C21.9819 32.7097 21.0979 32.9423 20.6963 33.6311L18.6752 37.0958C18.2734 37.7845 18.5061 38.6684 19.1948 39.0701C19.8834 39.4719 20.7674 39.2393 21.1691 38.5505L23.1902 35.0858Z" fill="var(--font)"/>
                    </svg>
                    <p>Chuva: <span><?php echo $data_current['daily']['precipitation_probability_max'][0] . ' ' . $data_current['daily_units']['precipitation_probability_max']; ?></span></p>
                </div>
                <?php
                
                $snow = $data_current['daily']['snowfall_sum'][0];

                if($snow > 0){
                    echo "
                    
                        <div>
                            <svg width='39' height='39' viewBox='0 0 39 39' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                <path d='M19.5002 0C20.1249 0 20.6386 0.474938 20.7003 1.08334L20.7065 1.20669L20.7051 8.71077L25.6482 4.36901C26.1487 3.92921 26.911 3.97848 27.3508 4.47904C27.7906 4.97961 27.7414 5.74191 27.2408 6.18171L20.7061 11.9229L20.7032 18.2912H27.0782L32.8216 11.7589C33.2323 11.2918 33.9237 11.218 34.421 11.568L34.5244 11.6494C34.9914 12.0601 35.0652 12.7515 34.7153 13.2487L34.6338 13.3522L30.2913 18.2902L37.7934 18.2911C38.4181 18.2911 38.932 18.7658 38.9938 19.3742L39 19.4975C39 20.1222 38.5253 20.636 37.917 20.6979L37.7936 20.7041L30.2865 20.7041L34.6296 25.6482C35.0401 26.1154 35.0245 26.8106 34.6137 27.2588L34.5196 27.3508C34.0524 27.7613 33.3572 27.7458 32.909 27.3349L32.8169 27.2408L27.0743 20.7051L20.7022 20.7041L20.7012 27.0743L27.2408 32.8204C27.708 33.2308 27.7821 33.9222 27.4323 34.4196L27.3508 34.523C26.9404 34.9902 26.2489 35.0643 25.7516 34.7145L25.6482 34.633L20.7012 30.2865L20.7 37.7937C20.6998 38.4184 20.225 38.932 19.6166 38.9938L19.4933 39C18.8686 39 18.3549 38.5251 18.2932 37.9167L18.287 37.7933L18.2873 30.2923L13.3522 34.6328C12.8517 35.0727 12.0894 35.0237 11.6494 34.5233C11.2095 34.0229 11.2585 33.2606 11.7589 32.8206L18.2883 27.0792L18.2892 20.7051L12.3602 20.7062C12.3363 20.7396 12.3106 20.7722 12.2829 20.8037L6.18149 27.7482C5.7417 28.2487 4.97939 28.298 4.47883 27.8582C3.97826 27.4184 3.929 26.6561 4.36879 26.1555L9.15669 20.7051L1.20656 20.7065C0.581882 20.7065 0.0680221 20.2318 0.00622974 19.6234L0 19.5001C0 18.8754 0.47468 18.3616 1.08305 18.2998L1.2064 18.2935L8.26003 18.2931L4.36834 13.8587C3.92883 13.3579 3.97853 12.5956 4.47934 12.1561C4.98015 11.7166 5.74244 11.7663 6.18194 12.2671L11.4702 18.2921H18.2902L18.2912 11.92L11.7589 6.17699C11.2918 5.76635 11.218 5.0749 11.568 4.57768L11.6494 4.47428C12.0601 4.00724 12.7515 3.93342 13.2487 4.28337L13.3522 4.36486L18.2921 8.70788L18.2935 1.20626C18.2936 0.623234 18.7073 0.136961 19.2571 0.0245024L19.3769 0.00622652L19.5002 0Z' fill='var(--font)'/>
                            </svg>
                            <p>Neve: <span>".$data_current['daily']['snowfall_sum'][0] . ' ' . $data_current['daily_units']['snowfall_sum'] . "</span></p>
                        </div>

                    ";
                }

                ?>

                <div>
                    <svg width="46" height="35" viewBox="0 0 46 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M39.1215 12.5962C42.9201 12.5962 46 15.5268 46 19.1426C46 22.7568 42.8893 25.7045 39.0889 25.7045C39.0401 25.7045 38.9918 25.7022 38.9442 25.6977L38.8014 25.7045L35.3134 25.7043C35.9574 26.644 36.3315 27.7644 36.3315 28.968C36.3315 32.3218 33.6885 35 30.1958 35C26.725 35 24.6512 33.0875 24.0882 30.4126C23.9322 29.6712 24.4368 28.9498 25.2152 28.8012C25.9936 28.6526 26.7511 29.1331 26.9071 29.8745C27.2181 31.3522 28.2045 32.2618 30.1958 32.2618C32.0595 32.2618 33.4565 30.8462 33.4565 28.968C33.4565 27.2641 32.0673 25.8587 30.2858 25.7034L1.43748 25.7045C0.64358 25.7045 0 25.0916 0 24.3354C0 23.6266 0.565646 23.0435 1.2905 22.9734L1.43748 22.9664L29.7569 22.9642C29.8224 22.9555 29.8893 22.951 29.9573 22.951C30.1054 22.951 30.2523 22.9558 30.3979 22.9651L38.8014 22.9664L38.9441 22.9731L39.0889 22.9664C41.2309 22.9664 43.0033 21.3545 43.119 19.3512L43.125 19.1426C43.125 17.0396 41.3329 15.3343 39.1215 15.3343C37.0013 15.3343 35.252 16.9071 35.1253 18.9088C35.0775 19.6635 34.3963 20.2385 33.6038 20.193C32.8114 20.1474 32.2077 19.4987 32.2555 18.7439C32.4737 15.2982 35.4791 12.5962 39.1215 12.5962ZM21.8392 17.5057H1.43748C0.64358 17.5057 0 16.8927 0 16.1366C0 15.4277 0.565646 14.8447 1.2905 14.7746L1.43748 14.7675H21.8392C25.327 14.7675 28.1544 12.0747 28.1544 8.75284C28.1544 5.43102 25.327 2.73815 21.8392 2.73815C18.3514 2.73815 15.524 5.43102 15.524 8.75284C15.524 9.50896 14.8805 10.1219 14.0866 10.1219C13.2927 10.1219 12.6491 9.50896 12.6491 8.75284C12.6491 3.91878 16.7636 0 21.8392 0C26.9148 0 31.0293 3.91878 31.0293 8.75284C31.0293 13.5869 26.9148 17.5057 21.8392 17.5057Z" fill="var(--font)"/>
                    </svg>
                    <p>Vento: <span><?php echo $data_current['current']['wind_speed_10m'] . ' ' . $data_current['current_units']['wind_speed_10m']; ?></span></p>
                </div>
            </div>
        </div>

        <div id="side_2">
            <h1>Hoje</h1>
            <div id="sec_1" class="shadow">
                <div style="height: 300px; width: 100%">
                    <canvas id="temperatureChart" style="height: 100%; width: 100%"></canvas>
                </div>
            </div>
            <div id="sec_2">
                <h1>Esta semana</h1>
                
                <div id="itens_sec_2">
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

                            echo "  <div class='item_s shadow'>
                                        <span>".$data_desc."</span>
                                        <span>".$data_d."</span>
                                        <img src='" . $weather_codes_translated[$code_c_h][$current_day_time]["image"] . "' alt=''>
                                        <div class='item_s_rain'>
                                            <svg width='42' height='40' viewBox='0 0 42 40' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M20.787 0C28.1057 0 32.2575 4.84436 32.8613 10.6948L33.046 10.6946C37.7559 10.6946 41.574 14.5029 41.574 19.2006C41.574 23.8983 37.7559 27.7066 33.046 27.7066L31.0562 27.7075L31.0524 27.7177H30.3755L28.0985 31.621C27.6968 32.3097 26.8128 32.5424 26.1242 32.1406C25.4354 31.739 25.2028 30.855 25.6046 30.1662L27.0397 27.7062L22.1214 27.7075L22.1171 27.7177H21.1362L18.8592 31.621C18.4576 32.3097 17.5736 32.5424 16.8849 32.1406C16.1961 31.739 15.9635 30.855 16.3653 30.1662L17.8003 27.7063L13.1866 27.7075L13.1827 27.7177H11.8972L9.62024 31.621C9.21856 32.3097 8.3346 32.5423 7.64593 32.1406C6.95714 31.7388 6.72455 30.8549 7.12634 30.1662L8.56107 27.7065L8.52804 27.7066C3.81811 27.7066 0 23.8983 0 19.2006C0 14.5029 3.81811 10.6946 8.52804 10.6946L8.71271 10.6948C9.32008 4.8059 13.4683 0 20.787 0ZM20.787 2.88169C15.8658 2.88169 11.5766 6.86371 11.5766 12.3642C11.5766 13.2357 10.8188 13.9195 9.95112 13.9195H8.31046C5.31464 13.9195 2.88594 16.3607 2.88594 19.3721C2.88594 22.3837 5.31464 24.8249 8.31046 24.8249H33.2634C36.2594 24.8249 38.688 22.3837 38.688 19.3721C38.688 16.3607 36.2594 13.9195 33.2635 13.9195H31.6229C30.7552 13.9195 29.9974 13.2357 29.9974 12.3642C29.9974 6.79315 25.7081 2.88169 20.787 2.88169Z' fill='var(--font)'/>
                                                <path d='M13.4314 33.1115C14.1201 33.5132 14.3527 34.3971 13.9509 35.0858L11.9298 38.5505C11.5281 39.2393 10.6442 39.4719 9.95551 39.0701C9.26684 38.6684 9.03413 37.7845 9.43592 37.0958L11.457 33.6311C11.8587 32.9423 12.7426 32.7097 13.4314 33.1115Z' fill='var(--font)'/>
                                                <path d='M23.1902 35.0858C23.592 34.3971 23.3594 33.5132 22.6707 33.1115C21.9819 32.7097 21.0979 32.9423 20.6963 33.6311L18.6752 37.0958C18.2734 37.7845 18.5061 38.6684 19.1948 39.0701C19.8834 39.4719 20.7674 39.2393 21.1691 38.5505L23.1902 35.0858Z' fill='var(--font)'/>
                                            </svg>
                                            <span>". $data_current_week['daily']['precipitation_probability_max'][$a] . ' ' . $data_current_week['daily_units']['precipitation_probability_max'] . "</span>
                                        </div>
                                        <div class='item_s_temp'>
                                            <span>" . round($data_current_week['daily']['temperature_2m_max'][$a]) . "°</span>
                                            <span>" . round($data_current_week['daily']['temperature_2m_min'][$a]) . "°</span>
                                        </div>
                                    </div>";
                        }

                    ?>
                </div>
            </div>
            <div id="sec_3">
                <h1>Mais informações</h1>

                <?php
                    $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
                    $time1 = $dateTime1->format('H:i');

                    $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
                    $time2 = $dateTime2->format('H:i');
                ?>

                <div id="items_s_3">
                    <div class="item_sec_3 shadow">
                        <h3>Pôr do Sol</h3>
                        <span><?php echo $time1 . ' ' . $time2 ?></span>
                    </div>
                    <div class="item_sec_3 shadow">
                        <h3>Vento</h3>
                        <span><?php echo $data_current['daily']['uv_index_max'][0] ?></span>
                    </div>
                    <div class="item_sec_3 shadow">
                        <h3>Precipitação</h3>
                        <span><?php echo $data_current['daily']['wind_speed_10m_max'][0] . ' ' . $data_current['daily_units']['wind_speed_10m_max']; ?></span>
                        <span><?php echo $data_current['daily']['wind_direction_10m_dominant'][0] . ' ' . $data_current['daily_units']['wind_direction_10m_dominant']; ?></span>
                    </div>
                    <div class="item_sec_3 shadow">
                        <h3>UV</h3>
                        <span><?php echo $data_current_week['daily']['precipitation_sum'][0] . ' ' . $data_current_week['daily_units']['precipitation_sum']; ?></span>
                    </div>
                </div>
            </div>
        </div>
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
    <script>
        function atualizarRelogio() {
            const agora = new Date();

            // Formatação da data
            const dia = String(agora.getDate()).padStart(2, '0'); // Dia com dois dígitos
            const mes = String(agora.getMonth() + 1).padStart(2, '0'); // Mês com dois dígitos
            const horas = String(agora.getHours()).padStart(2, '0'); // Horas com dois dígitos
            const minutos = String(agora.getMinutes()).padStart(2, '0'); // Minutos com dois dígitos

            const formatoRelogio = `${dia}/${mes} ${horas}:${minutos}`;

            document.getElementById('relogio').innerText = formatoRelogio;
        }

        // Atualiza o relógio a cada minuto
        setInterval(atualizarRelogio, 60000);

        // Atualiza o relógio imediatamente
        atualizarRelogio();

    </script>
</body>
</html>