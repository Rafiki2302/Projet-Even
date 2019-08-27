<?php


namespace App\Service;


class ParticipantService
{
    public function validatePassword(string $pw){
        if(preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@\#\$%\^&])(?=.{8,})#",$pw)){

        }
    }
}