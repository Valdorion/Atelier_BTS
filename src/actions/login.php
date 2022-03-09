<?php
include_once "check_security_token.php";
include_once "../config.php";
include_once "database-connection.php";

$username = filter_input(INPUT_POST,'username');
$psw = filter_input(INPUT_POST,"password");
$user = sqlCommand("SELECT login FROM user WHERE (login = :username OR email=:username) AND password = :password",[':username'=>$username, ':password'=>$psw],$conn);
if (count($user)==1){ //vérifie si l'utilisateur existe et si le mdp est bon
    $_SESSION["username"] = $user[0]["login"];
    $_SESSION["user_connect"] = true;
    if (isset($_SESSION["redirect"])){
        $redirection = $_SESSION["redirect"];//vérifie si une redirection à été donnée
        unset($_SESSION["redirect"]);
    }else{//redirection par défaut
        $redirection = "campaigns_list.php";
    }
    header("location: ../../admin/".$redirection);
} else {
    $_SESSION["error_message_connection"] = "Identifiant ou mot de passe incorrect";
    header("location: ../../admin/login.php");
}