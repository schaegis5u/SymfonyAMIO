<?php

namespace App\Address;

class GouvApi implements AddressApiInterface
{
    const URL = "https://api-adresse.data.gouv.fr/search/";

    public function searchAddress(string $search): array
    {
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, self::URL . '?' . http_build_query(['q' => $search]));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl);

        curl_close($curl);

   

        return json_decode($result, true)?? [];
    }
}