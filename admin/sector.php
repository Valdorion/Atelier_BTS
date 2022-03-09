<?php //page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
$search = filter_input(INPUT_GET, 'search');
if (isset($search)) {
    $redirect = basename(__FILE__)."?search=$search";
}
include_once "../src/actions/checkConnectionAdmin.php";
$title = "Modification des catégories";
include_once "../src/actions/function.php";
include_once "../src/layout/headerAdmin.php";
include_once "../src/config.php";
include_once "../src/actions/database-connection.php";

// Listing des secteurs déjà enregistrés dans la base de donnée
if (isset($search)) {
    $lines = sqlCommand("SELECT * FROM sector  WHERE name LIKE :search ORDER BY name", [":search" => "%" . $search . "%"], $conn);
} else {
    $lines = sqlCommand("SELECT * FROM sector ORDER BY name", [], $conn);
}
?>
    <section>
        <?php if (isset($_SESSION["error"])) {
            //afficher le message de l'erreur / succès
            if ($_SESSION["error"]) {
                echo "<div class='alert alert-danger'>"; //si erreur
            } else {
                echo "<div class='alert alert-success'>"; //si succès
            }
            echo $_SESSION["error_message"] . "</div>";
            unset($_SESSION["error"]);
            unset($_SESSION["error_message"]);
        } ?>
        <div class="container mt-5">
            <h1>Gestion des secteurs</h1>
            <?php if (isset($search) and $search != "") {
                echo "<h2>Résultat de la recherche '" . dataDBSafe($search) . "'</h2>";
            }

            searchInput($search, "sector.php", "sector.php");
            ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Nom</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $nbr_line = 1;
                // création du tableau en fonction du nombre de secteurs enregistrés dans la base de donnée
                if (count($lines) == 0) {
                    echo "<tr><th class='text-center py-3' colspan='3'>Aucune donnée</th></tr>";
                } else {
                    foreach ($lines as $l) {
                        ?>
                        <tr>
                            <th><?= $nbr_line ?></th>
                            <td> <!-- affichage du nom du secteur -->
                                <?= dataDBSafe($l["name"]) ?>
                            </td>
                            <td> <!-- option applicable au secteur enregistré dans la base de donnée-->
                                <div class="btn-group">
                                    <?php
                                    modalButton("<span class='fas fa-edit'></span>", "success", "modalRenameSector" . $l['id']); //bouton modifier nom secteur
                                    modalButton("<span class='fas fa-trash'></span>", "danger", "modalDeleteSector" . $l['id']); //bouton supprimer secteur
                                    ?>
                                </div>

                                <?php modalBodyFormRenameSector($l["name"],$l["id"],$token);
                                modalBodyFormDeleteSector($l["name"],$l["id"],$token);?>
                            </td>
                        </tr>
                        <?php
                        $nbr_line++;
                    }
                }
                ?>
                </tbody>
            </table>
            <hr>
            <div> <!-- création d'un nouveau secteur dans la base de donnée-->
                <h2 class="mt-5">Ajouter un secteur</h2>
                <form action="../src/actions/insert_sector.php" method="POST" class="mt-3 needs-validation" novalidate>
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input type="text" name="name" placeholder="Secteur" id="add_sector" class="form-control"
                                   maxlength="30" required> <!-- nommage du secteur -->
                            <label for="add_sector">Nom du secteur</label>
                        </div>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <button type="submit" class="btn btn-success"><span class="fad fa-plus-circle"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        <?php jsFormValidatation(); ?>
    </script>
<?php
include "../src/layout/footer.php";
?>