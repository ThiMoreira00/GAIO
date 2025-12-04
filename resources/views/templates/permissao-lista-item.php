<template id="template-permissao-lista-item">
    <li class="py-3 flex items-center justify-between">
        <div class="mr-4">
            <p class="permissao-nome text-base font-medium text-gray-900"></p>
            <p class="permissao-desc text-base text-gray-500"></p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer permissao-checkbox" name="permissoes[]">
            <div class="w-11 h-6 bg-gray-200 rounded-full peer
                        peer-focus:outline-none peer-focus:ring-4
                        peer-focus:ring-sky-300
                        peer-checked:after:translate-x-full
                        peer-checked:after:border-white
                        after:content-[''] after:absolute
                        after:top-0.5 after:left-[2px]
                        after:bg-white after:rounded-full
                        after:h-5 after:w-5 after:transition-all
                        peer-checked:bg-sky-600"></div>
        </label>
    </li>
</template>