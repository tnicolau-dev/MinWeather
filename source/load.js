// Função para mostrar a tela de carregamento
function mostrarLoader() {
    document.getElementById("loading-screen").style.display = "flex";
    document.body.style.overflow = 'hidden';
    window.scrollTo({top: 0});
}

// Função para ocultar a tela de carregamento
function ocultarLoader() {
    document.getElementById("loading-screen").style.display = "none";
}


window.addEventListener("load", () => {
    ocultarLoader();
});

window.addEventListener("beforeunload", () => {
    mostrarLoader();
});