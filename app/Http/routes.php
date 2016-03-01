<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/{adjective}/{name}', function ($adjective, $name)  {
    
    $apiKey = getenv('THESAURUS_KEY');
    
    $cacheTtl = 0;
    
    $url = sprintf("http://words.bighugelabs.com/api/2/%s/%s/json",
                    $apiKey,
                    urlencode($adjective));
    
    $result = json_decode(file_get_contents($url));
    $synonyms = Cache::remember($adjective, 
                                $cacheTtl, 
                                function() use ($adjective, $apiKey) {
                                $url = sprintf("http://words.bighugelabs.com/api/2/%s/%s/json",
                                $apiKey,
                                urlencode($adjective));
                                
                                $result = json_decode(file_get_contents($url));
                                $synonyms = $result->adjective->syn;
                                
                                return $synonyms;
                                });
                                
    
    $synonym = $synonyms[array_rand($synonyms)];
    
    return response([
        'result' => ucwords($synonym . ' ' . $name)
    ]);
});
