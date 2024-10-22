//-----------------------------------------------------------------------------------------------
// função para atualizar o relógio de acordo com o time zone

function atualizarRelogio() {

    const agora = new Date();
    const opcoesData = {
        timeZone: timezone_l,
        day: '2-digit',
        month: '2-digit'
    };

    const opcoesHora = {
        timeZone: timezone_l,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    };

    const dataFormatada = agora.toLocaleDateString('pt-BR', opcoesData);
    const horaFormatada = agora.toLocaleTimeString('pt-BR', opcoesHora);
    const formatoRelogio = `${dataFormatada} ${horaFormatada}`;

    const relogio = document.getElementById('relogio');

    if (relogio) {
        relogio.innerText = formatoRelogio;
    }
}

setInterval(atualizarRelogio, 60000);

atualizarRelogio();

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//função utilizada para dar um refresh na página redirecionando para o index, limpando assim os dados da URL

const button_r = document.getElementById('refresh');
if(button_r){
    button_r.addEventListener('click', () => {

        mostrarLoader();
        window.location.href = 'index.php';
    
    });
}

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------   

//função para ativar/desativar o dark mode do site, além de identificr automaticamente se o dispositivo está no modo escuro ou não

const button = document.getElementById('toggle-button');

if(button){

    button.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        document.body.classList.toggle('light');
    
        if (document.body.classList.contains('dark')) {
            document.getElementById("light_i").style.display = 'none';
            document.getElementById("night_i").style.display = 'initial';
        } else {
            document.getElementById("night_i").style.display = 'none';
            document.getElementById("light_i").style.display = 'initial';
        }
    
        draw();
    
    });
    
    
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    
        document.body.classList.add('dark');
    
        document.getElementById("light_i").style.display = 'none';
        document.getElementById("night_i").style.display = 'initial';
    
        draw();
    
    } else {
        document.body.classList.add('light');
    
        document.getElementById("night_i").style.display = 'none';
        document.getElementById("light_i").style.display = 'initial';
    }
}

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//função para desenhar o gráfico na tag canvas

var splineAreaChart;

function draw(){

    const ctx = document.getElementById('temperatureChart').getContext('2d');
    
    //pega as variáveis de cor do css do body

    var fontColor = getComputedStyle(document.body).getPropertyValue('--font').trim();
    var lightBlueColor = getComputedStyle(document.body).getPropertyValue('--light_blue').trim();
    var blueColor = getComputedStyle(document.body).getPropertyValue('--blue').trim();
    var whiteColor = getComputedStyle(document.body).getPropertyValue('--white').trim();
    var grayColor = getComputedStyle(document.body).getPropertyValue('--gray').trim();
    var yellowColor = getComputedStyle(document.body).getPropertyValue('--yellow').trim();
    var lineColor = getComputedStyle(document.body).getPropertyValue('--line_g').trim();

    //caso o gráfico já exista, destroi ele para ser possível redesenhá-lo

    if (splineAreaChart) {
        splineAreaChart.destroy();
    }

    //------------------------------------------------------------------------------------

    //converte o código HEX das variáveis de cor para RGB

    function hexToRgba(hex, alpha) {
        hex = hex.replace('#', '');
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    //gradiente do gráfico

    var gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, hexToRgba(lightBlueColor, 1));
    gradient.addColorStop(1, hexToRgba(whiteColor, 0));

    //------------------------------------------------------------------------------------

    var customLabelsPlugin = {
        id: 'customLabelsPlugin',
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
        
            ctx.fillStyle = fontColor;
        
            const promises = chart.data.labels.map((label, index) => {
                return new Promise((resolve) => {

                    //trecho para desenhar os icones do clima no gráfico com suas respectiveis cores

                    var x = chart.scales.x.getPixelForValue(index);
                    var y = chart.scales.y.bottom + 15;
                
                    var svg = images[index];
                    svg = svg.replace(/var\(--gray\)/g, grayColor);
                    svg = svg.replace(/var\(--white\)/g, whiteColor);
                    svg = svg.replace(/var\(--blue\)/g, blueColor);
                    svg = svg.replace(/var\(--yellow\)/g, yellowColor);
                    svg = svg.replace(/var\(--font\)/g, fontColor);

                    var svgBlob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8' });
                    var url_t = URL.createObjectURL(svgBlob);
                    var img = new Image();

                    img.src = url_t;

                    //------------------------------------------

                    //trecho para desenhar os icones de chuva no gráfico

                    var svg_w = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--font)"><path d="M480-152q-111.39 0-189.69-76.71Q212-305.41 212-415.47 212-468 233.5-516t56.5-86l190-186 190 186q35 38 56.5 86.04Q748-467.92 748-415.28 748-305 669.69-228.5 591.39-152 480-152Zm0-28q100 0 170-68t70-167.23q0-47.11-18-89.71-18-42.6-52-74.67L480-748 310-579.61q-34 32.07-52 74.67t-18 89.71Q240-316 310-248q70 68 170 68Z"/></svg>';
                    svg_w = svg_w.replace(/var\(--font\)/g, fontColor);
                    
                    var svgBlob_w = new Blob([svg_w], { type: 'image/svg+xml;charset=utf-8' });
                    var url_t_w = URL.createObjectURL(svgBlob_w);
                    var img_w = new Image();
                    
                    img_w.src = url_t_w;

                    //---------------------------------------------

                    //trecho para colocar a porcentagem de chuva e a hora no gráfico

                    var img = new Image();
                    img.src = url_t;
                    img.onload = function() {
                        const originalWidth = img.width;
                        const originalHeight = img.height;
                        const desiredWidth = 35;
                        const scaleFactor = desiredWidth / originalWidth;
                        const desiredHeight = originalHeight * scaleFactor;
                        ctx.drawImage(img, x - 20, y - 10, desiredWidth, desiredHeight);
                        ctx.drawImage(img_w, x-27, y+30, 20, 20);
                    
                        ctx.font = '200 14px Poppins';
                        ctx.fillText(precipitation[index] + '%', x+5, y + 45);
                    
                        ctx.font = '350 24px Poppins';
                        ctx.fillText(times[index] + 'h', x, y + 80);
                    
                        URL.revokeObjectURL(url_t);
                        resolve();
                    };
                });
            });
        }
    };

    //trecho para colocar a temperatura no gráfico

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
                    ctx.fillStyle = fontColor;
                    ctx.font = '24px Poppins';
                    ctx.textAlign = 'center';
                    ctx.fillText(Math.round(value)+"°", x, 40);
                });
            });
        }
    };

    splineAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: times,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatures,
                borderColor: blueColor,
                backgroundColor: gradient,
                borderWidth: 5,
                tension: 0.4,
                fill: true,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    grid: {
                        color: lineColor,
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
                    min: min_temp - 1, //margem de segurança para a linha do gráfico não saia para fora da área visivel
                    max: max_temp + 1  //margem de segurança para a linha do gráfico não saia para fora da área visivel
                }
            }
        },

        //adiciona as customizações criadas anteriormente

        plugins: [topValuesPlugin, customLabelsPlugin]
    });
}

draw();

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//função dedicada a funcionalidade da barra de pesquisa

$(document).ready(function() {
    $('#search').on('input', function() {
        let query = $(this).val();
        if (query.length < 4) {
            $('#suggestions').hide();
            return;

        } else {

            $.ajax({
                url: `https://nominatim.openstreetmap.org/search?format=json&q=${query}&addressdetails=1`,
                method: 'GET',
                success: function(data) {
                    $('#suggestions').empty();
                    if (data.length) {

                        const limitedData = data.slice(0, 5);

                        limitedData.forEach(item => {
                            let county = (item.address.county) ? item.address.county : '';
                            let country = (item.address.country) ? item.address.country : '';
                            let municipality = (item.address.municipality) ? item.address.municipality : '';
                            let state = (item.address.state) ? item.address.state : '';
                            let country_code = (item.address.country_code) ? item.address.country_code.toUpperCase() : '';

                            let c = '';

                            if(state){
                                c = '- ' + state;
                            } else if(county){
                                c = '- ' + county;
                            }

                            

                            let iso = (item.address['ISO3166-2-lvl4']) ? item.address['ISO3166-2-lvl4'] : country_code;

                            //cria os itens que aparecem abaixo da barra de pesquisa de acordo com o que o usuário for digitando

                            $('#suggestions').append(`<div class="suggestion-item" data-lat="${item.lat}" data-lon="${item.lon}" data-state="${state}" data-county="${county}" data-municipality="${municipality}" data-country_code="${country_code}" data-country="${country}">${item.name} ${c} - ${iso}</div><hr>`);
                        });
                        $('#suggestions').show();
                        $('#suggestions hr:last').remove();
                    } else {
                        $('#suggestions').hide();
                    }
                }
            });
        }
    });

    $(document).on('click', '.suggestion-item', function() {
        let selectedCity = $(this).text();
        let lat = $(this).data('lat');
        let lon = $(this).data('lon');

        let reg = $(this).data('state');
        let coun = $(this).data('county');
        let city = $(this).data('municipality');
        let coun_c = $(this).data('country_code');
        let count = $(this).data('country');

        $('#search').val(selectedCity);
        $('#suggestions').hide();

        //ao clicar em um dos itens de pesquisa, ele mostra a tela de carregamento e redireciona para o index porém passando parâmetros pela URL

        mostrarLoader();

        window.location.href = `./index.php?latitude=${lat}&longitude=${lon}&region=${reg}&county=${coun}&city=${city}&country_code=${coun_c}&country=${count}`;

    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#search').length) {
            $('#suggestions').hide();
        }
    });
});