<?php
include_once "check_security_token.php";
include_once "function.php";
$data_post = getPost(["organization", "event_name", "description", "color_primary", "color_secondary", "start_date", "end_date", "campaign_id"]);

$redirect = "campaigns_list.php"; //page redirection après la connexion de l'utilisateur s'il n'était pas encore connecté
if (isset($data_post["campaign_id"])) { //message d'erreur après la connexion de l'utilisateur s'il n'était pas encore connecté
    $message = "Suite à une erreur, la campagne n'a pas pu être modifié, veuillez recommencer";
} else {
    $message = "Suite à une erreur, la campagne n'a pas pu être créée, veuillez recommencer";
}
include_once "checkConnectionData.php";
if (isset($_SESSION["user_connect"])) {
    include_once "../config.php";
    include_once "database-connection.php";

    $data_post["color_primary"] = substr($data_post["color_primary"], 1); //supprime le # des couleurs exadecimales
    $data_post["color_secondary"] = substr($data_post["color_secondary"], 1);
    $sector = [];
    $sector_id = sqlCommand("SELECT id FROM sector", [], $conn);

    foreach ($sector_id as $l) {//récupère l'état des checkbox des secteurs
        $checkbox = filter_input(INPUT_POST, "checkbox_sector_" . $l["id"]);
        if (isset($checkbox) == true) {
            $sector[] = $l["id"];
        }
    }

    function checkSector($list_sector_check, $list_sector) //vérifie si les secteurs existe
    {
        if (count($list_sector_check) == 0) {
            return false;
        }
        $list_id_sector = [];
        foreach ($list_sector as $sector_id) {
            $list_id_sector[] = $sector_id["id"];
        }
        foreach ($list_sector_check as $sector_check) {
            if (in_array($sector_check, $list_id_sector) == false) {
                return false;
            }
        }
        return true;
    }

    function checkId($id, $conn) //vérifie si l'id de la campagne existe
    {
        if ($id == null or sqlCommand("SELECT count(*) FROM form WHERE id=:id", [":id" => $id], $conn)[0][0] == 1) {
            return true;
        }
        return false;
    }

    $newCampaign = $data_post["campaign_id"] == null;
    $imagePost = $_FILES['add_file']['error'] != 4;
    $checkFileResult = checkFile("add_file", ["image/png", "image/jpg", "image/jpeg"]);//vérifie le fichier image


    if (checkLenString($data_post["organization"], 31) && checkLenString($data_post["event_name"], 127)
        && checkLenString($data_post["description"], 65535) && checkLenString($data_post["color_primary"], 6, 6)
        && checkLenString($data_post["color_secondary"], 6, 6) && verifDate($data_post["start_date"], $data_post["end_date"])
        && (($checkFileResult == true && $newCampaign == true) || ($checkFileResult == true && $imagePost == true) || ($imagePost == false && $newCampaign == false))
        && checkSector($sector, $sector_id) && checkId($data_post["campaign_id"], $conn)) {

        if ($imagePost) {//renomme l'image
            $directoryDestination = "../../assets/img/";
            $newName = date("Y-m-d-H-i-s") . "_" . $data_post["organization"] . "-" . $data_post["event_name"];
            $name = moveFile("add_file", $directoryDestination, $newName, ["image/png", "image/jpg", "image/jpeg"]);
        }

        if ($newCampaign == true) {//vérifie s'il s'agit d'une nouvelle campagne
            sqlCommand("INSERT INTO form (title, description, image, color_primary, color_secondary,
            start_date, end_date, organisation) VALUES (:title, :description, :image, :color_primary,:color_secondary, :start_date, :end_date, :organization)",
                [":title" => $data_post["event_name"], "description" => $data_post["description"], ":image" => $name,
                    ":color_primary" => $data_post["color_primary"], ":color_secondary" => $data_post["color_secondary"], ":start_date" => $data_post["start_date"],
                    ":end_date" => $data_post["end_date"], ":organization" => $data_post["organization"]], $conn, false);

            $campaign_id = sqlCommand("SELECT id FROM form ORDER BY id DESC LIMIT 1", [], $conn)[0]["id"];


            foreach ($sector as $s) {
                sqlCommand("INSERT INTO form_sector (id_form, id_sector) VALUES (:id_form, :id_sector)", ["id_form" => $campaign_id, ":id_sector" => $s], $conn, false);
            }
            $_SESSION["id_campaign"] = $campaign_id;
            $_SESSION["error_message"] = "Campagne créer avec succès";

        } else {//si c'est une modification d'un formulaire d'une campagne déjà existante
            $image = sqlCommand("SELECT image FROM form WHERE id=:campaign_id", ["campaign_id" => $data_post["campaign_id"]], $conn)[0]["image"];
            if ($imagePost == false) {
                $name = $image;
            } else {
                unlink("../../assets/img/" . $image);
            }

            sqlCommand("UPDATE form SET title = :title, description = :description, image = :image,
                    color_primary = :color_primary, color_secondary = :color_secondary,
                    start_date = :start_date, end_date = :end_date, organisation = :organization WHERE id = :campaign_id",
                [":title" => $data_post["event_name"], "description" => $data_post["description"], ":image" => $name,
                    ":color_primary" => $data_post["color_primary"], ":color_secondary" => $data_post["color_secondary"], ":start_date" => $data_post["start_date"],
                    ":end_date" => $data_post["end_date"], ":organization" => $data_post["organization"], ":campaign_id" => $data_post["campaign_id"]], $conn, false);

            $request_sector_form_db = sqlCommand("SELECT id_sector FROM form_sector WHERE id_form=:id_form", [":id_form" => $data_post["campaign_id"]], $conn);

            $sector_form_id = [];
            foreach ($request_sector_form_db as $element) {
                $id = $element['id_sector'];
                if (in_array($id, $sector)) {
                    //secteurs ajouter au formulaire
                    $sector_form_id[] = $id;
                } else {
                    //secteur à supprimer du formulaire
                    $delete_sector_form[] = $id;
                }
            }
            if (isset($delete_sector_form) == true) {
                foreach ($delete_sector_form as $value) {//supprime l'association du secteur et du formulaire
                    sqlCommand("DELETE FROM form_sector WHERE id_form=:id_form AND id_sector = :id_sector", ["id_form" => $data_post["campaign_id"], "id_sector" => $value], $conn, false);
                }
            }

            foreach ($sector as $s) {
                if (in_array($s, $sector_form_id) == false) {//créer une association entre le secteur et le formulaire
                    sqlCommand("INSERT INTO form_sector (id_form, id_sector) VALUES (:id_form, :id_sector)", ["id_form" => $data_post["campaign_id"], "id_sector" => $s], $conn, false);
                }
            }
            $_SESSION["id_campaign"] = $data_post["campaign_id"];
            $_SESSION["error_message"] = "Campagne modifier avec succès";
        }

        $_SESSION["error"] = false;
        $_SESSION["title_campaign"] = $data_post["event_name"];
        $_SESSION["start_campaign"] = $data_post["start_date"];
        $_SESSION["end_campaign"] = $data_post["end_date"];
    } else { //message d'erreur
        $_SESSION["error"] = true;
        $_SESSION["error_message"] = "Impossible de créer la campagne, les données ne sont pas valide";
    }
    header("Location: ../../admin/campaigns_list.php");
}