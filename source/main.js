// atualiza relógio

function atualizarRelogio() {

    const agora = new Date();

    const dia = String(agora.getDate()).padStart(2, '0');
    const mes = String(agora.getMonth() + 1).padStart(2, '0');
    const horas = String(agora.getHours()).padStart(2, '0');
    const minutos = String(agora.getMinutes()).padStart(2, '0');

    const formatoRelogio = `${dia}/${mes} ${horas}:${minutos}`;

    document.getElementById('relogio').innerText = formatoRelogio;
}

setInterval(atualizarRelogio, 60000);

atualizarRelogio();

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

const button_r = document.getElementById('refresh');
button_r.addEventListener('click', () => {

    mostrarLoader();
    window.location.href = 'index.php';

});

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//loader


//onReload

window.addEventListener("beforeunload", () => {
    mostrarLoader();
});


// Função para mostrar a tela de carregamento
function mostrarLoader() {
    document.getElementById("loading-screen").style.display = "flex";
    document.body.style.overflow = 'hidden';
}

// Função para ocultar a tela de carregamento
function ocultarLoader() {
    document.getElementById("loading-screen").style.display = "none";
}

window.addEventListener("pageshow", () => {
    ocultarLoader();
});
        


//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//dark mode

const button = document.getElementById('toggle-button');

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

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------

//search bar

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

                            let iso = (item.address['ISO3166-2-lvl4']) ? item.address['ISO3166-2-lvl4'] : country_code;

                            $('#suggestions').append(`<div class="suggestion-item" data-lat="${item.lat}" data-lon="${item.lon}" data-state="${state}" data-county="${county}" data-municipality="${municipality}" data-country_code="${country_code}" data-country="${country}">${item.name} - ${iso}</div><hr>`);
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

        mostrarLoader();

        window.location.href = `./index.php?latitude=${lat}&longitude=${lon}&region=${reg}&county=${coun}&city=${city}&country_code=${coun_c}&country=${count}`;

    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#search').length) {
            $('#suggestions').hide();
        }
    });
});