<?php

namespace App\Services;

use App\Enums;

$file_services = [
    'handle_promotion_image' => function(array $file): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Chemin absolu vers public/assets/images/uploads
        $baseDir = realpath(__DIR__ . '/../../public/assets/images/uploads');
        if ($baseDir === false) {
            throw new \RuntimeException("Répertoire de base introuvable");
        }

        $uploadDir = $baseDir . '/promotions';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new \RuntimeException("Impossible de créer le dossier $uploadDir");
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target   = $uploadDir . '/' . $filename;

        return move_uploaded_file($file['tmp_name'], $target)
            ? $filename
            : null;
    }
];
return $file_services;