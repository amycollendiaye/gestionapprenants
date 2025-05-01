<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les référentiels</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tous les Référentiels</h1>
            <div class="header-actions">
                <a href="?page=referentiels" class="btn btn-back">Retour</a>
                <!-- Modifier le lien pour qu'il ouvre le modal -->
                <a href="?page=add-referentiel" class="btn btn-add">+ Ajouter un référentiel</a>
            </div>
        </div>

        <!-- Ajout d'une barre de recherche -->
        <div class="search-section">
            <form action="" method="GET" class="search-bar">
                <div class="search-icon">🔍</div>
                <input type="hidden" name="page" value="all-referentiels">
                <input type="text" 
                       name="search" 
                       placeholder="Rechercher un référentiel..." 
                       value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="search-button">Rechercher</button>
            </form>
        </div>

        <div class="cards-container">
            <?php if (!empty($referentiels)): ?>
                <?php foreach ($referentiels as $ref): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? 'assets/images/referentiels/default.jpg' ?>" 
                                 alt="<?= htmlspecialchars($ref['name']) ?>">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="card-subtitle"><?= count($ref['modules'] ?? []) ?> modules</p>
                            <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="card-avatars">
                                <?php for($i = 0; $i < min(3, count($ref['apprenants'] ?? [])); $i++): ?>
                                    <div class="avatar"></div>
                                <?php endfor; ?>
                            </div>
                            <div class="card-learners">
                                <?= count($ref['apprenants'] ?? []) ?> apprenants
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">Aucun référentiel trouvé</div>
            <?php endif; ?>
        </div>

        <!-- Ajout de la pagination -->
        <div class="pagination">
            <?php if (isset($pages) && $pages > 1): ?>
                <!-- Bouton précédent -->
                <?php if ($page > 1): ?>
                    <a href="?page=all-referentiels&page_num=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="pagination-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Précédent
                    </a>
                <?php endif; ?>

                <!-- Pages numérotées -->
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=all-referentiels&page_num=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="pagination-button <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Bouton suivant -->
                <?php if ($page < $pages): ?>
                    <a href="?page=all-referentiels&page_num=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="pagination-button next">
                        Suivant
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <style>
        /* Styles pour la pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination-button {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }
        
        .pagination-button.active {
            background-color: #f97316;
            color: white;
            border-color: #f97316;
        }
        
        .pagination-button:hover:not(.active) {
            background-color: #e9e9e9;
        }
        
        .pagination-button.prev svg,
        .pagination-button.next svg {
            margin: 0 5px;
        }
        
        /* Styles pour la barre de recherche */
        .search-section {
            margin-bottom: 20px;
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
            border-radius: 4px;
            padding: 8px 12px;
            max-width: 500px;
        }
        
        .search-icon {
            margin-right: 10px;
        }
        
        .search-bar input[type="text"] {
            flex-grow: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 5px;
        }
        
        .search-button {
            background-color: #f97316;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</body>
</html>
