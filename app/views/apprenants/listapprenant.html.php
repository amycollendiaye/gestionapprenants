 
    <link rel="stylesheet" href="/assets/css/apprenants/list.css">

    <!-- Header -->

    <!-- Main Content -->
    <div class="container main-content">
        <div class="title-section">
            <h1 class="title">Apprenants</h1>
          <!-- <span class="learner-count"> <?=count($apprenants)?> apprenants</span> -->
       </div>

        <div class="filters">
            <div class="search-filter">
                <input type="text" placeholder="Rechercher...">
            </div>
            <div class="filter-dropdown" placeholder="Filtrer par référentiel">
               <select name="totalreferenteil" id="" class="filter-button" placeholder="Filtrer par référentiel">
                        
                            <?php  foreach ($ref as $referentiel) :?>
                                <option value="<?= $referentiel['name'] ?>"   name="">  <?= $referentiel["name"]; ?></option>
                                
                               <?php endforeach;?>
                               </select>
                              
            </div>
            <div class="filter-dropdown">
                <select class="filter-button">
                <?php foreach($apprenants as $apprenant) :?>
                    <option value=""> <?= $apprenant['status']; ?>
                    <?php break;?>
                    </option>
                    <?php endforeach;?>
                              </select>
            </div>
        </div>

        <div class="btn-group">
            <button class="download-btn">
                <span>Télécharger la liste</span>
                <span>📥</span>
            </button>
            <button class="add-btn">
                <span>👤</span>
<a href="?page=add-apprenant">ajouter un apprenant</a>       </button>
        </div>

        <div class="tabs">
            <div class="tab active">Liste des retenues</div>
            <div class="tab">Liste d'attente</div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Matricule</th>
                        <th>Nom Complet</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Référentiel</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apprenants as $apprenant): ?>
                    <tr>

                        <td>
                            <div class="avatar">
                        <img src="/assets/images/uploads/apprenants/photoetudiant.jpg" alt="Avatar">
                        </td>
                        <td><?= $apprenant["id"]?> </td>
                        <td> <?= $apprenant["nom"], " " ,$apprenant["prenom"]?>  </td>
                        <td><?= $apprenant["adresse"]?> </td>
                        <td><?= $apprenant["telephone"]?> </td>
                        <td class="ref-web"><?= $apprenant["referentiel"]?> </td>
                        <td><span class="status-active"><?= $apprenant["status"]?></span></td>
                        <td class="action-menu">... </td>
              </tr>
              <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <div class="pagination-info">
                Apprenants/page
                <select>
                    <option>10</option>
                </select>
            </div>
            <div class="pagination-info">
                1 à 10 apprenants pour 142
            </div>
            <div class="pagination-controls">
                <button class="page-btn">←</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">...</button>
                <button class="page-btn">10</button>
                <button class="page-btn">→</button>
            </div>
        </div>
    </div>
