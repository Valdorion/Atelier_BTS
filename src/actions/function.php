<?php

function checkLenString($valueCheck, $length_max, $length_min = 1) //vérifie la longueur de la chaîne de caractère
{
    return strlen($valueCheck) <= $length_max && strlen($valueCheck) >= $length_min;
}

function checkboxCheck($checkbox){//retourne true ou false en fonction de si la checkbox est cochée
    return (($checkbox == null)?0:1);
}


function checkInt($value,$min,$max){ //vérifie la valeur du int
    return ($value>=$min and ($value<=$max or $max==0) and gettype($value)=="integer");
}

function checkEmail($email){ //vérifie si il s'agit d'une adresse mail
    return (filter_var($email,FILTER_VALIDATE_EMAIL));
}


function checkMobil($phone){ //vérifie s'il s'agit d'un numéro d'un téléphone portable
    return (preg_match("/^0[6-7]\d{8}$|^$/",$phone)==1 or $phone==null);
}

function checkFix($phone){//vérifie s'il s'agit d'un numéro d'un téléphone fixe
    return (preg_match("/^0[1-59]\d{8}$|^$/",$phone)==1 or $phone==null);
}

function checkDateExist($date, $format = "Y-m-d") //convertit un string en date
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function scoring($data,$score){ //retourne 0 si $data=null ou false sinon retourne $score
    return (($data==null or $data==false) ? 0:$score);
}

function verifDate($date1, $date2) //vérifie les dates
{
    return checkDateExist($date1) && checkDateExist($date2) && $date1 <= $date2;
}

function getPost($args) //récupères tous les données envoyés en post portant dont le nom est entrée en paramètre
{
    $result = [];
    foreach ($args as $varName) {
        $result[$varName] = filter_input(INPUT_POST, $varName);
    }
    return $result;
}

function moveFile($file_name_post, $destinationPath, $newName, $authorized_type = ["*"]) //déplacer un fichier upload
{
    if (checkFile($file_name_post, $authorized_type) == true) {
        $extension = pathinfo(basename($_FILES[$file_name_post]["name"]), PATHINFO_EXTENSION);
        $destination = $destinationPath . $newName . "." . $extension;
        move_uploaded_file($_FILES[$file_name_post]["tmp_name"], $destination);
        return ($newName . "." . $extension);
    } else {
        return null;
    }
}


function checkFile($file_name_post, $authorized_type = ["*"]) //vérifie si un fichier upload est conforme
{
    return ($_FILES[$file_name_post]["error"] == 0 and ($authorized_type == ["*"] or in_array($_FILES[$file_name_post]["type"], $authorized_type)));
}

function nbDays($start_date, $end_date) //retourne le nombre de jour entre les deux dates
{

    $start = explode("-", $start_date);
    $end = explode("-", $end_date);

    $diff = mktime(0, 0, 0, $end[1], $end[2], $end[0]) -
        mktime(0, 0, 0, $start[1], $start[2], $start[0]);

    return (($diff / 86400));
}

function url_campaign($id, $level) //retourne l'URL de la campagne
{
    return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER["REQUEST_URI"], $level) . "/?id=" . $id);
}

function dataDBSafe($data) //sécurise un string pour éviter l'injection de code
{
    return htmlspecialchars($data, ENT_SUBSTITUTE, 'UTF-8');
}

function clean($string) //supprime les ; et remplace les espaces par des -
{
    $string = str_replace(' ', '-', $string);
    return str_replace(';', "", $string);
}

function searchInput($search, $link1, $link2, $inputIdHidden = null) //HTML pour la barre de recherche
{
    echo "<form action='$link1' method='get'>
        <div class='input-group mb-3'>
            <div class='form-floating'>
                <input type='text' class='form-control' placeholder='recherche un formulaire' name='search' id='search'>
                <label for='search'>Rechercher un formulaire</label>";
    if ($inputIdHidden != null) {
        echo "<input type='hidden' name='id' value='$inputIdHidden'>";
    }
    echo "</div><button class='btn btn-outline-secondary fs-5' type='submit'><span class='fad fa-search'></span></button>";
    if (isset($search) and $search != "") {
        echo "<a href='$link2' class='btn btn-outline-danger text-center fs-4'><span class='fad fa-times-circle text-center'></span></a>";
    }
    echo "</div></form>";
}

function searchData($title, $search, $link1, $link2, $inputHidden = null) //affichage du titre de la page + "résultat de la requête"
{
    echo "<h1>" . $title . "</h1>";
    if (isset($search) and $search != "") {
        echo "<h2>Résultat de la recherche '" . dataDBSafe($search) . "'</h2>";
    }
    searchInput($search, $link1, $link2, $inputHidden);
}

function jsFormValidatation() //javascript utilisé pour la validation des formulaires
{
    echo "(function () {
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
            .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                            event.stopPropagation()
                        }
                form.classList.add('was-validated')
                    }, false)
                })
        })()";
}

function modalButton($text, $color, $target) //bouton pour afficher une popup
{
    echo "<button type = 'button' class='btn btn-$color' data-bs-toggle='modal' data-bs-target = '#$target'>$text</button>";
}

function modalTop($id) //haut de l'HTML d'une popup
{
    echo "<div class='modal fade' id='$id' data-bs-keyboard='false' tabindex='-1' data-bs-backdrop='static'>
    <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>";
}

function modalBottom() //bas de l'HTML d'une popup
{
    echo "</div></div></div>";
}

function modalBodyFormRenameSector($sector_name, $id, $token) //corps de la popup pour renommer un secteur
{
    modalTop("modalRenameSector" . $id);
    echo "<form action='../src/actions/modify_name_sector.php' class='needs-validation' method='post' novalidate>
        <div class='modal-header'>
            <h5 class='modal-title'>Modifier le nom du secteur \"" . dataDBSafe($sector_name) . "\"</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
        </div>
        <div class='modal-body'>
            <div class='form-floating'>
                <input type='text' placeholder='Nom du secteur' name='new_name'
                       id='sector_$id' class='form-control'
                       maxlength='30' required>
                <label for='sector_$id'>Nouveau nom</label>
            </div>
            <input type='hidden' name='token' value='$token'>
            <input type='hidden' name='id' value='$id'>
        </div>
        <div class='modal-footer'>
            <button type='button' class='btn btn-danger'
                    data-bs-dismiss='modal'>Annuler
            </button>
            <button type='submit' class='btn btn-success'>Modifier</button>
        </div>
    </form>";
    modalBottom();
}

function modalBodyFormDeleteSector($sector_name, $id, $token) //corps de la popup pour supprimer un secteur
{
    modalTop("modalDeleteSector" . $id);
    echo "<form action='../src/actions/delete_sector.php' method='post'>
        <div class='modal-header'>
            <h5 class='modal-title'>Suppression du
                secteur \"" . dataDBSafe($sector_name) . "\"</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
        </div>
        <div class='modal-body'>
            <div class='form-floating'>
                <p class='text-danger'>Souhaitez-vous vraiment supprimer le secteur
                    \"" . dataDBSafe($sector_name) . "\" ?<br><br>
                    <span class='far fa-exclamation-triangle'></span>
                    La suppression échouera si celui-ci a été sélectionné comme l'un des secteurs d'activité
                    d'un formulaire
                    <span class='far fa-exclamation-triangle'></span></p>
            </div>
            <input type='hidden' name='token' value='$token'>
            <input type='hidden' name='id' value='$id'>
        </div>
        <div class='modal-footer'>
            <button type='button' class='btn btn-danger'
                    data-bs-dismiss='modal'>Annuler
            </button>
            <button type='submit' class='btn btn-success'>Supprimer</button>
        </div>
    </form>";
    modalBottom();
}

function modalBodyLink($modal_id, $modal_title,$color,$title, $start, $end, $id){ //corps de la popup pour afficher le lien d'une campagne
modalTop($modal_id);
echo "
    <div class='modal-header bg-$color'>
        <h5 class='modal-title'>$modal_title</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'
                aria-label='Close'></button>
    </div>
    <div class='modal-body'>
        Le lien de la campagne \"".dataDBSafe($title)."\" (valide
        du ".date("d/m/Y", strtotime($start))." à 00h00
        au ".date("d/m/Y", strtotime($end))." à 23h59) est :
        <input id='azerty' type='text' class='form-control' readonly
               value='".url_campaign($id, 2)."'>

    </div>
    <div class='modal-footer'>
        <button type='button' class='btn btn-danger'
                data-bs-dismiss='modal'>Retour
        </button>
    </div>";
    modalBottom();
    }