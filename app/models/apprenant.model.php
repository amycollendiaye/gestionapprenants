<?php 
namespace App\Models;
require_once __DIR__.'/model.php';
require_once __DIR__.'/auth.model.php';
require_once __DIR__.'/promotion.model.php';
require_once __DIR__.'/referentiel.model.php';
require_once __DIR__ . '/../enums/path.enum.php';

use App\Enums;
use App\Enums\Status;
$modelapprenant = [
    'get_all_apprenants' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['apprenants'] ?? [];
    },
    
    'get_apprenants_by_promotion' => function ($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par promotion
        $apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($promotion_id) {
            return $apprenant['promotion_id'] === $promotion_id;
        });
        
        return array_values($apprenants);
    },
    
    'get_apprenant_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par ID
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($id) {
            return $apprenant['id'] === $id;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'get_apprenant_by_matricule' => function ($matricule) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par matricule
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($matricule) {
            return $apprenant['matricule'] === $matricule;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'generate_matricule' => function () use (&$model) {
        $data = $model['read_data']();
        $year = date('Y');
        $count = count($data['apprenants'] ?? []) + 1;
        
        return 'ODC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    },
    
  
    
   
    'create_apprenant' => function ($apprenant_data) use (&$model) {    
    $data = $model['read_data']();
    $new_apprenant=[
        'id' => $model['generate_id'](),
        'nom' => $apprenant_data['nom'],
        'prenom' => $apprenant_data['prenom'],
        'email' => $apprenant_data['email'],
        'image' => $apprenant_data['image'],
       'status'=>'retenu',
       "motpassee"=>"SONATEL123",
        'telephone' => $apprenant_data['telephone'],
        'referentiel' => $apprenant_data['referentiel'],
        'date_naissance' => $apprenant_data['date_naissance'],
    'lieu_naissance' => $apprenant_data['lieu_naissance'],
        'adresse' => $apprenant_data['adresse'],
        'tuteur'=>[
            'nomprenom' => $apprenant_data['tuteur']['nomprenom'],
            'telephone' => $apprenant_data['tuteur']['telephone'],
   'lienparente' => $apprenant_data['tuteur']['lienparente'],
   'adresse' => $apprenant_data['tuteur']['adresse'],
        ]
      
        
        ];
    $data['apprenants'][] = $new_apprenant;
    if ($model['write_data']($data)) {
        return $new_apprenant;
    }
    return null;



},

];
return $modelapprenant;

?>