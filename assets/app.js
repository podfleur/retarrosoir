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

    $('.like-button, .unlike-button').click(function(e) {
    
        var actionUrl = $(this).data('action-url'); // Récupère l'URL de l'action à effectuer (like ou unlike)
        var likeButton = $(this); // Stocke une référence au bouton de like

        console.log(actionUrl)
    
        // Envoie une requête AJAX pour liker ou déliker le post
        $.ajax({
            url: actionUrl,
            type: 'GET',
            success: function(response) {
                var nbLikes = response.nb_like; // Récupère le nombre de likes du post depuis la réponse AJAX
                var likeText = nbLikes > 1 ? ' like' : ' likes'; // Détermine le texte à afficher en fonction du nombre de likes
    
                // Met à jour le nombre de likes affiché
                likeButton.siblings('.like-count').text(nbLikes + likeText);
    
                // Affiche le bouton liker ou unliker en fonction de l'action effectuée
                if (likeButton.hasClass('like-button')) {
                    likeButton.hide();
                    likeButton.siblings('.unlike-button').show();
                } else {
                    likeButton.hide();
                    likeButton.siblings('.like-button').show();
                }
            },
            error: function(xhr, status, error) {
                console.error(error); // Gère les erreurs éventuelles
            }
        });
    });
    

})
