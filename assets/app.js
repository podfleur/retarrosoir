// Autocompletion barre de recherche et affichage de la croix rouge avec jquery

$(document).ready(function() {
    $('#search-bar').on('input', () => {
        if ($('#search-bar').val() !== '') {
            $('#clear-input').show();
        } else {
            $('#clear-input').hide();
        }

        $('#search-bar').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/autocomplete",
                    dataType: 'json',
                    data: {
                        search: request.term // Envoie le terme de recherche au serveur
                    },
                    success: function(data) {
                        console.log(data)
                        response(data); // Affiche les suggestions renvoyées par le serveur
                    }
                });
            },
            minLength: 1 // Nombre minimum de caractères avant de lancer une recherche
        });
    })
    $('#clear-input').on('click', () => {
        $('#search-bar').val('');
        $('#clear-input').hide();
    })
})