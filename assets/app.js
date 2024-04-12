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
                var likeText = nbLikes > 1 ? ' likes' : ' like'; // Détermine le texte à afficher en fonction du nombre de likes
    
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

    $('.golden-like-button').click(function(e) {
        
        var actionUrl = $(this).data('action-url'); // Récupère l'URL de l'action à effectuer (like ou unlike)
        var likeButton = $(this); // Stocke une référence au bouton de like
    
        // Envoie une requête AJAX pour liker ou déliker le post
        $.ajax({
            url: actionUrl,
            type: 'GET',
            success: function(response) {
                console.log(response)
            },
            error: function(xhr, status, error) {
                console.error(error); // Gère les erreurs éventuelles
            }
        });
    })

    $('.open-commentaire').click(function(e) {

        let id = $(this).data('id'); // Récupère l'id du post à commenter

        e.preventDefault(); // Pour éviter que le formulaire ne se soumette immédiatement
    
        // Trouver le parent contenant les commentaires en utilisant la méthode closest()
        let commentairesSection = $(`#commentaires-${id}`);

        // On récupère tous les commentaires du post
        $.ajax({
            url: `/commentaire/commentaire_post/${id}`,
            type: 'GET',
            success: function(response) {
                console.log(response.commentaires)
                
                // Met à jour la liste des commentaires
                let commentaires = JSON.parse(response.commentaires);

                // On vide le contenu actuel de la section des commentaires
                $(`#liste-commentaires-${id}`).empty();

                // On ajoute chaque commentaire à la section des commentaires
                // On ajoute les commentaires
                commentaires.forEach(commentaire => {
                    $(`#liste-commentaires-${id}`).append(`
                        <div class="d-flex justify-content-between align-items-center w-100 border-bottom gap-2">
                            <div class="d-flex align-items-start justify-content-center flex-column ">
                                <p class="fw-bold">@${commentaire.username}</p>
                                <p>${commentaire.commentaire}</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <p class="h-100">${commentaire.date}</p>
                                <a href="{{ deleteCommentaireUrl }}" class="delete-commentaire" data-id="${commentaire.id}"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    `);
                });
            
            }
        
        })
    
        // Inverser l'état d'affichage de la section des commentaires
        commentairesSection.show();

    });
        
})
