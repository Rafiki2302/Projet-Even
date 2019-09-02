<?php


namespace App\Service;


use App\Entity\Lieu;

class LieuService
{
    function validerLieu(Lieu $lieu){
        $messErreur = '';
        if($lieu->getNom() === ''){
            $messErreur = "Le champ nom doit être rempli ! \n";

        }
        if(strlen($lieu->getNom()) > 50){
            $messErreur = "Le nom ne peut pas faire plus de 50 caractères \n";
        }

        if($lieu->getRue() === ''){
            if($lieu->getLatitude() === null || $lieu->getLongitude() === null){
                $messErreur = $messErreur."Il faut remplir soit le champ rue, soit les champs latitude et longitude";
            }
            elseif(!is_float($lieu->getLongitude()) || $lieu->getLongitude()<-180 || $lieu->getLongitude() > 180){
                $messErreur = $messErreur."La longitude doit être comprise entre -180 et 180";
            }
            elseif(!is_float($lieu->getLatitude()) || $lieu->getLatitude()<-90 || $lieu->getLatitude() > 90){
                $messErreur = $messErreur."La latitude doit être comprise entre -90 et 90";
            }
        }
        return $messErreur;


    }
}