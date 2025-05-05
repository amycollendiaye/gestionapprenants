<?php   
namespace App\Models;
require_once __DIR__ . '/model.php';
require_once __DIR__. "/promotion.model.php";
require_once __DIR__.'/auth.model.php';
require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/profile.enum.php';
use App\Enums;
$modelreferentiel=['referentiel_name_exists' => function(string $name) use (&$model): bool {
        $data = $model['read_data']();
        
        foreach ($data['referentiels'] as $referentiel) {
            if (strtolower($referentiel['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
 
    'get_referentiel_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($id) {
            return $referentiel['id'] === $id;
        });
        
        return !empty($filtered_referentiels) ? reset($filtered_referentiels) : null;
    },
    
    'referentiel_name_exists' => function ($name, $exclude_id = null) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($name, $exclude_id) {
            return strtolower($referentiel['name']) === strtolower($name) && ($exclude_id === null || $referentiel['id'] !== $exclude_id);
        });
        
        return !empty($filtered_referentiels);
    },
    
    'create_referentiel' => function ($referentiel_data) use (&$model) {
        $data = $model['read_data']();
        
        $new_referentiel = [
            'id' => $model['generate_id'](),
            'name' => $referentiel_data['name'],
            'description' => $referentiel_data['description'],
            'image' => $referentiel_data['image'],
            'capacite' => $referentiel_data['capacite'],
            'sessions' => $referentiel_data['sessions'],
            'modules' => []
        ];
        
        $data['referentiels'][] = $new_referentiel;
        
        if ($model['write_data']($data)) {
            return $new_referentiel;
        }
        
        return null;
    },

   
    
    'assign_referentiels_to_promotion' => function ($promotion_id, $referentiel_ids) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($promotion_id) {
            return $promotion['id'] === $promotion_id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Ajouter les référentiels à la promotion
        if (!isset($data['promotions'][$promotion_index]['referentiels'])) {
            $data['promotions'][$promotion_index]['referentiels'] = [];
        }
        
        $data['promotions'][$promotion_index]['referentiels'] = array_unique(
            array_merge($data['promotions'][$promotion_index]['referentiels'], $referentiel_ids)
        );
        
        return $model['write_data']($data);
    },
    
    'search_referentiels' => function(string $query) use (&$model) {
        $referentiels = $model['get_all_referentiels']();
        if (empty($query)) {
            return $referentiels;
        }
        
        return array_filter($referentiels, function($ref) use ($query) {
            return stripos($ref['name'], $query) !== false || 
                   stripos($ref['description'], $query) !== false;
        });
    },
    'update_promotion_referentiels' => function($promotion_id, $referentiels) use (&$model) {
        try {
          
            $data = $model['read_data']();

            
            // Mettre à jour les référentiels de la promotion
            foreach ($data['promotions'] as &$promotion) {
                if ($promotion['id'] === $promotion_id) {
                    $promotion['referentiels'] = $referentiels;
                    break;
                }
            }
            
            // Sauvegarder les modifications
            return $model['write_data']($data);       
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    },
 
];
return $modelreferentiel;
?>