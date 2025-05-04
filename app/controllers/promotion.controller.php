<?php

namespace App\Controllers;
require_once __DIR__.'/../models/promotion.model.php';
require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php'; // Ajout de cette ligne
require_once __DIR__ . '/../enums/messages.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne
use App\Enums\Messages;

// Dans votre routeur


// Affichage de la liste des promotions
function list_promotions() {
    global $model,  $modelpromotion, $session_services ;
    

    
    // Vérification de l'authentification
    $user = check_auth();

    // Récupérer les statistiques
    $stats = $modelpromotion['get_statistics']();
    
    // Récupérer le terme de recherche depuis GET
    $search = $_GET['search'] ?? '';
    
    // Récupérer la page courante et le nombre d'éléments par page
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 5; // Modification de 6 à 8 éléments par page

    // Récupérer toutes les promotions
    $promotions = $modelpromotion['get_all_promotions']();
    
    // Filtrer les promotions si un terme de recherche est présent
    if (!empty($search)) {
        $promotions = array_filter($promotions, function($promotion) use ($search) {
            return stripos($promotion['name'], $search) !== false;
        });
    }
    
    // Calculer le nombre total de pages
    $total_items = count($promotions);
    $total_pages = ceil($total_items / $items_per_page);
    

    // S'assurer que la page pagesante est valide
    $current_page = max(1, min($current_page, $total_pages));
    
    // Calculer l'offset pour la pagination
    $offset = ($current_page - 1) * $items_per_page;
    
    // Récupérer les promotions pour la page courante
    $paginated_promotions = array_slice(array_values($promotions), $offset, $items_per_page);
    
    // Rendu de la vue avec les statistiques
    render('admin.layout.php', 'promotion/list.html.php', [
        'user' => $user,
        'promotions' => $paginated_promotions,
        'search' => $search,
        'active_menu' => 'promotions',
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'total_items' => $total_items,
        'stats' => $stats  // S'assurer que les stats sont passées ici
    ]);
}

// Affichage du formulaire d'ajout d'une promotion
function add_promotion_form() {
    global $model,$modelpromotion, $session_services;
    
  
    // Vérér si l'utilisateur est connecté
    $user = check_auth();
    
    // Récupérer les statistiques
    $stats = $modelpromotion['get_statistics']();
    
    // Récupérer tous les référentiels pour les afficher dans le formulaire
    $all_referentiels = $model['get_all_referentiels']();
    
    // Rendu de la vue - assurez-vous que le chemin est correct
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user,
        'active_menu' => 'promotions',
        'stats' => $stats,
        'all_referentiels' => $all_referentiels
    ]);
}


// Ajout d'une promotion//
function add_promotion() {
    global $model,$modelpromotion,$modelauth, $session_services, $validator_services, $file_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    
    // Vérification de la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $session_services['set_flash_message']('error', Messages::INVALID_REQUEST->value);
        redirect('?page=promotions');
        return;
    }
    
    // Traitement des référentiels sélectionnés
    if (isset($_POST['referentiels']) && is_array($_POST['referentiels'])) {
        $_POST['referentiels'] = json_encode($_POST['referentiels']);
    }
    
    // Validation des données
    $validation = $validator_services['validate_promotion']($_POST, $_FILES);
   
   
    if (!$validation['valid']) {
        // Récupérer les statistiques
        $stats = $modelpromotion['get_statistics']();
      
        // Récupérer tous les référentiels pour les afficher dans le formulaire
        $all_referentiels = $model['get_all_referentiels']();
       
        // Rendu de la vue avec les erreurs
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'active_menu' => 'promotions',
            'stats' => $stats,
            'errors' => $validation['errors'],
            'form_data' => $_POST,
            'all_referentiels' => $all_referentiels

        ]);
       

        return;
       
    }
    
    // Traitement de l'image avec le service
   $image_path = $file_services['handle_promotion_image']($_FILES['image']);
    
    
    // Préparation des données
    $promotion_data = [
        'name' => htmlspecialchars($_POST['name']),
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'image' => $image_path,
        'status' => 'inactive',
        'apprenants' => [],
        'referentiels' => isset($_POST['referentiels']) ? json_decode($_POST['referentiels'], true) : []
    ];
    
    // Création de la promotion
    $result = $modelpromotion['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_CREATE_ERROR->value);
        redirect('?page=add_promotion_form');
        return;
    }

    $session_services['set_flash_message']('success', Messages::PROMOTION_CREATED->value);
    redirect('?page=promotions');
}// Recherche des référentiels
function search_referentiels() {
    global $model;
    
    // Vérification si l'utilisateur est connecté
    check_auth();
    
    $query = $_GET['q'] ?? '';
    $referentiels = $model['search_referentiels']($query);
    
    // Retourner les résultats en JSON
    header('Content-Type: application/json');
    echo json_encode(array_values($referentiels));
    exit;
}
function toggle_promotion_status() {
    global $model,$modelpromotion, $session_services;
    

    // //érification de l'authentification
    check_auth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=promotions');
        return;
    }
    
    $promotion_id = filter_input(INPUT_POST, 'promotion_id', FILTER_VALIDATE_INT);
   
    if (!$promotion_id) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    $result = $modelpromotion['toggle_promotion_status']($promotion_id);
   
    if ($result) {
        $message = $result['status'] === Status::ACTIVE->value ? 
                  Messages::PROMOTION_ACTIVATED->value : 
                  Messages::PROMOTION_INACTIVE->value;
        $session_services['set_flash_message']('success', $message);
    } else {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
    }
    
    redirect('?page=promotions');
}
