jQuery(document).ready(function($) {
    // Intercetta il cambio di stato dell'ordine
    $('#order_status').on('change', function() {
        var selectedStatus = $(this).val();

        // Se lo stato è "spedito", richiedi il codice di tracking
        if (selectedStatus === 'wc-order-shipped') {
            var tracking = prompt('Inserisci il codice di tracking per questo ordine:');

            // Se il codice di tracking non è nullo, aggiungilo come campo nascosto
            if (tracking !== null) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'tracking_number',
                    value: tracking
                }).appendTo('#post');
            }
        }
    });
});
