$('#add-image').click(function () {
    //récupération de la valeur de l'input widgets-counter
    // le + devant permet de parser la valeur en int et non en chaine de caractère
    const index = +$('#widgets-counter').val();
    console.log($('#widgets-counter'));
    //recupération du prototype
    const template = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    // on ajoute cette template à la suite des autres
    $('#annonce_images').append(template);
    // on ajoute 1 à la valeur de l'input widgets-counter
    $('#widgets-counter').val(index+1);
    // je gére le bouton supprimer
    handleDeleteButtons();
})

// quand on clique sur un bouton supprimer
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target; // on récupère l'id de la div à supprimer
        console.log(target);
        $(target).remove(); // on supprime la div
    })
}
function updateCounter(){
    const count= +$('#annonce-images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();