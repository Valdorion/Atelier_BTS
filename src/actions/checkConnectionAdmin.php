<?php
//vérifie si l'utilisateur est connecté, si non, il est redirigé vers la page login puis vers la page à laquelle il avait tenté d'accéder
include_once "../src/actions/security_token.php";
if (isset($_SESSION["user_connect"]) == false or $_SESSION["user_connect"] == false or isset($_SESSION["username"]) == false) {

    if (isset($redirect)){
        $_SESSION["redirect"] = $redirect;
    }
    header("Location: login.php");
}