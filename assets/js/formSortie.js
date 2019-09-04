
const $ = require('jquery');

$(document).ready(function ($) {
    affLieu($);

    /*
    méthode permettant d'éviter les conflits avec d'autres librairies
    elle est utilisée ici pour faire appel à la méthode .modal associée à la modale bootstrap
    rérérence : https://learn.jquery.com/using-jquery-core/avoid-conflicts-other-libraries/
    */
    const $j = jQuery.noConflict();
    $j("#exampleModal").modal('show');

});

$("#sortie_lieu").change(function (event) {
    affLieu();
});

$("#testSelect").click(function(event){
    var compteur = 3;
    $("#sortie_lieu option[value='5']").prop('selected',true);
    compteur++;
})

$("#enregistrerLieu").click(function (event) {
   var nomLieu = $("#sortie_nouveauLieu_nom").val();
   var nomRue = $("#sortie_nouveauLieu_rue").val();
   var lat = $("#sortie_nouveauLieu_latitude").val();
   var lon = $("#sortie_nouveauLieu_longitude").val();
   var villeId = $("#sortie_nouveauLieu_ville").val();
   var lieu = new Lieu(nomLieu,nomRue,lat,lon,villeId);

   enregistrerLieu(lieu)

});

/**
 * Fonction permettant d'enregistrer un lieu via une requête ajax
 * @param lieu
 */
function enregistrerLieu(lieu){

    //on récupère l'url de la page permettant de créer un lieu via un attribut caché dans la page
    var urlCreaLieu = $("#urlCreationLieu").val();

    $.ajax(urlCreaLieu,{
        method: "post",
        data: {lieu: lieu}
    }).done(function (msg) {
        $("#messErreur").remove();
        if(JSON.parse(msg["data"]).erreur !== undefined){
            $("<p id='messErreur'>"+JSON.parse(msg["data"]).erreur+"</p>").insertBefore("#sortie_nouveauLieu");
        }
        else{
            var lieu = JSON.parse(msg["data"]);
            console.log(lieu);
            $("#sortie_lieu option:selected").prop('selected',false);
            $("#sortie_lieu").append(new Option(lieu.nomLieu,lieu.idLieu,true,true));
            $("#sortie_lieu:last-child").prop('selected',true);
            affLieu();
            $("<p>"+"Nouveau lieu créé !"+"</p>").insertBefore("#sortie_nouveauLieu");
            $(".modal-footer").empty();
            var nouvelElement = $("<button/>",{
                html: "Retour à la création de la sortie",
                class: "btn btn-primary",
            });
            $(".modal-footer").append(nouvelElement);


            //ferme la modale bootstrap via la variable appelant la méthode noConflict()
            //jQuery.noConflict("#exampleModal").modal('hide');

        }

    }).fail(function () {
        console.log("Erreur dans la requête Ajax");
    });
}

$()

/**
 * récupère les principales infos d'un lieu et les affiche en front via une requete ajax
 */
function affLieu(){

    //récupération de l'url de la page permettant d'afficher les infos d'un lieu via un input caché dans le HTML
    var urlInfoLieu = $("#infoLieu").val();
    //récupération du numéro du lieu sélectionné
    var idLieu = $("#sortie_lieu").val();

    //requête ajax visant à récup les principales infos du lieu sélectionné en front et de les afficher
    $.ajax(urlInfoLieu,{
        method: "post",
        data: {id: idLieu}
    }).done(function (msg) {
        var lieu = JSON.parse(msg['data']);
        $("#rue").text("Rue : " + lieu.rue);
        $("#codeP").text("Code postal : " + lieu.codeP);
        if (lieu.lat !== null){
            $("#latitude").text("Latitude : " + lieu.lat);
        }
        if(lieu.lon !== null){
            $("#longitude").text("Longitude : " + lieu.lon);
        }
    }).fail(function () {
        console.log("Erreur dans la requête Ajax");
    });
}

var Lieu = function(nom, rue, latitude, longitude, idVille){
    this.nom = nom || '';
    this.rue = rue || '';
    this.latitude = latitude || null;
    this.longitude = longitude || null;
    this.idVille = idVille || null;
}

