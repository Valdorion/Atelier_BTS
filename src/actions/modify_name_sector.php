<?php
include_once "check_security_token.php";
$redirect = "sector.php";//page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
$message = "Suite à une erreur, le nom du secteur n'a pas pu être modifié, veuillez recommencer";//message d'erreur après la connexion de l'utilisateur s'il n'était pas encore connecté
include_once "checkConnectionData.php";

if (isset($_SESSION["user_connect"])) {

//connection à la base de donnée
    include_once "function.php";
    include_once "../config.php";
    include_once "database-connection.php";

//récupération du nouveau nom du secteur
    $new_name = filter_input(INPUT_POST, "new_name");
    $id = filter_input(INPUT_POST, "id");

    if (checkLenString($new_name, 30) && sqlCommand("SELECT count(id) FROM sector WHERE id=:id", [":id" => $id], $conn)[0][0] == 1) {
        sqlCommand("UPDATE sector SET name=:name WHERE id=:id", [":name" => $new_name, ":id" => $id], $conn,false);
        $_SESSION["error"] = false;//succès
        $_SESSION["error_message"] = "Nom du secteur modifié avec succès";
    } else {
        $_SESSION["error"] = true;//erreur
        $_SESSION["error_message"] = "Impossible de modifier le secteur, les données ne sont pas valide";
    }
    header("location: ../../admin/sector.php");//retour à la page
}