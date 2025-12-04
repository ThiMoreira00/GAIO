<template id="template-modal-adicionar-membros">
    <div id="modal-adicionar-membros" class="hidden fixed inset-0 z-[51] items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl flex flex-col h-[90vh] max-h-[600px]">
            <header class="p-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-semibold leading-6 text-gray-800">Adicionar membros</h3>
                <p class="mt-1 text-sm text-gray-500">Selecione os membros para adicionar ao grupo.</p>
                <button type="button" class="modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
            </header>
            <main class="p-4 flex-grow overflow-y-auto">
                <div class="relative mb-4">
                    <span class="material-icons-sharp text-gray-400 absolute inset-y-0 left-0 pl-3 !flex items-center">search</span>
                    <label for="buscar-membros-disponiveis-input"></label><input type="text" id="buscar-membros-disponiveis-input" class="form-input pl-10 w-full" placeholder="Buscar membros">
                </div>
                <form id="form-adicionar-membros">
                    <div id="lista-membros-disponiveis" class="space-y-2">
                    </div>
                </form>
            </main>
            <footer class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between flex-shrink-0">
                <div class="flex items-center">
                    <input type="checkbox" id="selecionar-todos-membros" class="mr-2">
                    <label for="selecionar-todos-membros" class="text-sm text-gray-600 select-none">Selecionar todos</label>
                </div>
                <div class="flex flex-shrink-0 gap-3 justify-end">
                    <button type="button" class="button-secondary modal-fechar">Cancelar</button>
                    <button type="button" id="btn-confirmar-adicionar-membros" class="button-primary">Adicionar</button>
                </div>
            </footer>
        </div>
    </div>
    <script type="text/javascript">
        document.getElementById('selecionar-todos-membros').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#lista-membros-disponiveis input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</template>