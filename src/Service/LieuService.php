<?php


namespace App\Service;


use App\Entity\Lieu;

class LieuService
{
    function validerLieu(Lieu $lieu){
        $messErreur = [];
        if($lieu->getNom() === ''){
            $messErreur["errNom"] = "Le champ nom doit être rempli !";
        }
        if(strlen($lieu->getNom()) > 50){
            $messErreur["errNom"] = "Le nom ne peut pas faire plus de 50 caractères";
        }
        if($lieu->getLatitude() !== null && $lieu->getLongitude() === null){
            $messErreur["errLong"] = "Il faut aussi remplir le champ longitude";
        }
        if($lieu->getLongitude() !== null && $lieu->getLatitude() === null){
            $messErreur["errLat"] = "Il faut aussi remplir le champ latitude";
        }
        if($lieu->getRue() === ''){
            if($lieu->getLatitude() === null || $lieu->getLongitude() === null){
                $messErreur["errRueLatLong"] = "Il faut remplir soit le champ rue, soit les champs latitude et longitude";
            }
        }
        if(!is_float($lieu->getLongitude()) || $lieu->getLongitude() < -180 || $lieu->getLongitude() > 180){
            $messErreur["errLong"] = "La longitude doit être comprise entre -180 et 180";
        }
        if(!is_float($lieu->getLatitude()) || $lieu->getLatitude() < -90 || $lieu->getLatitude() > 90){
            $messErreur["errLat"] = "La latitude doit être comprise entre -90 et 90";
        }
        return $messErreur;


    }
}