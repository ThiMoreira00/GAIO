const fotoPerfilInput = document.getElementById('fotoPerfilInput');
const imagePreview = document.getElementById('fotoPerfilImagem');
const removerFotoButton = document.getElementById('removerFotoButton');
const alterarFotoButton = document.getElementById('alterarFotoButton');

// Placeholder para a imagem padrão (círculo preto)
const imagemPadrao = '/assets/img/usuario-padrao.png'

// Adicionar um evento de, quando clicar no botão de 'Alterar', clique no botão de input
alterarFotoButton.addEventListener('click', function() {
    fotoPerfilInput.click();
});

// Evento para quando um arquivo é selecionado
fotoPerfilInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
        }
        reader.readAsDataURL(file);
        fotoPerfilInput.value = file.name;
    }
});

// Evento para o botão de remover
removerFotoButton.addEventListener('click', function() {
    imagePreview.src = imagemPadrao;
    fotoPerfilInput.value = '';
});