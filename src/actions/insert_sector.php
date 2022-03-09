<?php
include_once "check_security_token.php";
$redirect = "sector.php";//page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
$message = "Suite à une erreur, le secteur n'a pas pu être ajouté, veuillez recommencer";//message d'erreur après la connexion de l'utilisateur s'il n'était pas encore connecté
include_once "checkConnectionData.php";

if (isset($_SESSION["user_connect"])) {

//connection à la base de donnée
    include_once "../config.php";
    include_once "database-connection.php";
    include_once "function.php";

//récupération du nom du secteur
    $name = filter_input(INPUT_POST, "name");

    if (checkLenString($name, 30)) {
        sqlCommand("INSERT INTO sector (name) VALUES (:name)", [":name" => $name], $conn,false);
        $_SESSION["error"] = false; //succès
        $_SESSION["error_message"] = "Secteur ajouté avec succès";
    } else {
        $_SESSION["error"] = true; //erreur
        $_SESSION["error_message"] = "Impossible d'ajouter ce secteur, les données ne sont pas valides";
    }
    header("location: ../../admin/sector.php");//retour à la page
}