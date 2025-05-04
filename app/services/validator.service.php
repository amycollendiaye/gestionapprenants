<?php

namespace App\Services;
require_once __DIR__. '/file.service.php';

// Regroupement des fonctions de validation
$validator_services = [
    'is_empty' => function ($value) {
        return empty(trim(($value)));
    },
    
    'min_length' => function ($value, $min) {
        return strlen(($value)) >= $min;
    },
    
    'max_length' => function ($value, $max) {
        return strlen(trim($value)) <= $max;
    },
    
    'is_email' => function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    },
    
    'is_valid_image' => function ($file) {
        // Vérifier si le fichier est une image valide (JPG ou PNG) et sa taille
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB en octets
        
        $file_info = getimagesize($file['tmp_name']);
        $file_type = $file_info ? $file_info['mime'] : '';
        
        return in_array($file_type, $allowed_types) && $file['size'] <= $max_size;
    },
     'validateTelephone'=> function($telephone) {
        // Supprimer tous les caractères non numériques (espaces, tirets, parenthèses, etc.)
        $numeroNettoye = preg_replace('/\D/', '', $telephone);
        
        // Vérifier si le numéro a exactement 9 chiffres
        if (strlen($numeroNettoye) !== 9) {
            return false;
        }
        
        // Vérifier si le numéro commence par 70, 75, 76, 77 ou 78
        $deuxPremiersChiffres = substr($numeroNettoye, 0, 2);
        $prefixesValides = ['70', '75', '76', '77', '78'];
        
        return in_array($deuxPremiersChiffres, $prefixesValides);
    },
    'validate_form' => function ($data, $rules) {
        $errors = [];
        
        $validate_rule = function($field, $rule, $rule_value, $data, &$errors) {
            $result = match($rule) {
                'required' => $rule_value && empty(trim($data[$field])) 
                    ? ["Le champ est obligatoire"] : [],
                'min_length' => !empty($data[$field]) && strlen(trim($data[$field])) < $rule_value 
                    ? ["Le champ doit contenir au moins $rule_value caractères"] : [],
                'max_length' => !empty($data[$field]) && strlen(trim($data[$field])) > $rule_value 
                    ? ["Le champ ne doit pas dépasser $rule_value caractères"] : [],
                'email' => $rule_value && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL) 
                    ? ["Email invalide"] : [],
                default => []
            };
            
            if (!empty($result)) {
                if (!isset($errors[$field])) {
                    $errors[$field] = [];
                }
                $errors[$field] = array_merge($errors[$field], $result);
            }
        };
        
        $process_field = function($field, $field_rules) use ($data, &$errors, $validate_rule) {
            $rule_keys = array_keys($field_rules);
            array_map(function($rule) use ($field, $field_rules, $data, &$errors, $validate_rule) {
                $validate_rule($field, $rule, $field_rules[$rule], $data, $errors);
            }, $rule_keys);
        };
        
        $fields = array_keys($rules);
        array_map(function($field) use ($rules, $process_field) {
            $process_field($field, $rules[$field]);
        }, $fields);
        
        return $errors;
    },
    
    'validate_promotion' => function(array $post_data, array $files) use ($file_services) : array  {
        $errors = [];
        
        // Validation du nom (obligatoire et unique)
        if (empty($post_data['name'])) {
            $errors['name']='Le nom de la promotion est requis pour la création' ;
        } else {
            global $model ,$modelpromotion;
            if ($modelpromotion['promotion_name_exists']($post_data['name'])) {
                $errors['name'] = 'Ce nom de promotion existe déjà';
            }
        }
        
        // Validation des dates (obligatoires)
      
        // Validation de la date de début (obligatoire et valide)
        if (empty(trim($post_data['date_debut']))) {
            $errors['date_debut'] = "La date de début est requise.";
        } elseif (!strtotime($post_data['date_debut'])) {
            $errors['date_debut'] = "La date de début est invalide.";
        }

        // Validation de la date de fin (obligatoire et valide)
        if (empty(trim($post_data['date_fin']))) {
            $errors['date_fin'] = "La date de fin est requise.";
        } elseif (!strtotime($post_data['date_fin'])) {
            $errors['date_fin'] = "La date de fin est invalide.";
        }

        // Vérification de la chronologie des dates
        if (!empty($post_data['date_debut']) && !empty($post_data['date_fin'])) {
            if (strtotime($post_data['date_debut']) > strtotime($post_data['date_fin'])) {
                $errors['date_fin'] = "La date de fin doit être postérieure à la date de début.";
            }
        }

        //verifier le format de la date JJ/MM/AAAA
        if (!empty($post_data['date_debut']) && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $post_data['date_debut'])) {
            $errors['date_debut'] = "Le format de la date de début est invalide. Utilisez le format JJ/MM/AAAA.";
        }
        if (!empty($post_data['date_fin']) && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $post_data['date_fin'])) {
            $errors['date_fin'] = "Le format de la date de fin est invalide. Utilisez le format JJ/MM/AAAA.";
        }

        
        // Validation de l'image
        if (empty($files['image']['name'])) {
            $errors['image'] = 'L\'image de la promotion est requise';
        } else {

            $image =$file_services["handle_promotion_image"]($files['image']);
            
            if(!$image){
                $errors['image'] = 'Une erreur est survenue lors du téléchargement de l\'image';
            }
        }
        
        // Validation des référentiels (au moins un requis)
        if (empty($post_data['referentiels'])) {
            $errors['referentiels'] = 'Au moins un référentiel doit être sélectionné';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors  // Où $errors est un tableau associatif avec les noms des champs comme clés
        ];
    },

'validate_referentiel' => function(array $data, array $referentiels): array {
    $errors = [];

    // Validation du nom (obligatoire et unique)
    if (empty(trim($data['nom'] ?? ''))) {
        $errors['nom'] = "Le nom du référentiel est requis.";
    } else {
        foreach ($referentiels as $ref) {
            if (strtolower(trim($ref['nom'])) === strtolower(trim($data['nom']))) {
                $errors['nom'] = "Un référentiel avec ce nom existe déjà.";
                break;
            }
        }
    }

    // Validation de la description (obligatoire)
    if (empty(trim($data['description'] ?? ''))) {
        $errors['description'] = "La description est requise.";
    }

    // Validation de la capacité (obligatoire et numérique)
    if (empty($data['capacite'])) {
        $errors['capacite'] = "La capacité est requise.";
    } elseif (!is_numeric($data['capacite']) || intval($data['capacite']) <= 0) {
        $errors['capacite'] = "La capacité doit être un nombre positif.";
    }

    // Validation du nombre de sessions (obligatoire)
    if (empty($data['sessions'])) {
        $errors['sessions'] = "Le nombre de sessions est requis.";
    }

    // Validation de la photo (obligatoire et format)
    if (empty($data['image']['name'] ?? '')) {
        $errors['image'] = "La photo est requise.";
    } else {
        $ext = strtolower(pathinfo($data['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            $errors['image'] = "La photo doit être au format JPG ou PNG.";
        }
        if ($data['image']['size'] > 2 * 1024 * 1024) {
            $errors['image'] = "La photo ne doit pas dépasser 2 Mo.";
        }
    }

    return $errors;
},



 "valide_date"=>function ($date) {
    $pattern = '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([0-9]{4})$/';
    return preg_match($pattern, $date);
},
  "validate_apprenant"=>function($data) {
    global $validator_services;
    $errors = [];
    // validation nom et prenom
    if (($validator_services['is_empty']($data['nom']))) {
        $errors['nom'] = 'Le nom  est  obligatoire';
    }
    if (($validator_services['is_empty']($data['prenom']))) {
        $errors['prenom'] = 'Le prenom est  obligatoire';
    }
   
    //validation email
    if ($validator_services['is_empty']($data['email'])) {
        $errors['email'] = 'L\'email est obligatoire';
    }
    elseif (!$validator_services['is_email']($data['email'])) {
        $errors['email'] = 'L\'email n\'est pas valide';
    }
    //validation telephone
    if ($validator_services['is_empty']($data['telephone'])) {
        $errors['telephone'] = 'Le téléphone est obligatoire';
    }
    else
    {
        if (!$validator_services['validateTelephone']($data['telephone'])) {
            $errors['telephone'] = 'Le téléphone n\'est pas valide';
        }
    }
    ///validation addresse
    if ($validator_services['is_empty']($data['adresse'])) {
        $errors['adresse'] = 'L\'adresse est obligatoire';
    }
    //validation referentiel
    if ($validator_services['is_empty']($data['referentiel'])) {
        $errors['referentiel'] = 'Le référentiel est obligatoire';
    }
    //validation date de naissance
    if ($validator_services['is_empty']($data['date_naissance'])) {
        $errors['date_naissance'] = 'La date de naissance est obligatoire';
    }else{
        if(!$validator_services['valide_date']($data['date_naissance'])){
            $errors['date_naissance'] = 'La date de naissance n\'est pas valide';
        }
    }
    //validation lieu de naissance
    if ($validator_services['is_empty']($data['lieu_naissance'])) {
        $errors['lieu_naissance'] = 'Le lieu de naissance est obligatoire';
    }
    //validation image 
    if ($validator_services['is_empty']($data['image'])) {
        $errors['image'] = 'L\'image est obligatoire';
    }
    else {
        if(!$validator_services["is_valid_image"]($data['image'])){
            $errors['image'] = 'L\'image n\'est pas valide';
        }
    }
    //validation tuteur
    if ($validator_services['is_empty']($data['tuteur']["nomprenom"])) {
        $errors['nom&prenom_tuteur'] = 'Le prénom du tuteur est obligatoire';
    }
    if ($validator_services['is_empty']($data['tuteur']["telephone"])) {
        $errors['telephone_tuteur'] = 'Le téléphone du tuteur est obligatoire';
    }
    else{
        if (!$validator_services['validateTelephone']($data['tuteur']["telephone"])) {
            $errors['telephone_tuteur'] = 'Le téléphone du tuteur n\'est pas valide';
        }
    }
    if ($validator_services['is_empty']($data['tuteur']["adresse"])) {
        $errors['adresse_tuteur'] = 'L\'adresse du tuteur est obligatoire';
    }
    if ($validator_services['is_empty']($data['tuteur']["lienparente"])) {
        $errors['lien_parente'] = 'Le lien de parenté est obligatoire';
    return $errors;
}
  }

];
return $validator_services;