<?php
//vérifie si l'utilisateur est connecté lors de modification de la base de donnée, si non, il est redirigé vers la page login puis vers la page du formulaire correspondant aux données qu'il avait tenté de modifier
if (isset($_SESSION["user_connect"]) == false or $_SESSION["user_connect"] == false or isset($_SESSION["username"]) == false) {
    if (isset($redirect)){
        $_SESSION["redirect"] = $redirect;
        $_SESSION["error"] = true;
        $_SESSION["error_message"] = $message;
    }
    if (isset($_SESSION["user_connect"])){
        unset($_SESSION["user_connect"]);
    }
    var_dump($_SESSION);
    header("Location: ../../admin/login.php");
}