<?php

header("Content-type: text/html; charset=utf-8");
ini_set("display_errors", 1);
error_reporting(E_ALL|E_STRICT);

include "./components/weather_codes.php"; // arquivo com todos os códigos de clima, suas descrições e icones
include "./components/cities_code.php"; // arquivo com de/para dos estados brasileiros e suas siglas
include "api_weather.php"; // arquivo principal com as requisições na API de clima

//------------------------------------------------------------------
//------------------------------------------------------------------
//------------------------------------------------------------------

// função de/para dia da semana em inglês para português
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

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <!-- PWA -->
    <link rel="manifest" href="manifest.webmanifest">

    <meta content='yes' name='apple-mobile-web-app-capable' />
    <meta content='yes' name='mobile-web-app-capable' />
    <meta name="apple-mobile-web-app-status-bar" content="#08BBCF">
    <meta name="theme-color" content="black">
    <link rel="apple-touch-icon" href="image/icon-192x192.png">
    <!-- PWA -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinWeather</title>
    <link rel="icon" href="./image/icone.ico" type="image/x-icon">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,100,0,0&icon_names=humidity_low" />

    <link rel="stylesheet" href="./source/main.css">
    <script src="./source/load.js" type="text/javascript"></script>
</head>

<body>
    <!-- verifica se tem qualquer mensagem de erro antes de carregar o conteúdo -->
    <?php if ($error_message == ''): ?>
    <div id="main">
        <div id="side_1">
            <div id="search_b">
                <div id="btn_ref">
                    <!-- botão ao lado da barra de pesquisa para recarregar a página com a localização atual -->
                    <button id="refresh">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
	                        <path fill="none" stroke="var(--font)" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M256 96V56M256 456v-40"/>
                            <path d="M256 112a144 144 0 10144 144 144 144 0 00-144-144z" fill="none" stroke="var(--font)" stroke-miterlimit="10" stroke-width="32"/>
                            <path fill="none" stroke="var(--font)" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M416 256h40M56 256h40"/>
                        </svg>
                    </button>

                    <!-- barra de pesquisa-->
                    <div id="search_bar">
                        <input type="text" id="search" placeholder="Digite uma cidade...">
                        <div class="suggestions" id="suggestions" style="display: none;"></div>
                    </div>
                </div>

                <!-- botão ativa e desativa modo escuro -->
                <div id="btn_l_n">
                    <button id="toggle-button">

                        <svg id="light_i" width="100%" height="100%" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 2V6.61538M22 37.3846V42M36.1423 7.85769L32.8788 11.1212M11.1212 32.8788L7.85769 36.1423M42 22H37.3846M6.61538 22H2M36.1423 36.1423L32.8788 32.8788M11.1212 11.1212L7.85769 7.85769" stroke="var(--font)" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round"/>
                            <path d="M22 29.6922C26.2483 29.6922 29.6923 26.2483 29.6923 21.9999C29.6923 17.7516 26.2483 14.3076 22 14.3076C17.7516 14.3076 14.3077 17.7516 14.3077 21.9999C14.3077 26.2483 17.7516 29.6922 22 29.6922Z" stroke="var(--font)" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round"/>
                        </svg>

                        <svg id="night_i" width="100%" height="100%" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.7692 10.4615C12.7692 7.51731 13.2029 4.5375 14.3077 2C6.95865 5.19904 2 12.7038 2 21.2308C2 32.701 11.299 42 22.7692 42C31.2962 42 38.801 37.0413 42 29.6923C39.4625 30.7971 36.4827 31.2308 33.5385 31.2308C22.0683 31.2308 12.7692 21.9317 12.7692 10.4615Z" stroke="var(--font)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </button>
                </div>
            </div>

            <div id="side_cont">
                <div id="header_side_1">
                    <?php   

                    //busca a sigla do estado se for brasileiro, caso contrático coloca a sigla do pais

                    if(isset($estados)){
                        foreach ($estados as $estado) {
                            if ($estado["nome"] == $details_loc["region"]) {
                                $sigla = ' - ' . $estado["sigla"];
                                break;
                            }
                        }

                        if(!isset($sigla)){
                            $sigla = ' - ' . $details_loc["country"];
                        }

                    } else {
                        $sigla = ' - ' . $details_loc["country"];
                    }


                    ?>

                    <h1><?php echo $details_loc["city"] . $sigla ?></h1>
                    <h2 id="relogio"></h2>

                    <?php

                        //busca a descrição do clima

                        $code_c = $data_current['current']['weather_code'];

                        if (isset($weather_codes_translated[$code_c][$current_day_time])) {
                            $desc_c = $weather_codes_translated[$code_c][$current_day_time]["description"];
                        } else {
                            $code_c = 1;
                            $desc_c = "Código ou período do dia não encontrado.";
                        }
                    ?>

                    <h3><?php echo diaDaSemanaEmPortugues(date('l')) . ' - ' . $desc_c; ?></h3>
                </div>
            
                <div id="temp_prin">
                    <!-- icone de clima principal -->
                    <div id="icon_side_1">
                        <?php echo $weather_codes_translated[$code_c][$current_day_time]["image"]; ?>
                    </div>

                    <!-- temperatura mínima e máxima-->
                    <div id="temp_side_1">
                        <span id="temp_s_m"><?php echo round($data_current['current']['temperature_2m']) . ' ' . $data_current['current_units']['temperature_2m'];  ?></span>
                        <div id="temp_s_mi_ma" class="item_s_temp">
                            <span><?php echo round($data_current['daily']['temperature_2m_max'][0]) ?>°</span>
                            <span><?php echo round($data_current['daily']['temperature_2m_min'][0]) ?>°</span>
                        </div>
                    </div>
                </div>

                <!-- dados de chuva, vento e neve caso exista algo, se não mostra apenas chuva e vento-->

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
                        <p>Vento: <span><?php echo $data_current['daily']['wind_speed_10m_max'][0] . ' ' . $data_current['daily_units']['wind_speed_10m_max']; ?></span></p>
                    </div>
                </div>

            </div>
        </div>

        <div id="side_2">
            
            <!-- gráfico -->
            <h1>Hoje</h1>
            <div id="sec_1" class="shadow">
                <div style="height: 300px; width: 100%">
                    <canvas id="temperatureChart" style="height: 100%; width: 100%"></canvas>
                </div>
            </div>

            <!-- cards com os próximos 7 dias -->
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
                                        ". $weather_codes_translated[$code_c_h]['day']["image"] . "
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
                <div id="items_s_3">

                    <!-- nascer e por do sol, calculando o angulo de rotação para o desenho gráfico e mudando de cor caso seja dia ou noite -->
                    <div class="item_sec_3 shadow">

                        <?php

                        $comprimento_linha = 197;
                        $grau_rot_max = 180;
                        $color_sun = 'var(--yellow)';

                        //------------------------------------------------------------

                        $dateTime1 = new DateTime($data_current['daily']['sunrise'][0]);
                        $time1 = $dateTime1->format('H:i');

                        $dateTime2 = new DateTime($data_current['daily']['sunset'][0]);
                        $time2 = $dateTime2->format('H:i');

                        $hora_atual = date('H:i');

                        //------------------------------------------------------------
                        
                        $inicio = strtotime($time1);
                        $fim = strtotime($time2);
                        $atual = strtotime($hora_atual);

                        $duracao_total = $fim - $inicio;

                        $tempo_passado = $atual - $inicio;

                        if ($tempo_passado < 0) {
                            $tempo_passado = 0;
                        }
                    
                        $porcentagem = round(($tempo_passado / $duracao_total) * 100);
                        $porcentagem = min($porcentagem, 100);

                        $grau_rot = round(($grau_rot_max * $porcentagem) / 100);
                        $progresso = round(((100 - $porcentagem)/100) * $comprimento_linha);

                        if($progresso <= 0 ){
                            $progresso = 0;
                            $color_sun = 'var(--blue)';
                        }

                        ?>

                        <h3>Pôr do Sol</h3>
                        <div id="sun_cont">
                            <div id="sun_bar">
                                <svg width="100%" height="100%" viewBox="0 0 146 146" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="arco_ciruclo">
                                        <g id="arco">
                                            <path id="Ellipse 32" d="M10 71C10 62.858 11.6295 54.7958 14.7956 47.2736C17.9616 39.7514 22.6022 32.9166 28.4523 27.1594C34.3024 21.4021 41.2474 16.8353 48.8909 13.7195C56.5345 10.6037 64.7267 9 73 9C81.2733 9 89.4655 10.6037 97.1091 13.7195C104.753 16.8353 111.698 21.4021 117.548 27.1594C123.398 32.9166 128.038 39.7514 131.204 47.2736C134.37 54.7958 136 62.858 136 71" stroke="var(--gray)" stroke-width="4" stroke-linecap="round"/>
                                            <path style="stroke-dasharray: <?php echo $comprimento_linha ?>; stroke-dashoffset: <?php echo $progresso ?>;" id="Ellipse 33" d="M10 71C10 62.858 11.6295 54.7958 14.7956 47.2736C17.9616 39.7514 22.6022 32.9166 28.4523 27.1594C34.3024 21.4021 41.2474 16.8353 48.8909 13.7195C56.5345 10.6037 64.7267 9 73 9C81.2733 9 89.4655 10.6037 97.1091 13.7195C104.753 16.8353 111.698 21.4021 117.548 27.1594C123.398 32.9166 128.038 39.7514 131.204 47.2736C134.37 54.7958 136 62.858 136 71" stroke="<?php echo $color_sun ?>" stroke-width="4" stroke-linecap="round"/>
                                        </g>
                                        <circle style="rotate: <?php echo $grau_rot ?>deg; transform-origin: 50% 50%;" id="sol" cx="9" cy="73" r="9" stroke-width="3" stroke="var(--white2)" fill="<?php echo $color_sun ?>"/>
                                    </g>
                                </svg>
                            </div>
                            <div id="sun_info">
                                <span><?php echo $time1 ?></span>
                                <span><?php echo $time2 ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="item_sec_3 shadow">

                        <!-- direção e velocidade atual do vento -->
                        <h3>Vento</h3>
                        <div id="buss-cont">
                            <div id="bussola_v">
                                <svg width="102" height="102" viewBox="0 0 102 102" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="bussola">
                                        <path id="Subtract" fill-rule="evenodd" clip-rule="evenodd" d="M51 102C79.1665 102 102 79.1665 102 51C102 22.8335 79.1665 0 51 0C22.8335 0 0 22.8335 0 51C0 79.1665 22.8335 102 51 102ZM50.9998 98C76.9571 98 97.9998 76.9574 97.9998 51C97.9998 25.0426 76.9571 4 50.9998 4C25.0424 4 3.99976 25.0426 3.99976 51C3.99976 76.9574 25.0424 98 50.9998 98Z" fill="var(--font)"/>
                                        <g id="Vector">
                                            <path d="M48.0777 21.8375C47.6989 21.8375 47.5095 21.6355 47.5095 21.2315V13.606C47.5095 13.202 47.6989 13 48.0777 13H48.2039C48.5069 13 48.7468 13.1305 48.9235 13.3914L53.1908 19.8049V13.606C53.1908 13.202 53.3886 13 53.7842 13C54.1629 13 54.3523 13.202 54.3523 13.606V21.2315C54.3523 21.6355 54.1671 21.8375 53.7968 21.8375H53.7084C53.3802 21.8375 53.1403 21.707 52.9888 21.4461L48.6584 14.9064V21.2315C48.6584 21.6355 48.4648 21.8375 48.0777 21.8375Z" fill="var(--font)"/>
                                            <path d="M82.6429 54.7809C82.2473 54.7809 82.0495 54.5789 82.0495 54.1749V46.5494C82.0495 46.1454 82.2473 45.9434 82.6429 45.9434H87.1626C87.5666 45.9434 87.7686 46.1243 87.7686 46.4862C87.7686 46.8482 87.5666 47.0291 87.1626 47.0291H83.1984V49.7182H86.7713C87.1668 49.7182 87.3646 49.9034 87.3646 50.2737C87.3646 50.6357 87.1668 50.8166 86.7713 50.8166H83.1984V53.6951H87.3141C87.7181 53.6951 87.9201 53.8761 87.9201 54.238C87.9201 54.5999 87.7181 54.7809 87.3141 54.7809H82.6429Z" fill="var(--font)"/>
                                            <path d="M17.4465 54.7809C17.093 54.7809 16.8784 54.6041 16.8027 54.2506L15.0225 46.5999C14.9805 46.3894 14.9973 46.2295 15.073 46.1201C15.1572 46.0023 15.2877 45.9434 15.4644 45.9434C15.8684 45.9434 16.0999 46.1201 16.1588 46.4736L17.5349 52.6851L19.2267 46.461C19.3108 46.1159 19.5213 45.9434 19.8579 45.9434C20.1778 45.9434 20.3882 46.1159 20.4892 46.461L22.1809 52.6851L23.5444 46.4736C23.6118 46.1201 23.8264 45.9434 24.1883 45.9434C24.3987 45.9434 24.546 46.0023 24.6302 46.1201C24.7228 46.2379 24.748 46.4021 24.7059 46.6125L22.9132 54.2506C22.8374 54.6041 22.6228 54.7809 22.2693 54.7809C21.9074 54.7809 21.6843 54.6083 21.6002 54.2632L19.8579 47.8624L18.103 54.2632C18.0189 54.6083 17.8 54.7809 17.4465 54.7809Z" fill="var(--font)"/>
                                            <path d="M51.1566 88.0856C50.5422 88.0856 49.9698 87.9846 49.4396 87.7826C48.9177 87.5806 48.4675 87.2524 48.0887 86.7979C47.9709 86.6379 47.9246 86.4907 47.9498 86.356C47.9835 86.2129 48.0803 86.0951 48.2402 86.0025C48.3917 85.9099 48.5306 85.8804 48.6568 85.9141C48.7915 85.9394 48.9262 86.0235 49.0608 86.1666C49.3133 86.4275 49.6163 86.6295 49.9698 86.7726C50.3233 86.9073 50.7315 86.9746 51.1945 86.9746C51.7836 86.9746 52.2423 86.8568 52.5706 86.6211C52.8988 86.3854 53.063 86.0404 53.063 85.5859C53.063 85.3165 53.004 85.0851 52.8862 84.8915C52.7684 84.6895 52.5327 84.5085 52.1792 84.3486C51.8257 84.1887 51.2912 84.0372 50.5758 83.8941C48.9514 83.5406 48.1392 82.7116 48.1392 81.407C48.1392 80.902 48.2612 80.4601 48.5053 80.0814C48.7494 79.7026 49.0903 79.408 49.528 79.1976C49.9656 78.9788 50.4748 78.8694 51.0556 78.8694C51.6279 78.8694 52.1413 78.9704 52.5958 79.1724C53.0503 79.3659 53.4501 79.6226 53.7952 79.9425C53.9215 80.0771 53.9762 80.216 53.9593 80.3591C53.9425 80.5022 53.8625 80.6327 53.7195 80.7505C53.568 80.8683 53.4249 80.9188 53.2902 80.902C53.164 80.8852 53.0293 80.8094 52.8862 80.6747C52.6337 80.4643 52.3602 80.3002 52.0656 80.1824C51.771 80.0561 51.4301 79.993 51.043 79.993C50.5548 79.993 50.1466 80.1108 49.8183 80.3465C49.4985 80.5737 49.3386 80.9104 49.3386 81.3565C49.3386 81.5921 49.3891 81.8068 49.4901 82.0004C49.5995 82.1855 49.7973 82.3497 50.0835 82.4927C50.3696 82.6358 50.7862 82.7663 51.3333 82.8841C52.377 83.1029 53.1261 83.4312 53.5806 83.8689C54.0435 84.3065 54.275 84.8747 54.275 85.5732C54.275 86.1119 54.1403 86.5706 53.871 86.9494C53.61 87.3197 53.2439 87.6017 52.7726 87.7952C52.3097 87.9888 51.771 88.0856 51.1566 88.0856Z" fill="var(--font)"/>
                                        </g>
                                        <path id="Vector 22" d="M24 77.2617L26.959 74.3027M77.2617 24L74.3027 26.959M77.2617 77.2617L74.3027 74.3027M24 24L26.959 26.959" stroke="var(--font)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                </svg>
                                <svg width="13" height="49" viewBox="0 0 13 49" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute;transform: rotate(<?php echo $data_current['daily']['wind_direction_10m_dominant'][0] ?>deg);">
                                    <g id="ponteiro">
                                        <path id="Vector 23" d="M11.3166 24.2134H1.10968C0.654278 24.2134 0.320128 23.7854 0.430578 23.3436L5.53404 2.92978C5.71078 2.22281 6.71549 2.22281 6.89223 2.92978L11.9957 23.3436C12.1061 23.7854 11.772 24.2134 11.3166 24.2134Z" fill="var(--yellow)"/>
                                        <path id="Vector 24" d="M1.10968 24.2134L11.3166 24.2134C11.772 24.2134 12.1061 24.6413 11.9957 25.0832L6.89223 45.497C6.71549 46.2039 5.71078 46.2039 5.53404 45.497L0.430578 25.0832C0.320127 24.6414 0.654279 24.2134 1.10968 24.2134Z" fill="var(--blue)"/>
                                        <circle id="Ellipse 30" cx="6.21313" cy="24.2134" r="6" fill="var(--font)"/>
                                    </g>
                                </svg>
                            </div>
                            <div id="vento_info">
                                <svg width="46" height="35" viewBox="0 0 46 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M39.1215 12.5962C42.9201 12.5962 46 15.5268 46 19.1426C46 22.7568 42.8893 25.7045 39.0889 25.7045C39.0401 25.7045 38.9918 25.7022 38.9442 25.6977L38.8014 25.7045L35.3134 25.7043C35.9574 26.644 36.3315 27.7644 36.3315 28.968C36.3315 32.3218 33.6885 35 30.1958 35C26.725 35 24.6512 33.0875 24.0882 30.4126C23.9322 29.6712 24.4368 28.9498 25.2152 28.8012C25.9936 28.6526 26.7511 29.1331 26.9071 29.8745C27.2181 31.3522 28.2045 32.2618 30.1958 32.2618C32.0595 32.2618 33.4565 30.8462 33.4565 28.968C33.4565 27.2641 32.0673 25.8587 30.2858 25.7034L1.43748 25.7045C0.64358 25.7045 0 25.0916 0 24.3354C0 23.6266 0.565646 23.0435 1.2905 22.9734L1.43748 22.9664L29.7569 22.9642C29.8224 22.9555 29.8893 22.951 29.9573 22.951C30.1054 22.951 30.2523 22.9558 30.3979 22.9651L38.8014 22.9664L38.9441 22.9731L39.0889 22.9664C41.2309 22.9664 43.0033 21.3545 43.119 19.3512L43.125 19.1426C43.125 17.0396 41.3329 15.3343 39.1215 15.3343C37.0013 15.3343 35.252 16.9071 35.1253 18.9088C35.0775 19.6635 34.3963 20.2385 33.6038 20.193C32.8114 20.1474 32.2077 19.4987 32.2555 18.7439C32.4737 15.2982 35.4791 12.5962 39.1215 12.5962ZM21.8392 17.5057H1.43748C0.64358 17.5057 0 16.8927 0 16.1366C0 15.4277 0.565646 14.8447 1.2905 14.7746L1.43748 14.7675H21.8392C25.327 14.7675 28.1544 12.0747 28.1544 8.75284C28.1544 5.43102 25.327 2.73815 21.8392 2.73815C18.3514 2.73815 15.524 5.43102 15.524 8.75284C15.524 9.50896 14.8805 10.1219 14.0866 10.1219C13.2927 10.1219 12.6491 9.50896 12.6491 8.75284C12.6491 3.91878 16.7636 0 21.8392 0C26.9148 0 31.0293 3.91878 31.0293 8.75284C31.0293 13.5869 26.9148 17.5057 21.8392 17.5057Z" fill="var(--font)"/>
                                </svg>
                                <p><?php echo $data_current['current']['wind_speed_10m'] . ' ' . $data_current['current_units']['wind_speed_10m']; ?></p>
                            </div>
                        </div>
                    </div>

                    <?php
                    
                    //ajusta a animação do nível da água de acordo com a quantidade de precipitação 

                    $precip_num = $data_current_week['daily']['precipitation_sum'][0];

                    if($precip_num == 0){
                        $animationName = "waveAction00";
                    } else if($precip_num > 0 and $precip_num <= 5){
                        $animationName = "waveAction01";
                    } else if($precip_num > 5 and $precip_num < 20){
                        $animationName = "waveAction02";
                    } else if($precip_num > 20) {
                        $animationName = "waveAction03";
                    }

                    ?>

                    <div class="item_sec_3 shadow">

                        <!-- nível e quantidade de preciptação atual -->
                        <h3>Precipitação</h3>
                        <div id="prec_cont">
                            <div id="prec_info">
                                <svg width="100%" height="100%" viewBox="0 0 143 123" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="water" clip-path="url(#clip-path)">
                                        <path style="animation-name: <?php echo $animationName?>" fill-rule="evenodd" clip-rule="evenodd" d="M1 123V48.0493C3.00361 47.0816 4.99534 45.7199 6.93665 43.8835C22.8009 28.877 33.6274 35.7002 42.2936 43.8018C49.6768 49.2366 62.9036 54.3203 73.9366 43.8835C90.1392 28.5569 101.087 36.0012 109.845 44.3215C109.957 44.8108 110.077 45.2917 110.197 45.7689L110.199 45.7773L110.2 45.7803C110.549 47.1799 110.891 48.5477 111 50V123H1Z" fill="var(--blue)"/>
                                        <path style="animation-name: <?php echo $animationName?>" d="M38.9366 43.8835C25.9746 56.145 10.7645 47.241 4.77979 41.2563C3.8501 43.7354 3.24939 46.748 3.06238 50.0356L3 50V123H143V50C142.891 48.5466 142.549 47.178 142.199 45.7773C142.079 45.2974 141.958 44.8137 141.845 44.3215C133.087 36.0012 122.139 28.5569 105.937 43.8835C94.9036 54.3203 81.6768 49.2366 74.2936 43.8018C65.6274 35.7002 54.8009 28.877 38.9366 43.8835Z" fill="var(--l_blue)"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip-path">
                                            <path d="M34.6434 5.59803C19.3284 21.023 8.74093 35.4505 4.319 46.9748C2.64729 51.3565 2.01816 54.4913 2.00018 58.4633C1.96423 70.326 7.24898 80.5143 16.7759 86.9087C21.3776 90.008 27.2196 92.1276 33.1334 92.8579C35.5601 93.1607 41.1325 93.1607 43.5591 92.8579C49.473 92.1276 55.315 90.008 59.9167 86.9087C69.3537 80.5677 74.6744 70.4329 74.6924 58.7483C74.7104 54.7051 74.0633 51.4455 72.3916 47.0638C68.6527 37.2139 59.8088 24.4429 47.8732 11.6184C45.2488 8.80415 38.5081 2.00005 38.3463 2.00005C38.2744 2.00005 36.6207 3.62092 34.6434 5.59803Z" stroke="var(--blue)" stroke-width="3"/>
                                        </clipPath>
                                    </defs>
                                    <path d="M34.6434 5.59803C19.3284 21.023 8.74093 35.4505 4.319 46.9748C2.64729 51.3565 2.01816 54.4913 2.00018 58.4633C1.96423 70.326 7.24898 80.5143 16.7759 86.9087C21.3776 90.008 27.2196 92.1276 33.1334 92.8579C35.5601 93.1607 41.1325 93.1607 43.5591 92.8579C49.473 92.1276 55.315 90.008 59.9167 86.9087C69.3537 80.5677 74.6744 70.4329 74.6924 58.7483C74.7104 54.7051 74.0633 51.4455 72.3916 47.0638C68.6527 37.2139 59.8088 24.4429 47.8732 11.6184C45.2488 8.80415 38.5081 2.00005 38.3463 2.00005C38.2744 2.00005 36.6207 3.62092 34.6434 5.59803Z" stroke="var(--blue)" stroke-width="3"/>
                                </svg>
                            </div>
                            <span><?php echo $precip_num . ' ' . $data_current_week['daily_units']['precipitation_sum']; ?></span>
                        </div>
                    </div>
                    <div class="item_sec_3 shadow">

                        <!-- nível e quantidade de UV atual -->
                        <h3>UV</h3>
                        <div id="uv_cont">
                            <div id="uv_info">

                                <?php

                                $currentDateTime = date('Y-m-d\TH:00');
                                $index = array_search($currentDateTime, $data_current['hourly']['time']);

                                $uv_index = number_format(round($data_current['hourly']['uv_index'][$index], 1), 1, '.', '');
                                $uv_percent = ($uv_index * 10) . '%';

                                if ($uv_index < 3) {
                                    $desc_uv = 'Fraco';
                                    $color_b = 'var(--blue)';
                                } elseif ($uv_index >= 3 && $uv_index < 6) {
                                    $desc_uv = 'Médio';
                                    $color_b = 'var(--blue)';
                                } elseif ($uv_index >= 6 && $uv_index < 8) {
                                    $desc_uv = 'Alto';
                                    $color_b = 'var(--yellow)';
                                } elseif ($uv_index >= 8 && $uv_index < 10) {
                                    $desc_uv = 'Muito Alto';
                                    $color_b = 'var(--yellow)';
                                } elseif ($uv_index >= 10) {
                                    $desc_uv = 'Extremo';
                                    $color_b = 'var(--yellow)';
                                }
                                
                                ?>

                                <span><?php echo $uv_index ?></span>
                                <span><?php echo $desc_uv ?></span>
                            </div>
                            <div id="uv_level">
                                <div id="back_level">
                                    <div id="front_level" style="height:<?php echo $uv_percent?>; background-color:<?php echo $color_b?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- footer com dados de redes sociais e copyright -->
            <footer>
                <hr>
                <br>
                <div>
                    <a href="https://www.linkedin.com/in/thiago-nicolau-dev/" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path d="M444.17 32H70.28C49.85 32 32 46.7 32 66.89v374.72C32 461.91 49.85 480 70.28 480h373.78c20.54 0 35.94-18.21 35.94-38.39V66.89C480.12 46.7 464.6 32 444.17 32zm-273.3 373.43h-64.18V205.88h64.18zM141 175.54h-.46c-20.54 0-33.84-15.29-33.84-34.43 0-19.49 13.65-34.42 34.65-34.42s33.85 14.82 34.31 34.42c-.01 19.14-13.31 34.43-34.66 34.43zm264.43 229.89h-64.18V296.32c0-26.14-9.34-44-32.56-44-17.74 0-28.24 12-32.91 23.69-1.75 4.2-2.22 9.92-2.22 15.76v113.66h-64.18V205.88h64.18v27.77c9.34-13.3 23.93-32.44 57.88-32.44 42.13 0 74 27.77 74 87.64z" fill="var(--font)"/>
                        </svg>
                    </a>
                    <a href="https://github.com/tnicolau-dev" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path d="M256 32C132.3 32 32 134.9 32 261.7c0 101.5 64.2 187.5 153.2 217.9a17.56 17.56 0 003.8.4c8.3 0 11.5-6.1 11.5-11.4 0-5.5-.2-19.9-.3-39.1a102.4 102.4 0 01-22.6 2.7c-43.1 0-52.9-33.5-52.9-33.5-10.2-26.5-24.9-33.6-24.9-33.6-19.5-13.7-.1-14.1 1.4-14.1h.1c22.5 2 34.3 23.8 34.3 23.8 11.2 19.6 26.2 25.1 39.6 25.1a63 63 0 0025.6-6c2-14.8 7.8-24.9 14.2-30.7-49.7-5.8-102-25.5-102-113.5 0-25.1 8.7-45.6 23-61.6-2.3-5.8-10-29.2 2.2-60.8a18.64 18.64 0 015-.5c8.1 0 26.4 3.1 56.6 24.1a208.21 208.21 0 01112.2 0c30.2-21 48.5-24.1 56.6-24.1a18.64 18.64 0 015 .5c12.2 31.6 4.5 55 2.2 60.8 14.3 16.1 23 36.6 23 61.6 0 88.2-52.4 107.6-102.3 113.3 8 7.1 15.2 21.1 15.2 42.5 0 30.7-.3 55.5-.3 63 0 5.4 3.1 11.5 11.4 11.5a19.35 19.35 0 004-.4C415.9 449.2 480 363.1 480 261.7 480 134.9 379.7 32 256 32z" fill="var(--font)"/>
                        </svg>
                    </a>
                </div>
                <p>&copy; <?php echo date('Y') ?> tnicolau-dev | Todos os direitos reservados.</p>
            </footer>
        </div>
    </div>

    <!-- caso tenha uma mensagem de erro pega pelo try catch, ele mostra a tela de erro abaixo -->
    <?php else: ?>
        <div id="error">
            <div>
                <svg width="161" height="136" viewBox="0 0 161 136" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="error">
                        <circle id="circulo" cx="80.5" cy="100.5" r="32.5" stroke="#FC261F" stroke-width="6"/>
                        <path id="nuvem" fill-rule="evenodd" clip-rule="evenodd" d="M60.1275 6.12435L60.128 6.12431L30.4459 45.7799V42.6492V39.3431C30.4459 33.6666 31.6901 28.4436 34.1021 23.7371C39.2532 13.6941 49.0411 7.0877 60.1275 6.12435ZM30.4459 45.7802L60.128 6.12431C69.3949 5.31829 78.4212 8.45646 85.1444 14.7841L85.1514 14.7907L85.1584 14.7972C88.4246 17.8515 91.3455 22.0587 92.9301 25.8677C93.2715 26.7223 93.6921 27.6001 94.1886 28.3689C94.4237 28.7328 94.819 29.2982 95.3849 29.8421C95.6662 30.1124 96.1572 30.5413 96.8524 30.9116C97.5141 31.264 98.8284 31.8095 100.531 31.5948C101.104 31.5224 102.538 31.2431 103.8 30.9422C104.555 30.7687 106.131 30.5929 106.995 30.5929C113.979 30.5929 120.34 35.1663 122.736 42.2485L122.736 42.2508L123.27 43.8341L124.518 47.5389L128.412 47.8934L132.594 48.274L132.599 48.2746C141.968 49.1427 148.824 53.8471 152.644 61.6819L152.647 61.6881C153.722 63.8857 154.235 65.2481 154.534 66.5432C154.835 67.8511 154.984 69.3827 154.999 71.9928V71.9944C155.013 74.4439 154.879 76.0096 154.595 77.3616C154.315 78.6965 153.836 80.075 152.906 82.0943C149.701 88.9862 143.536 93.9242 135.973 95.5635C135.425 95.6811 134.653 95.8401 127.068 95.9168C124.26 95.9452 120.584 95.964 115.715 95.9764C115.903 97.4577 116 98.9675 116 100.5C116 100.994 115.99 101.486 115.97 101.976C133.547 101.93 135.513 101.798 137.242 101.428C146.631 99.3932 154.351 93.2261 158.352 84.6112C160.432 80.0971 161.03 77.2996 160.999 71.959C160.967 66.3641 160.369 63.8209 158.037 59.0525C153.248 49.2296 144.489 43.3486 133.146 42.2995L128.956 41.9181L128.42 40.3286C125.238 30.919 116.668 24.5929 106.995 24.5929C105.703 24.5929 103.655 24.8154 102.427 25.1015C101.229 25.3876 100.032 25.6101 99.78 25.6419C99.528 25.6737 98.9923 24.879 98.4882 23.6074C96.5663 18.9662 93.132 14.0388 89.2566 10.4148C81.2852 2.91255 70.5727 -0.806806 59.6081 0.146875C46.438 1.29129 34.8433 9.14326 28.7624 21.0007C25.8952 26.5956 24.4459 32.7627 24.4459 39.3431V42.6492L22.3349 43.1579C12.0635 45.7646 3.58797 54.2523 0.783814 64.8064C-0.255929 68.5893 -0.255929 75.5194 0.752307 79.2706C3.80852 90.6194 12.5046 98.9482 23.8157 101.428C25.5427 101.798 27.5064 101.93 45.0301 101.976C45.0101 101.486 45 100.994 45 100.5C45 98.9674 45.0971 97.4576 45.2855 95.9763C40.4449 95.9639 36.7868 95.9451 33.9902 95.9168C26.4234 95.8403 25.6371 95.6818 25.0887 95.5643C16.0003 93.5676 9.02663 86.9214 6.54605 77.7109L28.9893 47.7262L30.4459 47.3753V45.7802ZM28.9889 47.7263L30.4459 45.7799V45.7802L28.9893 47.7262L28.9889 47.7263ZM28.9889 47.7263L6.5459 77.7104C6.23412 76.5484 5.99652 74.4658 6.00006 72.0592C6.00361 69.6496 6.24807 67.5652 6.56927 66.3965L6.57605 66.3719L6.58262 66.3471C8.84253 57.8415 15.6965 51.0466 23.7776 48.9819L25.8513 48.4823L28.9889 47.7263Z" fill="var(--gray)"/>
                        <path id="x" d="M94 113L68 87M94 87L68 113" stroke="#FC261F" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>
            <h1>Opss!</h1>
            <h2><?php echo $error_message ?></h2>
            <div>
                <p>Recarregue a página ou clique no botão abaixo.</p>
                <button onclick="window.location.href = 'index.php'">Recarregar</button>
            </div>
        </div>
    <?php endif; ?>


    <!-- ------------------------------------------------------------------------------------------- -->
    <!-- ------------------------------------------------------------------------------------------- -->
    <!-- ------------------------------------------------------------------------------------------- -->
     
    <!-- tela de carregamento -->


    <div class="loader popup" id="loading-screen">
        <svg width="123" height="112" viewBox="0 0 123 112" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g id="load">
            <g id="sunny">
              <circle id="sol_s" cx="62.75" cy="56" r="23" stroke="var(--yellow)" stroke-width="6"/>
              <g id="raio_sol">
                <path id="Vector 33" d="M62.75 3V17.5" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 34" d="M62.75 94V108.5" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 33_2" d="M10 55.75H24.5" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 34_2" d="M101 55.75H115.5" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 33_3" d="M100.05 18.4502L89.7969 28.7032" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 34_3" d="M35.7031 82.7969L25.4501 93.0499" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 33_4" d="M25.4502 18.4502L35.7032 28.7032" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 34_4" d="M89.7969 82.7969L100.05 93.0499" stroke="var(--yellow)" stroke-width="6" stroke-linecap="round"/>
              </g>
            </g>
            <g id="chuva">
              <g id="agua">
                <path id="Vector 28" d="M104.092 88L85 107.092" stroke="var(--blue)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 31" d="M81 91L67 105" stroke="var(--blue)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 32" d="M40 91L26 105" stroke="var(--blue)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 29" d="M22.0919 88L3 107.092" stroke="var(--blue)" stroke-width="6" stroke-linecap="round"/>
                <path id="Vector 30" d="M63.0919 88L44 107.092" stroke="var(--blue)" stroke-width="6" stroke-linecap="round"/>
              </g>
              <g id="nuvem_l">
                <mask id="path-15-inside-1_564_41" fill="white">
                  <path d="M47.4284 4.10804C37.6122 4.94952 28.9701 10.723 24.4378 19.4417C22.3008 23.5556 21.2205 28.0903 21.2205 32.9288V35.3598L19.6471 35.7338C11.9914 37.6505 5.67425 43.8915 3.58419 51.6518C2.80923 54.4334 2.80923 59.529 3.56071 62.2872C5.83863 70.6319 12.3202 76.756 20.7508 78.5793C22.6295 78.9766 24.884 79 63.0216 79C101.159 79 103.414 78.9766 105.292 78.5793C112.291 77.0833 118.044 72.5486 121.027 66.2141C122.576 62.895 123.023 60.838 122.999 56.9111C122.976 52.7972 122.529 50.9272 120.792 47.421C117.222 40.1983 110.694 35.874 102.239 35.1027L99.1162 34.8222L98.7169 33.6534C96.3451 26.7346 89.9575 22.083 82.748 22.083C81.7851 22.083 80.2587 22.2467 79.3428 22.457C78.4504 22.6674 77.5581 22.831 77.3702 22.8544C77.1823 22.8778 76.7831 22.2934 76.4073 21.3584C74.9748 17.9458 72.4151 14.3227 69.5266 11.658C63.5852 6.14162 55.6007 3.4068 47.4284 4.10804Z"/>
                </mask>
                <path d="M47.4284 4.10804C37.6122 4.94952 28.9701 10.723 24.4378 19.4417C22.3008 23.5556 21.2205 28.0903 21.2205 32.9288V35.3598L19.6471 35.7338C11.9914 37.6505 5.67425 43.8915 3.58419 51.6518C2.80923 54.4334 2.80923 59.529 3.56071 62.2872C5.83863 70.6319 12.3202 76.756 20.7508 78.5793C22.6295 78.9766 24.884 79 63.0216 79C101.159 79 103.414 78.9766 105.292 78.5793C112.291 77.0833 118.044 72.5486 121.027 66.2141C122.576 62.895 123.023 60.838 122.999 56.9111C122.976 52.7972 122.529 50.9272 120.792 47.421C117.222 40.1983 110.694 35.874 102.239 35.1027L99.1162 34.8222L98.7169 33.6534C96.3451 26.7346 89.9575 22.083 82.748 22.083C81.7851 22.083 80.2587 22.2467 79.3428 22.457C78.4504 22.6674 77.5581 22.831 77.3702 22.8544C77.1823 22.8778 76.7831 22.2934 76.4073 21.3584C74.9748 17.9458 72.4151 14.3227 69.5266 11.658C63.5852 6.14162 55.6007 3.4068 47.4284 4.10804Z" stroke="white" stroke-width="12" mask="url(#path-15-inside-1_564_41)"/>
              </g>
            </g>
          </g>
        </svg>
    </div>

    <script>

        <?php

        //variáveis para o gráfico - canvas

        $times = isset($data_current_hr_at_gr['time'])?array_values($data_current_hr_at_gr['time']):null;
        $temperatures = isset($data_current_hr_at_gr['temperature_2m'])?array_values($data_current_hr_at_gr['temperature_2m']):null;
        $images = isset($data_current_hr_at_gr['image'])?array_values($data_current_hr_at_gr['image']):null;
        $precipitation = isset($data_current_hr_at_gr['precipitation'])?array_values($data_current_hr_at_gr['precipitation']):null;

        $times_json = isset($times)?json_encode($times):'';
        $temperatures_json = isset($temperatures)?json_encode($temperatures):'';
        $images_json = isset($images)?json_encode($images):'';
        $precipitation_json = isset($precipitation)?json_encode($precipitation):'';
    
        ?>

        const times = <?php echo $times_json; ?>;
        const temperatures = <?php echo $temperatures_json; ?>;
        const images = <?php echo $images_json; ?>;
        const precipitation = <?php echo $precipitation_json; ?>;
        const max_temp = <?php echo round(max($temperatures)); ?>;
        const min_temp = <?php echo round(min($temperatures)); ?>;

        const timezone_l = <?php echo "'" . $timezone_l . "'"; ?>;

    </script>

    <script src="./source/main.js" type="text/javascript"></script>

</body>
</html>