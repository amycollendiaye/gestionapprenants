    <link rel="stylesheet" href="/assets/css/apprenants/add.css">
  

    <div class="container">
        <div class="form-card">
            <h1 class="form-title">Ajout apprenant</h1>

            <form action="?page=add-apprenant-process" method="POST">
                <!-- Section Informations de l'apprenant -->
                <div class="section">
                    <div class="section-header">
                        <h2>Informations de l'apprenant</h2>
                        <span class="edit-icon">✏️</span>
                    </div>

                    <div class="section-content">
                        <div class="form-row">
                     
                            <div class="form-group">
                            <?php if (isset($errors) && $errors["prenom"]): ?>
                                <div class='error'><?= $errors["prenom"] ?></div>
                                <?php endif; ?>
                                <label for="">Prénom(s)</label>
                                <input type="text" id="prenom" name="prenom" placeholder="Seydina Mouhammad">
                            </div>

                            <div class="form-group">
                            <div class="form-group">
                                  <?php if ( isset($errors) && $errors["nom"]): ?>
                                <div class='error'><?= $errors["nom"] ?></div>
                                <?php endif; ?>
                    
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Ndiaye">
                </div>
                <?php if (isset($errors) &&  $errors["referentiel"]): ?>
                                <div class='error'><?= $errors["referentiel"] ?></div>
                                <?php endif; ?>
                                <label for="nom">Référentiel</label>
                        <select  name="referentiel" id="referentiel">
                            <?php  foreach ($ref as $referentiel) :?>
                                <option value="<?= $referentiel['name'] ?>"   name="referentiel">  <?= $referentiel["name"]; ?></option>
                                
                               <?php endforeach;?>
                            </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <?php if ( isset($errors) && $errors["date_naissance"]): ?>
                                <div class='error'><?= $errors["date_naissance"] ?></div>
                                <?php endif; ?>
                                <label for="date-naissance">Date de naissance</label>
                                <div class="input-with-icon">
                                    <input type="text" id="date-naissance" name="date_naissance" placeholder="01/03/2025">
                                    <span class="calendar-icon">📅</span>
                                </div>
                            </div>
                            <div class="form-group">
                            <?php if( isset($errors) &&  $errors["lieu_naissance"]): ?>
                                <div class='error'><?= $errors["lieu_naissance"] ?></div>
                                <?php endif; ?>
                                <label for="lieu-naissance">Lieu de naissance</label>
                                <input type="text" id="lieu-naissance" name="lieu_naissance" placeholder="Dakar">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <?php if( isset($errors) &&  $errors["adresse"]): ?>
                                <div class='error'><?= $errors["adresse"] ?></div>
                                <?php endif; ?>
                                <label for="adresse">Adresse</label>
                              
                                <input type="text" id="adresse" name="adresse" placeholder="Sicap Liberté 6 Villa 6059 Dakar, Sénégal">
                            </div>
                            <div class="form-group">
                            <?php if( isset($errors) &&  $errors["email"]): ?>
                                <div class='error'><?= $errors["email"] ?></div>
                                <?php endif; ?>
                                <label for="email">Email</label>
                                <input type="text" id="email" name="email" placeholder="amycollendiaye@gmail.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <?php if (isset($errors) &&  $errors["telephone"]): ?>
                                <div class='error'><?= $errors["telephone"] ?></div>
                                <?php endif; ?>
                          
                                <label for="telephone">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" placeholder="+221 77 453 19 36">
                            </div>
                            <div class="form-group document-upload">
                            <?php if ( isset($errors) && $errors["image"]): ?>
                                <div class='error'><?= $errors["image"] ?></div>
                                <?php endif; ?>
                                <div class="upload-box">
                                    <span class="document-icon"> Ajouter une image</span>
                                    <input type="file" name="image" id="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Informations du tuteur -->
                <div class="section">
                    <div class="section-header">
                        <h2>Informations du tuteur</h2>
                        <span class="edit-icon">✏️</span>
                    </div>
                    
                    <div class="section-content">
                        <div class="form-row">
                            <div class="form-group">
                            <?php if ( isset($errors) && $errors["nom&prenom_tuteur"]): ?>
                                <div class='error'><?= $errors["nom&prenom_tuteur"] ?></div>
                                <?php endif; ?>
                                <label for="prenom-tuteur">Nom&Prenom</label>
                                <input type="text" id="prenom-tuteur" name="prenom_tuteur" placeholder="Assane Diop">
                            </div>
                            <div class="form-group">
                            <?php if ( isset($errors) && $errors["lien_parente"]): ?>
                                <div class='error'><?= $errors["lien_parente"] ?></div>
                                <?php endif; ?>
                                <label for="lien-parente">Lien parental</label>
                                <input type="text" id="lien-parente" name="lien_parente" placeholder="Père">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <?php if ( isset($errors) && $errors["adresse_tuteur"]): ?>
                                <div class='error'><?= $errors["adresse_tuteur"] ?></div>
                                <?php endif; ?>
                                <label for="adresse-tuteur">Adresse</label>
                                <input type="text" id="adresse-tuteur" name="adresse_tuteur" placeholder="Sicap Liberté 6 Villa 6059 Dakar, Sénégal">
                            </div>
                            <div class="form-group">
                            <?php if ( isset($errors) && $errors["telephone_tuteur"]): ?>
                                <div class='error'><?= $errors["telephone_tuteur"] ?></div>
                                <?php endif; ?>
                                <label for="telephone-tuteur">Téléphone</label>
                                <input type="tel" id="telephone-tuteur" name="telephone_tuteur" placeholder="+221 77 453 19 36">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel"><a href="?page=apprenants"> Annuler</a></button>
                    <button type="submit" class="btn btn-submit">Enregistrer</button>
                </div>
                
            </form>
        </div>
    </div>
