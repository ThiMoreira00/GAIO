class Tabela {

    constructor(tabelaId, opcoes = {}, acoes = {}) {
        this.tabela = document.getElementById(tabelaId);

        if (!this.tabela) {
            console.error(`[Tabela] Tabela com ID "${tabelaId}" não encontrada.`);
            return;
        }

        this.opcoes = opcoes;
        this.acoes = acoes;
    }

}