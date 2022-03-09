<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="shortcut icon" href="../assets/img/logo-raminagrobis.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?= $title ?></title>
</head>
<?php
if (isset($class) == false) {
    $class = "";
}
if (isset($navbar) == false) {
    $navbar = true;
}
echo "<body class='$class'>";
if ($navbar){?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <img src="../assets/img/logo-raminagrobis.png" alt="" height="40px" class="me-5">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item me-3">
                    <a class="nav-link active" href="./sector.php">Secteurs d'activité</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link active" href="./campaigns_list.php">Liste des campagnes</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link active" href="./campaign.php">Créer une campagne</a>
                </li>
            </ul>
            <div class="d-flex"></div>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- TODO enlever le texte "test" et remettre la fonction php quand l'utilisateur devra forcément être connecté pour accéder aux pages admin-->
                    <p class="navbar-text fs-4 my-auto me-3"><span class="fad fa-user-circle"></span>
                        <?php echo dataDBSafe($_SESSION['username']); ?></p>
                </li>
                <li class="nav-item mt-2">
                    <form action="../src/actions/logout.php" method="post">
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <button type="submit" class="btn btn-primary"><span class="fas fa-sign-out-alt"></span> Se
                            déconnecter
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main>
<?php } ?>