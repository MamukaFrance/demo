<?php

namespace App\Service;

class BankService
{
    function virement($amount) {

        // Si erreur => arret code 4889
        if ($amount > 2000){
            return ['code' => "4889", "message" => "Solde insuffisant", "data"=> null];
        }

        // Si pas d'erreur
        return ['code' => "200", "message" => "Virement effectué avec succès", "data"=> 568];
    }
}
