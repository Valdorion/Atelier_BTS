<?php
$title = "Données formulaire";
$id = filter_input(INPUT_GET, 'id');
$search = filter_input(INPUT_GET, 'search');
if (isset($id)) {//page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
    $redirect = basename(__FILE__) . "?id=" . $id;
    if (isset($search)){
        $redirect = $redirect."&search=$search";
    }
}
include_once "../src/actions/checkConnectionAdmin.php";
include_once "../src/actions/function.php";
include_once "../src/layout/headerAdmin.php";
include_once "../src/config.php";
include_once "../src/actions/database-connection.php";


$id = filter_input(INPUT_GET, 'id');
if (isset($id)) {
    $exist = sqlCommand("SELECT count(id) FROM form WHERE id=:id", [":id" => $id], $conn)[0]['count(id)'];
    if ($exist != 1) { //si la campagne n'existe pas
        echo "<div class='container'><h1>Cette campagne n'existe pas</h1><br><a href='campaigns_list.php' class='btn btn-primary'>Liste des campagnes</a></div>"; //bouton de redirection vers la liste de redirection
    } else { //si la campagne existe
        $form_data = sqlCommand("SELECT title,start_date,end_date FROM form WHERE id=:id", [":id" => $id], $conn)[0]; //récupère les données des formulaires
        $file = $id . "-" . $form_data["title"] . ".csv";
        if (isset($search)) {
            $campaign_data = sqlCommand("SELECT civility, firstname, lastname, email, tel_mob, tel_fix, type, comp_name, people_num, news, score, id_category FROM form_data WHERE id_form=:id AND (
firstname LIKE :search OR lastname LIKE :search OR email LIKE :search OR tel_fix LIKE :search OR tel_mob LIKE :search)", [":id" => $id, ":search" => "%" . $search . "%"], $conn);
        } else {
            $campaign_data = sqlCommand("SELECT civility, firstname, lastname, email, tel_mob, tel_fix, type, comp_name, people_num, news, score, id_category FROM form_data WHERE id_form=:id", [":id" => $id], $conn);
        }
        ?>
        <div class="container">
            <div class="z-flex mb-4 mt-5">
                <a href="./export.php?id=<?= dataDBSafe($id); ?>" class="btn btn-success"><span
                            class="fad fa-download"></span> Télécharger les données</a>
                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#modalLink"><span class="fad fa-link"></span> Lien du formulaire
                </button>
            </div>

            <?php searchData('Donnée du formulaire "' . $form_data["title"] . '"', $search, "campaign_data.php", "campaign_data.php?id=" . dataDBSafe($id), dataDBSafe($id)) ?>

            <table class="table table-striped"><!-- tableau avec les données-->
                <thead>
                <tr>
                    <th scope="col">Genre</th>
                    <th scope="col">Prénom</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Fixe</th>
                    <th scope="col">Nom de l'entreprise</th>
                    <th scope="col">Secteur d'activité</th>
                    <th scope="col">Nbr de personnes</th>
                    <th scope="col">Newsletter</th>
                    <th scope="col">Score</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($campaign_data) == 0) {
                    ?>
                    <tr>
                        <th colspan="11" class="text-center">Aucune donnée</th>
                    </tr>
                    <?php
                } else {
                    foreach ($campaign_data as $data) { //ajoute une ligne au tableau pour chaque donnée du formulaire
                        ?>
                        <tr>
                            <td class="table-list"><?php
                                switch ($data["civility"]) {
                                    case 0:
                                        echo "Homme";
                                        break;
                                    case 1:
                                        echo "Femme";
                                        break;
                                    case 2:
                                        echo "Autre";
                                        break;
                                }
                                ?></td>
                            <td class="table-list"><?= dataDBSafe($data["firstname"]) ?></td>
                            <td class="table-list"><?= dataDBSafe($data["lastname"]) ?></td>
                            <td><?= ($data["email"]==null)?"/":dataDBSafe($data["email"]) ?></td>
                            <td><?= ($data["tel_mob"]==null)?"/":dataDBSafe($data["tel_mob"]) ?></td>
                            <td><?= ($data["tel_fix"]==null)?"/":dataDBSafe($data["tel_fix"]) ?></td>
                            <td><?php if ($data["type"] == 1) {
                                    $sector = sqlCommand("SELECT name FROM sector WHERE id=:id", [":id" => $data["id_category"]], $conn)[0]["name"];
                                    echo dataDBSafe($data["comp_name"]) . "</td><td>" . dataDBSafe($sector);
                                } else {
                                    echo "/</td><td>/";
                                } ?></td>
                            <td><?= dataDBSafe($data["people_num"]) ?></td>
                            <td><?php if ($data["news"] == 1) {
                                    echo "Inscrit";
                                } else {
                                    echo "Non";
                                } ?></td>
                            <td><?= dataDBSafe($data["score"]) ?></td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
        </div>
        <?php modalBodyLink("modalLink", "Lien du formulaire", "light", $form_data["title"], $form_data["start_date"], $form_data["end_date"], dataDBSafe($id)); ?>
        <div class="modal fade" id="modalLink"
             data-bs-keyboard="false" tabindex="-1">
            <!-- création d'une popup pour afficher le lien d'un formulaire -->
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Lien du formulaire</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Le lien de la campagne "<?= dataDBSafe($form_data["title"]); ?>" (valide
                        du <?= date("d/m/Y", strtotime($form_data["start_date"])) ?> à 00h00
                        au <?= date("d/m/Y", strtotime($form_data["end_date"])) ?> est :
                        <input id="azerty" type="text" class="form-control" readonly
                               value="<?= url_campaign($id, 2) ?>">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                                data-bs-dismiss="modal">Retour
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    include_once "../src/layout/footer.php";

} else {
    header("Location: ./campaigns_list.php");
} ?>