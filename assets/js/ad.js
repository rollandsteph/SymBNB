$('#add-image').click(function () {
    //récupération de la valeur de l'input widgets-counter
    // le + devant permet de parser la valeur en int et non en chaine de caractère
    const index = +$('#widgets-counter').val();
    //recupération du prototype
    const template = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    // on ajoute cette template à la suite des autres
    $('#annonce_images').append(template);
    // on ajoute 1 à la valeur de l'input widgets-counter
    $('#widgets-counter').val(index + 1);
    // je gére le bouton supprimer
    handleDeleteButtons();
    updateTitre();
})

// quand on clique sur un bouton supprimer
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target; // on récupère l'id de la div à supprimer
        $(target).remove(); // on supprime la div
        $('#widgets-counter').val(+$('#widgets-counter').val() - 1);
        updateTitre();
    })
}

// fonction qui se lance uniquement au démarrage pour compter le nombre d'images
function updateCounter() {
    const count = +$('#annonce-images div.form-group').length; // on recupère le nombre d'images
    $('#widgets-counter').val(count); // on met à jour la variable widgets-counter
}

// fonction qui permet d'afficher ou masquer les titres des colonnes des images
function updateTitre(){
    const count =+$('#widgets-counter').val(); // on récupère la variable widgets-counter
    //on s'occupe d'afficher les titres de colonnes s'il y a au moins une image
    console.log(count);
    if (count > 0) { // s'il y a au moins une image
        $('#titreCol').show(); // on affiche les titres de colonnes
    } else {
        $('#titreCol').hide(); // on masque les titres de colonnes
    }
}

updateCounter();
handleDeleteButtons();
updateTitre();
