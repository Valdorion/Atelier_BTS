<?php
include_once "check_security_token.php";
include_once "../config.php";
include_once "database-connection.php";
include_once "function.php";


function checkSector($sector_id, $conn) //vérifie si le secteur existe
{
    return (sqlCommand("SELECT count(id) FROM sector WHERE id=:sector_id", [":sector_id" => $sector_id], $conn)[0][0] == 1);
}

function checkForm($id, $conn) //vérifie les dates du formulaire
{
    $form_data = sqlCommand("SELECT start_date,end_date FROM form WHERE id=:id", [":id" => $id], $conn)[0];
    $today = date("Y-m-d");
    if (empty($form_data) or $today < $form_data['start_date'] or $today > $form_data['end_date']) {
        return false;
    }else{
        return true;
    }
}

$data = getPost(["civility-field", "firstname-field", "lastname-field", "email-field", "mobile-field", "fixe-field", "peopleType-field", "sector-field", "compagny-field", "news-field", "rgpd-field", "id","number-field"]);

if (checkInt((int)$data["civility-field"], 0, 2) and checkLenString($data["firstname-field"], 32) and checkLenString($data["lastname-field"], 32) and checkEmail($data["email-field"]) and checkLenString($data["email-field"],50) and checkForm($data["id"],$conn) and checkInt((int)$data["number-field"],1,999) and
    checkMobil($data["mobile-field"]) and checkFix($data["fixe-field"]) and (checkboxCheck($data["peopleType-field"]==0) or (checkboxCheck($data["peopleType-field"]==1) and checkSector($data["sector-field"], $conn) and checkLenString($data["compagny-field"], 32))) and checkboxCheck("rgpd-field")==1) {
    $data["news-field"] = checkboxCheck($data["news-field"]); //transforme la valeur news-field qui est égal à null ou on en 0 ou 1
    $data["peopleType-field"] = checkboxCheck($data["peopleType-field"]);

    $score = 0;

    if ($data["peopleType-field"] == false) {
        $data["sector-field"] = null;
        $data["compagny-field"] = null;
    }
    if ($data["fixe-field"]==null){
        $data["fixe-field"]=null;
    }
    if ($data["mobile-field"]==null){
        $data["mobile-field"]=null;
    }

    $score += scoring($data["news-field"], 1); //+1 si la checkbox est cochée
    $score += scoring($data["mobile-field"], 1); //+1 si le champ du téléphone mobile est remplie
    $score += scoring($data["fixe-field"], 1); //+1 si le champ du téléphone fixe est remplie
    $score += scoring($data["peopleType-field"], 3); //+3 s'il s'agit d'une entreprise
    $score += (int)$data["number-field"]; //+1 par personne qui vient à l'événement

    sqlCommand("INSERT INTO form_data (civility, firstname, lastname, email, tel_mob, tel_fix, type, comp_name, people_num, news, score, id_form, id_category) VALUES (:civility,:firstname,:lastname,:email,:tel_mob, :tel_fix, :type, :comp_name, :people_num, :news, :score, :id_form, :id_category)",
    [":civility"=>$data["civility-field"],":firstname"=>$data["firstname-field"],":lastname"=>$data["lastname-field"],":email"=>$data["email-field"],":tel_mob"=>$data["mobile-field"], ":tel_fix"=>$data["fixe-field"], ":type"=>$data["peopleType-field"], ":comp_name"=>$data["compagny-field"], ":people_num"=>$data["number-field"], ":news"=>$data["news-field"], ":score"=>$score, ":id_form"=>$data["id"], ":id_category"=>$data["sector-field"]],$conn, false);
    header("Location: ../../?id=".$data["id"]."&register=success");
}else{
    $_SESSION["error"]=true;//afficher le message d'erreur en cas d'erreur
    $_SESSION["error_message"]="Les données envoyées ne sont pas valide, merci de remplir de nouveau le formulaire";
    header("Location: ../../?id=".$data["id"]);
}
