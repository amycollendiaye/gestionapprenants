<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use Exception; // Ajoutez cette ligne

// Ajouter cette fonction après les requires et avant les autres fonctions
function validate_referentiel($data) {
    global $validator_services;
    $errors = [];
    
    // Validation du nom
    if ($validator_services['is_empty']($data['name'])) {
        $errors['name'] = 'Le nom du référentiel est obligatoire';
    } else {
        global $model;
        if ($model['referentiel_name_exists']($data['name'])) {
            $errors['name'] = 'Ce nom de referentiel existe déjà';
        }
    }
    
    // Validation de la description
    if ($validator_services['is_empty']($data['description'])) {
        $errors['description'] = 'La description est obligatoire';
    }
    
    // Validation de la capacité
    if ($validator_services['is_empty']($data['capacite'])) {
        $errors['capacite'] = 'La capacité est obligatoire';
    } elseif (!is_numeric($data['capacite']) || $data['capacite'] <= 0) {
        $errors['capacite'] = 'La capacité doit être un nombre positif';
    }
    
    // Validation des sessions
    if ($validator_services['is_empty']($data['sessions'])) {
        $errors['sessions'] = 'Le nombre de sessions est obligatoire';
    } elseif (!in_array($data['sessions'], ['1', '2', '3'])) {
        $errors['sessions'] = 'Le nombre de sessions doit être entre 1 et 3';
    }
    
    // Validation de l'image si elle est présente
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors['image'] = 'Format d\'image non valide (JPG, PNG ou GIF uniquement)';
        }
        if ($_FILES['image']['size'] > $max_size) {
            $errors['image'] = 'L\'image ne doit pas dépasser 5MB';
        }
    }
    
    return $errors;
}

// Affichage de la liste des référentiels de la promotion en cours
function list_referentiels() {
    global $model, $session_services;
    
    try {
        // Vérifier l'authentification
        $user = $session_services['get_current_user']();
        if (!$user || !in_array($user['profile'], ['Admin', 'Attache'])) {
            redirect('?page=forbidden');
            return;
        }
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Si aucune promotion n'est active
        if (!$current_promotion) {
            $session_services['set_flash_message']('info', 'Aucune promotion active');
            redirect('?page=promotions');
            return;
        }
        
        // Récupération des référentiels de la promotion courante
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        // Filtrage des référentiels selon le critère de recherche
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $referentiels = array_filter($referentiels, function ($referentiel) use ($search) {
                return stripos($referentiel['name'], $search) !== false || 
                       stripos($referentiel['description'], $search) !== false;
            });
        }
        
        // Affichage de la vue
        render('admin.layout.php', 'referentiel/list.html.php', [
            'user' => $user,
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'search' => $search
        ]);
        
    } catch (Exception $e) {
        $session_services['set_flash_message']('danger', 'Une erreur est survenue');
        redirect('?page=dashboard');
    }
}

// Affichage de la liste de tous les référentiels
function list_all_referentiels() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération de tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Filtrage des référentiels selon le critère de recherche
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $referentiels = array_filter($referentiels, function ($referentiel) use ($search) {
            return stripos($referentiel['name'], $search) !== false || 
                   stripos($referentiel['description'], $search) !== false;
        });
    }
    
    // Pagination
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 6; // Nombre de référentiels par page
    
    // Calculer le nombre total de pages
    $total_items = count($referentiels);
    $total_pages = ceil($total_items / $items_per_page);
    
    // S'assurer que la page courante est valide
    $current_page = max(1, min($current_page, $total_pages));
    
    // Calculer l'offset pour la pagination
    $offset = ($current_page - 1) * $items_per_page;
    
    // Récupérer les référentiels pour la page courante
    $paginated_referentiels = array_slice(array_values($referentiels), $offset, $items_per_page);
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/list-all.html.php', [
        'user' => $user,
        'referentiels' => $paginated_referentiels,
        'search' => $search,
        'page' => $current_page,
        'pages' => $total_pages,
        'total_items' => $total_items
    ]);
}

// Affichage du formulaire d'ajout d'un référentiel
function add_referentiel_form() {
    global $model;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération de la promotion active
    $active_promotion = $model['get_current_promotion']();
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/addref.html.php', [
        'user' => $user,
        'active_promotion' => $active_promotion
    ]);
}


function add_referentiel_process() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $referentiel_data = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'capacite' => $_POST['capacite'] ?? '',
        'sessions' => $_POST['sessions'] ?? ''
    ];
    
    // Validation des données
    $errors = validate_referentiel($referentiel_data);
    
    // S'il y a des erreurs, retourner au formulaire avec les messages
    if (!empty($errors)) {
        $session_services['set_flash_message']('danger', 'Veuillez corriger les erreurs dans le formulaire');
        render('admin.layout.php', 'referentiel/addref.html.php', [
            'errors' => $errors,
            'old' => $referentiel_data
        ]);
        return;
    }
    
    // Si tout est valide, créer le référentiel
    $result = $model['create_referentiel']($referentiel_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création du référentiel');
        redirect('?page=add-referentiel');
        return;
    }
    
    $session_services['set_flash_message']('success', 'Référentiel créé avec succès');
    redirect('?page=referentiels');
}
// Affichage du formulaire d'affectation/désaffectation de référentiels à une promotion
function assign_referentiels_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_auth();
    
    // Récupération de la promotion active
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
    
    // Vérifier si la promotion a l'état "en cours"
    if (!isset($active_promotion['etat']) || $active_promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('warning', 'La gestion des référentiels n\'est possible que pour une promotion en cours.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/assign.html.php', [
        'user' => $user,
        'active_promotion' => $active_promotion,
        'all_referentiels' => $all_referentiels
    ]);
}

// Traitement de l'affectation de référentiels
function assign_referentiels_process() {

    global $model, $session_services;
    
    // Vérification des droits d'accès
    check_auth();
    
    // Récupération de la promotion active
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
    
    // Vérifier si la promotion a l'état "en cours"
    if (!isset($active_promotion['etat']) || $active_promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('warning', 'La gestion des référentiels n\'est possible que pour une promotion en cours.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des référentiels sélectionnés (ceux qui sont cochés)
    $selected_referentiels = $_POST['referentiels'] ?? [];
    
    if (empty($selected_referentiels)) {
        $session_services['set_flash_message']('warning', 'Aucun référentiel sélectionné.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Récupération des référentiels actuels de la promotion
    $current_referentiels = $active_promotion['referentiels'] ?? [];
    
    // Ajout des nouveaux référentiels à la liste existante
    $updated_referentiels = array_unique(array_merge($current_referentiels, $selected_referentiels));
    
    // Mise à jour des référentiels de la promotion
    $result = $model['assign_referentiels_to_promotion']($active_promotion['id'], $updated_referentiels);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la mise à jour des référentiels.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    $session_services['set_flash_message']('success', 'Les référentiels ont été mis à jour avec succès.');
    redirect('?page=referentiels');
}

// Traitement de la désaffectation d'un référentiel individuel
function unassign_referentiel_process() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    check_auth();
    
    // Récupération de la promotion active
    $active_promotion = $model['get_current_promotion']();
    
    if (!$active_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Vérifier si la promotion a l'état "en cours"
    if (!isset($active_promotion['etat']) || $active_promotion['etat'] !== 'en cours') {
        $session_services['set_flash_message']('warning', 'La gestion des référentiels n\'est possible que pour une promotion en cours.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération de l'ID du référentiel à désaffecter
    $referentiel_id = $_POST['referentiel_id'] ?? null;
    
    if (!$referentiel_id) {
        $session_services['set_flash_message']('danger', 'Référentiel non spécifié.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    try {
        // Récupération des référentiels actuels de la promotion
        $current_referentiels = $active_promotion['referentiels'] ?? [];
        
        // Suppression du référentiel de la liste
        $updated_referentiels = array_filter($current_referentiels, function($ref_id) use ($referentiel_id) {
            return (string)$ref_id !== (string)$referentiel_id;
        });
        
        // Mise à jour des référentiels de la promotion
        $result = $model['update_promotion_referentiels']($active_promotion['id'], array_values($updated_referentiels));
        
        if (!$result) {
            throw new Exception('Erreur lors de la désaffectation du référentiel.');
        }
        
        $session_services['set_flash_message']('success', 'Le référentiel a été désaffecté avec succès.');
        redirect('?page=assign-referentiels');
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        $session_services['set_flash_message']('danger', 'Erreur lors de la désaffectation du référentiel.');
        redirect('?page=assign-referentiels');
    }
}