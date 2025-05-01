<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Ajouter une promotion</h1>
            <div class="header-subtitle">Créer une nouvelle promotion pour l'école</div>
        </div>
        <a href="?page=promotions" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour à la liste
        </a>
    </div>
    <div class="form-container" style="margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <div class="form-header" style="margin-bottom: 20px;">
            <h2>Créer une nouvelle promotion</h2>
        </div>
        
        <p style="margin-bottom: 20px; color: #64748b;">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</p>
        
        <form class="promotion-form" action="?page=add-promotion-process" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <?php if (isset($errors) && isset($errors['name'])): ?>
                    <div class="error-message"><?= $errors['name'] ?></div>
                <?php endif; ?>
                <label for="promotion-name">Nom de la promotion</label> <br> <br>
                <input type="text" id="promotion-name" name="name" placeholder="Ex: Promotion 2025" value="<?= htmlspecialchars($form_data['name'] ?? '') ?>">
            </div>
             <div class="form-row">
                <div class="form-group">
                    <?php if (isset($errors) && isset($errors['date_debut'])): ?>
                        <div class="error-message"><?= $errors['date_debut'] ?></div>
                    <?php endif; ?>
                    <label for="start-date">Date de début</label> <br> <br>
                    <div class="date-input-container">
                        <input type="" id="start-date" name="date_debut" value="<?= htmlspecialchars($form_data['date_debut'] ?? '') ?>">
                        <span class="calendar-icon"></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <?php if (isset($errors) && isset($errors['date_fin'])): ?>
                        <div class="error-message"><?= $errors['date_fin'] ?></div>
                    <?php endif; ?>
                    <label for="end-date">Date de fin</label> <br> <br>
                    <div class="date-input-container">
                        <input type="" id="end-date" name="date_fin" value="<?= htmlspecialchars($form_data['date_fin'] ?? '') ?>">
                        <span class="calendar-icon"></span>
                    </div>
                </div>
            </div>
            <label>Photo de la promotion</label> <br> <br>
            <div class="form-group" id="taf">
                <?php if (isset($errors) && isset($errors['image'])): ?>
                    <div class="error-message"><?= $errors['image'] ?></div>
                <?php endif; ?>
                <div class="file-upload-container">
                    <input type="file" id="promotion-image" name="image" accept="image/png,image/jpeg">
                    <p class="file-restrictions">Format JPG, PNG. Taille max 2MB</p>
                </div>
            </div>
            
            <div class="form-group">
                <?php if (isset($errors) && isset($errors['referentiels'])): ?>
                    <div class="error-message"><?= $errors['referentiels'] ?></div>
                <?php endif; ?>
                <label>Référentiels</label> <br> <br>
                
                <!-- Liste des référentiels sans recherche JavaScript -->
                <div class="referentiels-container">
                    <?php if (isset($all_referentiels) && !empty($all_referentiels)): ?>
                        <?php foreach ($all_referentiels as $ref): ?>
                            <div class="referentiel-item">
                                <input type="checkbox" 
                                       name="referentiels[]" 
                                       value="<?= $ref['id'] ?>"
                                       <?= isset($form_data['referentiels']) && in_array($ref['id'], json_decode($form_data['referentiels'], true) ?? []) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($ref['name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun référentiel disponible</p>
                    <?php endif; ?>
                </div>
            </div>            
            <div class="form-buttons">
                <a href="?page=promotions" class="cancel-button">Annuler</a>
                <button type="submit" class="submit-button">Créer la promotion</button>
            </div>
        </form>
    </div>
</div>