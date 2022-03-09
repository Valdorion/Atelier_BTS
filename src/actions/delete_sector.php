<?php
include_once "check_security_token.php";
$redirect = "sector.php"; //page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
$message = "Suite à une erreur, le secteur n'a pas pu être supprimé, veuillez recommencer";//message d'erreur après la connexion de l'utilisateur s'il n'était pas encore connecté
include_once "checkConnectionData.php";

if (isset($_SESSION["user_connect"])) {
//connection à la base de donnée
    include_once "../config.php";
    include_once "database-connection.php";

    $id = filter_input(INPUT_POST, "id");

    if (sqlCommand("SELECT count(id) FROM sector WHERE id=:id", [":id" => $id], $conn)[0][0] == 1) { //vérification si l'id du secteur existe
        $nbr_use = sqlCommand("SELECT count(id_sector) FROM form_sector WHERE id_sector=:id", [":id" => $id], $conn)[0][0] +
            sqlCommand("SELECT count(id) from form_data WHERE id_category=:id", [":id" => $id], $conn)[0][0]; //vérification si le secteur est utilisé par un formulaire
        if ($nbr_use == 0) { //vérifie si le secteur n'est pas utilisé, soit par un formulaire, soit comme secteur d'activité d'une entreprise
            sqlCommand("DELETE FROM sector WHERE id=:id", [":id" => $id], $conn, false);
            $_SESSION["error"] = false; //succès
            $_SESSION["error_message"] = "Secteur supprimé avec succès";
        } else {
            $_SESSION["error"] = true; //erreur
            $_SESSION["error_message"] = "Impossible de supprimer ce secteur, il est soit, utilisé comme secteur d'activité dans un formulaire, soit utilisé comme secteur d'activité pour l'entreprise d'un professionnel inscrit dans un formulaire";
        }
    } else {
        $_SESSION["error"] = true; //erreur
        $_SESSION["error_message"] = "Impossible de supprimer ce secteur, les données ne sont pas valides";
    }
    header("location: ../../admin/sector.php");//retour à la page
}
