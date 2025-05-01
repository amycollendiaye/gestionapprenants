<link rel="stylesheet" href="/assets/css/referentiels.css">

<form action="?page=add-referentiel-process" method="POST" enctype="multipart/form-data">
    <h1>Créer un nouveau referentiel</h1>
                <div class="image-upload">
                    <div class="upload-placeholder">
                    <?php if (isset($errors) && isset($errors['image'])): ?>
                    <div class="error-message"><?= $errors['image'] ?></div>
                <?php endif; ?>
                        <img src="/assets/images/placeholder.png" alt="Upload">
                        <p>Cliquez pour ajouter une photo</p>
                        <input type="file" name="image" accept="image/*" class="hidden-input">
                    </div>
                </div>

                <div class="form-group">
                <?php if (isset($errors) && isset($errors['name'])): ?>
                    <div class="error-message"><?= $errors['name'] ?></div>
                <?php endif; ?>
                    <label for="nom">Nom*</label>
                    <input type="text" id="nom" name="name" >
                </div>

                <div class="form-group">
                <?php if (isset($errors) && isset($errors['description'])): ?>
                    <div class="error-message"><?= $errors['description'] ?></div>
                <?php endif; ?>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div class="form-row">
                <?php if (isset($errors) && isset($errors['capacite'])): ?>
                    <div class="error-message"><?= $errors['capacite'] ?></div>
                <?php endif; ?>
                    <div class="form-group half">
                        <label for="capacite">Capacité*</label>
                        <input type="number" id="capacite" name="capacite" value="30" >
                    </div>
                    
                    <div class="form-group half">
                    <?php if (isset($errors) && isset($errors['sessions'])): ?>
                    <div class="error-message"><?= $errors['sessions'] ?></div>
                <?php endif; ?>                        <label for="sessions">Nombre de sessions*</label>
                        <select id="sessions" name="sessions" >
                            <option value="1">1 session</option>
                            <option value="2">2 sessions</option>
                            <option value="3">3 sessions</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="?page=referentiels" class="btn-annuler">Annuler</a>
                    <button type="submit" class="btn-creer">Créer</button>
                </div>
            </form>
           