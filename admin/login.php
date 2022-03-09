<?php
include_once "../src/actions/security_token.php";
$title = "Connexion";
$class = "d-flex p-2 flex-column";
$navbar = false; //cache la navbar
include "../src/layout/headerAdmin.php";
if (isset($_SESSION["error_message_connection"])){//vérifie si une erreur est survenue lors d'une tentative de connexion
    $error_message = $_SESSION["error_message_connection"];
    $error = true;
    unset($_SESSION["error_message_connection"]);

}else{
    $error=false;
}
?>
<main class="form-signin text-center mx-auto">
    <div class="card bg-light">
        <div class="card-body">
            <h1 class="h2 mb-4 fw-normal">Se connecter</h1>
            <form action="../src/actions/login.php" id="register" method="POST" class="needs-validation" novalidate>
                <div class="form-floating">
                    <input type="text"
                           class="form-control form-login"
                           id="username"
                           name="username"
                           placeholder="jean@raminagrobis.fr"
                           value=""
                           maxlength="255"
                           required>
                    <label for="login">identifiant ou adresse email</label>
                </div>
                <div class="form-floating">
                    <input type="password"
                           class="form-control form-login"
                           id="password"
                           name="password"
                           placeholder="password"
                           value=""
                           maxlength="36"
                           required>
                    <label for="password">Mot de passe</label>
                </div>
                <input type="hidden" name="token" value="<?php echo $token ?>">
                <button type="submit" class="btn btn-primary my-4 w-100 py-2">
                    <span class="fas fa-sign-in-alt"></span> Se connecter
                </button>
            </form>
            <?php if($error){ //afficher le message d'erreur s'il y a eu une erreur
                echo "<div class='alert alert-danger'><p><span class='fal fa-exclamation-triangle'></span> $error_message</p></div>";
            } ?>
        </div>
    </div>
</main>
<script>
    <?php jsFormValidatation(); ?>
</script>
<?php
include "../src/layout/footer.php";
?>
