<?php


namespace App\Service;


use App\Entity\Lieu;

class LieuService
{
    function validerLieu(Lieu $lieu){
        $messErreur = [];
        //test si le nom est vide
        if($lieu->getNom() === ''){
            $messErreur["errNom"] = "Le champ nom doit être rempli !";
        }
        //test si le nom respecte le nb de char max en bdd
        if(strlen($lieu->getNom()) > 50){
            $messErreur["errNom"] = "Le nom ne peut pas faire plus de 50 caractères";
        }
        //test si seulement un des champs lat on long est rempli
        if($lieu->getLatitude() !== null && $lieu->getLongitude() === null){
            $messErreur["errLong"] = "Il faut aussi remplir le champ longitude";
        }
        if($lieu->getLongitude() !== null && $lieu->getLatitude() === null){
            $messErreur["errLat"] = "Il faut aussi remplir le champ latitude";
        }
        //test si rue est vide, erreur si lat et long sont vides aussi
        if($lieu->getRue() === ''){
            if($lieu->getLatitude() === null || $lieu->getLongitude() === null){
                $messErreur["errRueLatLong"] = "Il faut remplir soit le champ rue, soit les champs latitude et longitude";
            }
        }
        //test si lat et long sont compris entre un certaine fourchette (et ne sont pas nuls, sinon on empeche la création
        //d'un lieu sans préciser la lat et la long)
        if((!is_float($lieu->getLongitude()) || $lieu->getLongitude() < -180 || $lieu->getLongitude() > 180) && $lieu->getLongitude() !== null){
            $messErreur["errLong"] = "La longitude doit être comprise entre -180 et 180";
        }
        if((!is_float($lieu->getLatitude()) || $lieu->getLatitude() < -90 || $lieu->getLatitude() > 90) && $lieu->getLatitude() !== null){
            $messErreur["errLat"] = "La latitude doit être comprise entre -90 et 90";
        }
        return $messErreur;
    }
}