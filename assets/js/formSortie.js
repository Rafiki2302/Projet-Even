
const $ = require('jquery');

$(document).ready(function ($) {

    var idRue = $("#sortie_lieu").val();

    $.ajax("../lieu/infos",{
        method: "post",
        data: {id: idRue}
    }).done(function (msg) {
        var lieu = JSON.parse(msg['data']);
        $("#rue").text("Rue : " + lieu.rue);

    }).fail(function () {
        console.log("Erreur dans la requête Ajax");
    });


});

$("#enregistrer").on("click",function (event) {
    console.log('coucou');
})