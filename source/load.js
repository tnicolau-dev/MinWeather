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

//se a tela carregou seu conteúdo, oculda a tela de carregamento
window.addEventListener("load", () => {
    ocultarLoader();
});

//qualquer evendo que carregue ou recarregue a página ele vai mostrar a tela de carregamento
window.addEventListener("beforeunload", () => {
    mostrarLoader();
});