
const $ = require('jquery');

$(document).ready(function ($) {
    affLieu($);
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

function enregistrerLieu(lieu){
    $.ajax("../lieu/new",{
        method: "post",
        data: {lieu: lieu}
    }).done(function (msg) {
        var lieu = JSON.parse(msg["data"]);
        $("#sortie_lieu option:selected").prop('selected',false);
        $("#sortie_lieu").append(new Option(lieu.nomLieu,lieu.idLieu,true,true));
        $("#sortie_lieu:last-child").prop('selected',true);
        affLieu();
    }).fail(function () {
        console.log("Erreur dans la requête Ajax");
    });
}

function affLieu(){
    var idRue = $("#sortie_lieu").val();

    $.ajax("../lieu/infos",{
        method: "post",
        data: {id: idRue}
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
