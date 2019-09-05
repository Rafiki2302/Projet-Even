const $ = require('jquery');

//si on clique sur le bouton "changer mon mot de passe, alors on affiche les champs de modif du omt de passe
//(champs qui s'afficheraient si on faisait appel à {{ form_row(form.motDePasseEnClair) }}
// et {{ form_row(form.motdePasseRepeat) }} dans le twig
$("#changePwd").click(function (event) {
    $("#changePwd").after($("<div><label for=\"participant_motDePasseEnClair\">Mot de passe :</label>" +
        "<input type=\"password\" id=\"participant_motDePasseEnClair\" name=\"participant[motDePasseEnClair]\">" +
        "</div><div><label for=\"participant_motdePasseRepeat\">Confirmez votre mot de passe : </label>" +
        "<input type=\"password\" id=\"participant_motdePasseRepeat\" name=\"participant[motdePasseRepeat]\"></div>"));
    $("#changePwd").remove();
});