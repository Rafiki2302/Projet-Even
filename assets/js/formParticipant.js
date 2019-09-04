const $ = require('jquery');

$("#changePwd").click(function (event) {
    $("#changePwd").after($("<div><label for=\"participant_motDePasseEnClair\">Mot de passe :</label>" +
        "<input type=\"password\" id=\"participant_motDePasseEnClair\" name=\"participant[motDePasseEnClair]\">" +
        "</div><div><label for=\"participant_motdePasseRepeat\">Confirmez votre mot de passe : </label>" +
        "<input type=\"password\" id=\"participant_motdePasseRepeat\" name=\"participant[motdePasseRepeat]\"></div>"));
    $("#changePwd").remove();
});