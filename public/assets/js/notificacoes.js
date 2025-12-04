$(function() {
    // Seletores dos elementos
    const $notificationButton = $('#notification-button');
    const $notificationDropdown = $('#notification-dropdown');
    const $notificationBadge = $('#notification-badge');
    const $notificationCount = $('#notification-count');

    // Lógica do Dropdown
    function toggleDropdown() {

        const isHidden = $notificationDropdown.hasClass('hidden');
        if (isHidden) {
            $notificationDropdown.removeClass('hidden').css({ 'opacity': '1', 'transform': 'scale(1)' });
            $notificationButton.attr('aria-expanded', 'true');
        } else {
            $notificationDropdown.css({ 'opacity': '0', 'transform': 'scale(0.95)' });
            setTimeout(() => $notificationDropdown.addClass('hidden'), 200);
            $notificationButton.attr('aria-expanded', 'false');
        }
    }
    $notificationButton.on('click', (event) => {
        event.stopPropagation();
        toggleDropdown();
    });
    $(window).on('click', (event) => {
        if (!$notificationDropdown.hasClass('hidden') && !$notificationButton[0].contains(event.target) && !$notificationDropdown[0].contains(event.target)) {
            toggleDropdown();
        }
    });

    // --- LÓGICA DA CONTAGEM DE NOTIFICAÇÕES ---
    function updateBadge(count) {
        if (count > 0) {
            $notificationCount.text(count);
            $notificationBadge.removeClass('hidden');
        } else {
            $notificationBadge.addClass('hidden');
        }
    }

    function fetchUnreadCount() {
        const initialCount = $('.mark-as-read-btn').length;
        console.log("Contagem inicial de notificações não lidas:", initialCount);
        updateBadge(initialCount);
    }


    $('#notification-dropdown').on('click', '.mark-as-read-btn', function() {
        const $button = $(this);
        const notificationId = $button.data('id');
        const $item = $button.closest('.notification-item');

        // 1. Simular chamada AJAX para o backend
        console.log(`Enviando requisição para marcar a notificação ${notificationId} como lida...`);
        // $.post('/notificacoes/ler', { notificacao_id: notificationId });


        const currentCount = parseInt($notificationCount.text());
        const newCount = currentCount > 0 ? currentCount - 1 : 0;
        updateBadge(newCount);

        // 3. Animar e remover o item da lista
        $item.css('background-color', '#f0f9ff'); // Efeito visual sutil de clique
        $item.animate({ opacity: 0.5, height: 0, padding: 0, margin: 0 }, 400, function() {
            $(this).remove();
        });
    });

    // --- INICIALIZAÇÃO ---
    fetchUnreadCount();
});