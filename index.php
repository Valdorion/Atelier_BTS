<?php
$form_id = filter_input(INPUT_GET, "id");
$register = filter_input(INPUT_GET,"register");
$title = "Participation à un event";
include_once "src/actions/security_token.php";
include_once "./src/layout/header.php";
include_once "./src/config.php";
include_once "./src/actions/database-connection.php";
include_once "./src/actions/function.php";
$result = sqlCommand("SELECT * FROM form WHERE id=:form_id", [":form_id" => $form_id], $conn);
$today = date("Y-m-d");
if (empty($result) or $today < $result[0]['start_date'] or $today > $result[0]['end_date']) { //vérifie si l'évènement existe et si on est dans la date du formulaire
    die("
        <main>
            <div class='container'>
                <h1>Cet évènement n'existe pas !</h1> 
                <p>Merci de vérifier l'URL entrée ainsi que la date de début et de fin des inscriptions à l'évènement.</p>
            </div>
        </main>
        ");
}
$result = $result[0];
$sector = sqlCommand("SELECT sector.id, sector.name FROM form_sector JOIN sector ON form_sector.id_sector = sector.id WHERE form_sector.id_form = :form_id", [":form_id" => $form_id], $conn);
//récupère les données de la campagne
?>
<main>
    <section id="home-hero" style="background-color: <?php echo "#".$result['color_primary']; ?>">
        <div class="form-header" style="background: rgba(0, 0, 0, 0.5) url('./assets/img/<?php echo $result['image']; ?>') center;background-size: cover;">
            <h1>🎫 <?php echo $result['title']; ?></h1>
            <p><?= dataDBSafe($result['description']) ?></p>
            <hr>
            <h2>Organisé par 🏢 <?php echo dataDBSafe($result['organisation']); ?></h2>
        </div>
        <div class="container">
            <?php if(isset($register)==false or $register!="success"){?>
            <div class="container-form">
                <form action="src/actions/add_form_data.php" id="register" method="POST" class="form-section">
                    <h2 style="text-align: center">S'inscrire à l'évènement</h2>
                    <p>Les champs (*) sont obligatoires pour toute inscription.</p>
                    <div class="inline-form">
                        <div class="col-form" style="flex-grow: 1;"><!--champ civilité-->
                            <label for="civility-field">🧑 Civilité (*)</label>
                            <select name="civility-field"
                                    style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                    id="civility-field"
                                    class="form-control"
                                    required>
                                <option value="" selected disabled>-----</option>
                                <option value="0">M</option>
                                <option value="1">Mme</option>
                                <option value="2">Autre</option>
                            </select>
                        </div>
                        <div class="col-form" style="flex-grow: 3;"><!--champ prénom-->
                            <label for="firstname-field">🧑 Prénom (*)</label>
                            <input type="text"
                                   style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                   name="firstname-field"
                                   id="firstname-field"
                                   placeholder="Camille"
                                   class="form-control"
                                   maxlength="32"
                                   required>
                        </div>
                        <div class="col-form" style="flex-grow: 3;"><!--nom-->
                            <label for="lastname-field">🧑 Nom (*)</label>
                            <input type="text"
                                   style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                   name="lastname-field"
                                   id="lastname-field"
                                   placeholder="Dupont"
                                   class="form-control"
                                   maxlength="32"
                                   required>
                        </div>
                    </div>
                    <label for="email-field">📧 E-mail (*)</label><!--champ email-->
                    <input type="email"
                           style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                           name="email-field"
                           id="email-field"
                           placeholder="camille@dupont.fr"
                           class="form-control"
                           maxlength="50"
                           pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
                           required>
                    <div class="inline-form">
                        <div class="col-form" style="flex-grow: 1;"><!--champ téléphone mobile-->
                            <label for="mobile-field">📞 Téléphone Mobile</label>
                            <input type="tel"
                                   style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                   pattern="^0[6-7]\d{8}$|^$"
                                   name="mobile-field"
                                   id="mobile-field"
                                   placeholder="0600000000"
                                   class="form-control">
                        </div>
                        <div class="col-form" style="flex-grow: 1;"><!--champ téléphone fixe-->
                            <label for="fixe-field">📞 Téléphone Fixe</label>
                            <input type="tel"
                                   style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                   pattern="^0[1-59]\d{8}$|^$"
                                   name="fixe-field"
                                   id="fixe-field"
                                   placeholder="0200000000"
                                   class="form-control">
                        </div>
                    </div>
                    <label for="peopleType-field">🏭 Entreprise ?</label><!--champ pour savoir si il s'agit d'un particulier ou d'une entreprise-->
                    <input type="checkbox"
                           name="peopleType-field"
                           id="peopleType-field"
                           onchange="peopleType()">
                    <div class="inline-form">
                        <div class="col-form" style="flex-grow: 1;"><!--champ du secteur d'activité de l'entreprise-->
                            <label for="sector-field" id="sector-label" class="hidden" hidden>🎯 Secteur (*)</label>
                            <select name="sector-field"
                                    style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                    id="sector-field"
                                    hidden>
                                <option value="" selected disabled>Choisir un secteur d'activité</option>
                                <?php
                                foreach ($sector as $s) {
                                    ?>
                                    <option value="<?= $s['id'] ?>"><?= dataDBSafe($s['name']) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-form" style="flex-grow: 2;"><!--champ du nom de l'entreprise-->
                            <label for="compagny-field" id="compagny-label" class="hidden" hidden>🏭 Nom de l'entreprise
                                (*)</label>
                            <input type="text"
                                   maxlength="32"
                                   style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                                   name="compagny-field"
                                   id="compagny-field"
                                   placeholder="Raminagrobis"
                                   hidden>
                        </div>

                    </div><!--champ nombre d'inscrit-->
                    <label for="number-field">🧑 Nombre de personnes venant à l'évènement (*)</label>
                    <input type="number"
                           value="1"
                           name="number-field"
                           style="border-bottom: 1px solid <?php echo "#" . dataDBSafe($result['color_secondary']); ?>"
                           id="number-field"
                           class="form-control"
                           min="1"
                           max="999"
                           required>
                    <div class="inline-form">
                        <div class="col-form" style="flex-grow: 1;"><!--checkbox RGPD-->
                            <label for="rgpd-field">RGPD ? (*)</label>
                            <input type="checkbox"
                                   name="rgpd-field"
                                   id="rgpd-field"
                                   required>
                        </div>
                        <div class="col-form" style="flex-grow: 1;"><!--checkbox newsletter-->
                            <label for="news-field">Newsletter ?</label>
                            <input type="checkbox"
                                   name="news-field"
                                   id="news-field">
                        </div>
                        <input type="hidden" name="id" value="<?= $form_id ?>">
                        <input type="hidden" name="token" value="<?= $token ?>">
                    </div>
                    <div class="button">
                        <button type="submit"
                                class="btn"
                                style="background-color: <?php echo "#" . $result['color_primary']; ?>; border-color: <?php echo "#" . $result['color_secondary']; ?>">
                            Je m'inscrit à l'évènement
                        </button>
                    </div>
                    <?php if(isset($_SESSION["error"])){
                       echo "<span style='color: red'>".$_SESSION['error_message']."</span>";
                       unset($_SESSION["error_message"]);
                       unset($_SESSION["error"]);
                    }?>
                </form>
            </div>
            <?php } else { //affichage si l'inscription a été effectué?>

            <h3 style="color: black">Vous vous êtes enregistrés avec succès</h3>
            <div class="button">
                <a style="background-color: <?php echo "#" . $result['color_primary']; ?>; border-color: <?php echo "#" . $result['color_secondary']; ?>"
                   class="btn" href="./?id=<?= $form_id ?>">
                    Retour au formulaire d'inscription
                </a>

            <?php } ?>
        </div>
    </section>
</main>
<?php if(isset($register)==false or $register!="success"){?>
    <script>
    function peopleType() {
        let form_field = [
            document.getElementById("sector-field"),
            document.getElementById("sector-label"),
            document.getElementById("compagny-field"),
            document.getElementById("compagny-label")
        ]
        if (document.getElementById("peopleType-field").checked) {
            for (const field in form_field) {
                form_field[field].hidden = false;
                form_field[field].setAttribute("class", "");
                if (field % 2 === 0) {
                    form_field[field].setAttribute("class", "form-control");
                    form_field[field].required = true;
                }
            }
        } else {
            for (const field in form_field) {
                form_field[field].hidden = true;
                form_field[field].setAttribute("class", "hidden");
                if (field % 2 === 0) {
                    form_field[field].required = false;
                }
            }
        }
    }
</script>
<?php }
include "./src/layout/footer.php";
?>
