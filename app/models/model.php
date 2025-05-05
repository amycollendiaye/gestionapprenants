<?php

namespace App\Models;
require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/profile.enum.php';
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne

$model = [
    // Fonctions de base pour manipuler les données
    'read_data' => function () {
        if (!file_exists(Enums\DATA_PATH)) {
            // Si le fichier n'existe pas, on renvoie une structure par défaut
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        $json_data = file_get_contents(Enums\DATA_PATH);
        $data = json_decode($json_data, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // En cas d'erreur de décodage JSON
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        return $data;
    },
    //ecriture des données
    'write_data' => function ($data) {
        // Assurez-vous que le chemin est correct
        $file_path =  __DIR__ . '/../data/data.json';;
        
        // Encodez les données en JSON avec des options pour une meilleure lisibilité
        $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Écrivez les données dans le fichier
        $result = file_put_contents($file_path, $json_data);
        
        // Vérifiez si l'écriture a réussi
        if ($result === false) {
            error_log("Erreur lors de l'écriture des données dans le fichier: $file_path");
            return false;
        }
        
        return true;
    },
    
    'generate_id' => function () {
        return uniqid();
    },
    
  
    
    'get_all_referentiels' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['referentiels'] ?? [];
    },
     

];


return $model;