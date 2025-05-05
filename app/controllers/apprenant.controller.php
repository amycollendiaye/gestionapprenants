<?php

namespace App\Controllers;
require_once __DIR__ . '/controller.php';   
require_once __DIR__ . '/../models/model.php';
require_once __DIR__.'/../models/auth.model.php';
require_once __DIR__.'/../models/promotion.model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php'; // Ajout de cette ligne
require_once __DIR__ . '/../enums/messages.enum.php';
use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne
use App\Enums\Messages;


function list_apprenants(){
    global $model,$modelapprenant,$modelpromotion,$modelauth,$modelreferentiel;
    global $session_services;
    // Vérification de l'authentification   
    $user =check_auth();
    // Récupération de la liste des apprenants  
   $apprenants = $modelapprenant['get_all_apprenants']();


   $stats = $modelpromotion['get_statistics']();
    $active_promotion = $modelpromotion['get_current_promotion']();
    $ref=($modelpromotion['get_referentiels_by_promotion']($active_promotion['id']));


    render('admin.layout.php', 'apprenants/listapprenant.html.php', $tableau = [
        'user' => $user,
        'apprenants' => $apprenants,
        'stats' => $stats,
    'active_promotion' => $active_promotion,
    ]);
   
}

    function add_apprenant_form(){
        global $model,$modelapprenant,$modelpromotion,$modelauth,$modelreferentiel;
        global $session_services;

    // Vérification de l'authentification
    $user = check_auth();
    $stats = $modelpromotion['get_statistics']();
    $promotions = $modelpromotion['get_all_promotions']();
    $active_promotion = $modelpromotion['get_current_promotion']();   
    
      if (!$active_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    elseif(!isset($active_promotion['etat']) || $active_promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('warning', 'La gestion des apprenants n\'est possible que pour une promotion en cours.');
        redirect('?page=apprenants');
        return;
    }
    $all_apprenants = $modelapprenant['get_all_apprenants'];
    $ref=($modelpromotion['get_referentiels_by_promotion']($active_promotion['id']));
    render('admin.layout.php', 'apprenants/addapprenant.html.php',$tableau=[
        'user' => $user,
        'stats' => $stats,
        'active_promotion'=> $active_promotion,
        'apprenants' => $all_apprenants,
        'ref' => $ref,
    ]);
    
  

    }
   

  function add_apprenant_process() {
    global $model, $modelapprenant,$modelpromotion,$modelreferentiel,$validator_services,$session_services;
     

    $user=check_auth( );
    $apprenants=[
    'nom'=>$_POST['nom'],
    'prenom'=>$_POST['prenom'],
    'date_naissance'=>$_POST['date_naissance'],
    'lieu_naissance'=>$_POST['lieu_naissance'],
    'adresse'=>$_POST['adresse'],
    'referentiel'=>$_POST['referentiel'],
    'image'=>$_FILES['image'],
    'email'=>$_POST['email'],
    'telephone'=>$_POST['telephone'],
   
    'tuteur'=>[
        'nomprenom'=>$_POST['prenom_tuteur'],
        'telephone'=>$_POST['telephone_tuteur'],
        'lienparente'=>$_POST['lien_parente'],
        'adresse'=>$_POST['adresse_tuteur'],
    ]
];

$errors = $validator_services["validate_apprenant"]($apprenants);

if(!empty($errors)){

    $session_services['set_flash_message']('danger', 'Veuillez corriger les erreurs suivantes :');
    render('admin.layout.php','apprenants/addapprenant.html.php',[
        'errors'=>$errors,
        'user'=>$user,
        'apprenants'=>$apprenants,
        'referentiels'=>$ref,
      
    ]);
    return;

  }
 $results= $modelapprenant['create_apprenant']($apprenants);
 if($results){
    $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès');
    redirect('?page=apprenants');
 }
 else{
    $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
    redirect('?page=add_apprenant_form');
 }
}





    
  
    


















?>