<div class="container">
    <div class="assign-modal">
        <div class="modal-header">
            <h1 class="modal-title">Gérer les référentiels</h1>
            <button type="button" class="close-button" onclick="window.history.back()">×</button>
        </div>

        <!-- Affichage de la promotion active -->
        <div class="promotion-active">
            <?php if (isset($active_promotion)): ?>
                <h4> PROMOTION_ACTIVE: <?= htmlspecialchars($active_promotion['name']) ?>
                <?php if (isset($active_promotion['etat'])): ?></h4>
                    <div class="promotion-etat">
                     <span class="etat-badge <?= $active_promotion['etat'] === 'en cours' ? 'en-cours' : '' ?>">
                            <?= htmlspecialchars($active_promotion['etat']) ?>
                        </span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="warning">Aucune promotion active sélectionnée</p>
            <?php endif; ?>
        </div>

        <!-- Instructions pour l'utilisateur -->
        <div class="instructions">
            <p>Pour désaffecter un référentiel, cliquez sur le bouton "Désaffecter" à côté du référentiel.</p>
        </div>

        <!-- Liste des référentiels déjà assignés avec boutons de désaffectation individuels -->
        <div class="form-field">
            <label>Référentiels assignés à la promotion</label>
            <div class="referentiels-list assigned-list">
                <?php 
                $assigned_referentiels = [];
                $unassigned_referentiels = [];
                
                if (!empty($all_referentiels)) {
                    foreach ($all_referentiels as $ref) {
                        $is_assigned = in_array($ref['id'], $active_promotion['referentiels'] ?? []);
                        if ($is_assigned) {
                            $assigned_referentiels[] = $ref;
                        } else {
                            $unassigned_referentiels[] = $ref;
                        }
                    }
                }
                ?>
                
                <?php if (!empty($assigned_referentiels)): ?>
                    <?php foreach ($assigned_referentiels as $ref): ?>
                        <div class="ref-tag">
                            <div class="ref-label assigned">
                                <?= htmlspecialchars($ref['name']) ?>
                                <form action="?page=unassign-referentiel-process" method="POST" class="unassign-form">
                                    <input type="hidden" name="referentiel_id" value="<?= $ref['id'] ?>">
                                    <button type="submit" class="btn-unassign" title="Désaffecter ce référentiel">
                                        Désaffecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="info">Aucun référentiel assigné à cette promotion</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Liste des référentiels non assignés avec formulaire d'affectation -->
        <div class="instructions">
            <p>Pour affecter de nouveaux référentiels, cochez-les dans la section "Référentiels disponibles" et cliquez sur "Affecter les référentiels sélectionnés".</p>
        </div>
        <div class="form-field">
            <label>Référentiels disponibles (non assignés)</label>
            <form action="?page=assign-referentiels-process" method="POST">
                <div class="referentiels-list unassigned-list">
                    <?php if (!empty($unassigned_referentiels)): ?>
                        <?php foreach ($unassigned_referentiels as $ref): ?>
                            <div class="ref-tag">
                                <input type="checkbox" 
                                       name="referentiels[]" 
                                       value="<?= $ref['id'] ?>" 
                                       id="ref_unassigned_<?= $ref['id'] ?>" 
                                       class="hidden-checkbox">
                                <label for="ref_unassigned_<?= $ref['id'] ?>" class="ref-label">
                                    <?= htmlspecialchars($ref['name']) ?>
                                    <span class="check-icon">✓</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-actions">
                            <button type="submit" class="btn-assign">Affecter les référentiels sélectionnés</button>
                        </div>
                    <?php else: ?>
                        <p class="info">Aucun référentiel disponible à assigner</p>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="window.history.back()">Retour</button>
        </div>
    </div>
</div>

<style>
.assign-modal {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.promotion-active {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    width: 28%
}

.promotion-etat {
    margin-top: 10px;
    font-weight: bold;
}

.etat-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    background-color: #f8f9fa;
    color: #6c757d;
}

.etat-badge.en-cours {
    color: white;
}

.instructions {
    background-color: #f8f9fa;
    border-left: 4px solid #f97316;
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 0 4px 4px 0;
}

.form-field {
    margin-bottom: 20px;
}

.form-field label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-size: 16px;
}

.referentiels-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 10px;
    margin-top: 10px;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 4px;
    background-color: #f9f9f9;
}

.assigned-list {
    background-color:rgba(238, 221, 209, 0.88);
}

.unassigned-list {
    background-color: #f5f5f5;
    border: 1px solid #e0e0e0;
}

.ref-tag {
    position: relative;
}

.ref-label {
    display: block;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    position: relative;
}

.ref-label.assigned {
    border-left: 4px solid #f97316;
    padding-right: 100px; /* Espace pour le bouton de désaffectation */
}

.hidden-checkbox:checked + .ref-label {
    background: #f97316;
    color: white;
    border-color: #f97316;
}

.check-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
}

.hidden-checkbox:checked + .ref-label .check-icon {
    opacity: 1;
}

.unassign-form {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
}

.btn-unassign {
    background: black;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    cursor: pointer;
}

.btn-unassign:hover {
    background: #f97316;
}

.form-actions {
    grid-column: 1 / -1;
    margin-top: 15px;
    text-align: right;
}

.btn-cancel, .btn-assign {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-cancel {
    background: #f5f5f5;
    border: 1px solid #ddd;
}

.btn-assign {
    background: #f97316;
    color: white;
    border: none;
}

.warning {
    color: #856404;
    background-color: #fff3cd;
    padding: 10px;
    border-radius: 4px;
}

.info {
    color: #0c5460;
    background-color: #d1ecf1;
    padding: 10px;
    border-radius: 4px;
}
</style>
