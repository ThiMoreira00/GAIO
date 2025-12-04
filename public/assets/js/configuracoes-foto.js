/*
=====================================================
    SCRIPT COMPLETO PARA MANIPULAÇÃO DE FOTO DE PERFIL
=====================================================
*/

// Garante que o script só execute após o carregamento completo da página
document.addEventListener('DOMContentLoaded', function () {

    // --- 1. CONFIGURAÇÕES ---
    // Objeto que centraliza as referências aos elementos do HTML e outras configurações.
    const configuracoesFoto = {
        inputFoto: document.querySelector('#fotoPerfilInput'),
        previewFoto: document.querySelector('#fotoPerfilImagem'),
        botaoAlterar: document.querySelector('#alterarFotoButton'),
        botaoRemover: document.querySelector('#removerFotoButton'),
        urlFotoPadrao: '/assets/img/usuario-padrao.png', // Caminho para a imagem padrão
        fotoPadrao: true
    };

    /**
     * Função placeholder (substituta) para simular a verificação de alterações no formulário.
     * No seu script original, esta função continha a lógica para avisar o usuário
     * sobre alterações não salvas.
     */
    function verificarAlteracoes() {
        console.log("Verificando alterações... A foto foi modificada.");
        // Exemplo de ação: alert('Você possui alterações não salvas.');
    }


    // --- 2. FUNÇÕES DE MANIPULAÇÃO ---

    /**
     * Define o estado inicial dos botões com base na imagem de perfil atual.
     * Se for a imagem padrão, o botão "Remover" é ocultado.
     */
    function inicializarManipulacaoFoto() {
        // Verifica se a foto atual é a padrão pela URL da imagem
        configuracoesFoto.fotoPadrao = configuracoesFoto.previewFoto.src.includes('usuario-padrao.png');

        // Atualiza a visibilidade do botão de remover com base no resultado
        configuracoesFoto.botaoRemover.style.display = configuracoesFoto.fotoPadrao ? 'none' : 'inline-block';
    }

    /**
     * Aciona o clique no input de arquivo (que está oculto),
     * abrindo a janela para o usuário selecionar uma nova imagem.
     */
    function alterarFoto() {
        configuracoesFoto.inputFoto.click();
    }

    /**
     * Restaura a imagem de perfil para a imagem padrão e oculta o botão "Remover".
     */
    function removerFoto() {
        configuracoesFoto.previewFoto.src = configuracoesFoto.urlFotoPadrao;
        configuracoesFoto.inputFoto.value = ''; // Limpa o valor do input de arquivo
        configuracoesFoto.fotoPadrao = true;
        configuracoesFoto.botaoRemover.style.display = 'none';
        verificarAlteracoes(); // Notifica que houve uma alteração
    }

    /**
     * Lê o arquivo de imagem selecionado pelo usuário e o exibe como preview.
     * @param {Event} evento - O evento 'change' do input de arquivo.
     */
    function visualizarFoto(evento) {
        const arquivo = evento.target.files[0];
        if (arquivo) {
            const leitor = new FileReader();

            // Quando a leitura do arquivo for concluída
            leitor.onload = function(e) {
                // Define o 'src' da imagem para o resultado da leitura (base64)
                configuracoesFoto.previewFoto.src = e.target.result;
                configuracoesFoto.fotoPadrao = false;
                configuracoesFoto.botaoRemover.style.display = 'inline-block'; // Mostra o botão "Remover"
                verificarAlteracoes(); // Notifica que houve uma alteração
            };

            // Inicia a leitura do arquivo como uma Data URL (string base64)
            leitor.readAsDataURL(arquivo);
        }
    }


    // --- 3. EVENT LISTENERS (OUVINTES DE EVENTOS) ---
    // Conectam as funções acima às ações do usuário (cliques e seleção de arquivo).

    configuracoesFoto.botaoAlterar.addEventListener('click', alterarFoto);
    configuracoesFoto.botaoRemover.addEventListener('click', removerFoto);
    configuracoesFoto.inputFoto.addEventListener('change', visualizarFoto);


    // --- 4. INICIALIZAÇÃO ---
    // Executa a função inicial para configurar o estado correto assim que a página carrega.
    inicializarManipulacaoFoto();

});