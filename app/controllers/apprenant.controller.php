<?php

namespace App\Controllers;
require_once __DIR__ . '/controller.php';   
require_once __DIR__ . '/../models/model.php';
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
    global $model;
    global $session_services;
    // Vérification de l'authentification   
    $user = check_auth();

    // Récupération de la liste des apprenants  
   $apprenants = $model['get_all_apprenants']();
    render('admin.layout.php', 'apprenants/listapprenant.html.php', [
        'user' => $user,
        'apprenants' => $apprenants,
        'stats' => $stats,
    'active_promotion' => $active_promotion,
    ]);
}
    function add_apprenant_form(){
        global $model;
        global $session_services;

    // Vérification de l'authentification
    $user = check_auth();
    $stats = $model['get_statistics']();
    $active_promotion = null;
    $promotions = $model['get_all_promotions']();
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active') {
            $active_promotion = $promotion;
            break;
        }
    }
    if (!$active_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    if (!isset($active_promotion['etat']) || $active_promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('warning', 'La gestion des apprenants n\'est possible que pour une promotion en cours.');
        redirect('?page=apprenants');
        return;
    }
    $all_apprenants = $model['get_all_apprenants']();
    render('admin.layout.php', 'apprenants/addapprenant.html.php', [
        'user' => $user,
        'active_menu' => 'apprenants',
        'stats' => $stats,
        'apprenants' => $all_apprenants,
    ]);


    }

  function add_apprenant_process() {
    global $model,$validator_services,$session_services;
    

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
        
    ]);
    return;

  }
 $results= $model['create_apprenant']($apprenants);
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