<?php

namespace App\Translate\fr;

   enum  form_message :string{
    case required ='Ce champ est obligatoire';
    case email = 'Veuillez saisir une adresse email valide';
    case  min_length = 'Ce champ doit contenir au moins %d caractères'; 
    case max_length ='Ce champ ne doit pas dépasser %d caractères';
    case  invalid_image  = 'Le fichier doit être une image valide (JPG ou PNG) de moins de 2MB';
   }
      enum auth : string  {
        case invalid_credentials ='Email ou mot de passe incorrect';
        case not_logged_in ='Veuillez vous connecter pour accéder à cette page';
      }
    enum  referentiel:string  {
        case name_exists ='Un référentiel avec ce nom existe déjà';
        case create_failed ='Erreur lors de la création du référentiel';
         case update_failed  = 'Erreur lors de la mise à jour du référentiel';

    }
    


