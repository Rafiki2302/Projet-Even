<?php


namespace App\Service;


class ParticipantService
{
    /**
     * @author thom
     * Fonction vérifiant que pw fait au moins 8 caractères et qu'il contient au moins 1 minuscule, 1 majuscule,
     * 1 chiffre et 1 caractère spécial.
     * Pour le moment, le pw entré par l'utililsateur est seulement "trimé" avant d'être testé. L'idéal serait de refuser
     * aussi les pw contenant des espaces entre les lettres.
     * @param string|null $pw
     * @return bool
     */
    public function validatePassword(?string $pw){
        if(preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@\#\$%\^&])([^\ ])(?=.{8,})#",trim($pw))===0){
            return false;
        }
        else{
            return true;
        }
    }

    public function validatePwConfirm(?string $pw, ?string $pwConfirm){
        if($pw === $pwConfirm){
            return true;
        }
        else{
            return false;
        }
    }
}