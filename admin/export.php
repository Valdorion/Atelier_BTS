<?php
// Récupération de l'id du formulaire souhaiter
$form_id = filter_input(INPUT_GET, "id");
if (isset($form_id)){//page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
    $redirect = "campaign_data.php?id=$form_id";
}
include_once "../src/actions/checkConnectionAdmin.php";
include_once "../src/config.php";
include_once "../src/actions/database-connection.php";
include_once "../src/actions/function.php";
// Changement du header de la page pour dire au navigateur qu'il s'agit d'un fichier csv
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=CampaignExport_'.$form_id.'.csv');
// Récupération des données
$result = sqlCommand("SELECT * FROM form_data WHERE id_form=:form_id", [":form_id" => $form_id], $conn);
// Création de la première ligne avec les en-têtes
echo "id; civility; firstname; lastname; email; tel_mob; tel_fix; type; comp_name; people_num; news; score; id_category\r\n";
// Création des lignes de données
foreach ($result as $r){
    echo $r['id'].";".clean($r['civility']).";".clean($r['firstname']).";".clean($r['lastname']).";".clean($r['email']).";".clean($r['tel_mob']).";".clean($r['tel_fix']).";".$r['type'].";".clean($r['comp_name']).";".clean($r['people_num']).";".clean($r['news']).";".clean($r['score']).";".clean($r['id_category'])."\r\n";
}