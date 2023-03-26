<?php

namespace App\Service;

class PicsumAPI {
    private $apiBase = "https://picsum.photos/";

    /**
     * Calls the API and fetches the image download_url
     */
    public function getImageDownloadUrl($imageId) {
        $response = $this->getImageInfo($imageId);

        if (!$response) 
            return ["error" => "Invalid image ID"];

        if ($response['code'] != 200) 
            return [
                "error" => "API call failure",
                "code" => $response['code'],
                "body" => $response['body']
            ]; 

        $info = json_decode($response['body'], true);
        if (!$info)
            return [
                "error" => "Could not parse the response body",
                "code" => $response['code'],
                "body" => $response['body']
            ];

        if (!isset($info['download_url']))
            return [
                "error" => "Could not find image download_url",
                "code" => $response['code'],
                "body" => $response['body']
            ];
        
        return [
            "url" => $info['download_url']
        ];

    }
    /**
     * Makes the actual call to the API and returns the response code and body
     */
    private function getImageInfo($imageId) {
        if (!$imageId) return null;

        $cURLConnection = curl_init();
        $url = $this->apiBase . "id/" . $imageId . "/info";
        // var_dump($url); exit;
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_FOLLOWLOCATION, true);
        
        $body = curl_exec($cURLConnection);
        $code = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);

        curl_close($cURLConnection);

        return ["code" => $code, "body" => $body];
    }
}