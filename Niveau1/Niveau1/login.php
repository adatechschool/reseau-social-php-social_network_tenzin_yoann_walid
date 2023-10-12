<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Connexion</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style 2.css" />
</head>

<body>
    <!-- <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">

            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Flux</a>
            <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mes abonnements</a></li>
            </ul>

        </nav>
    </header> -->

    
    <div id="wrapper">
    <!-- <h2>Présentation</h2> -->
    <aside>
        <p class="para">  Bienvenu sur notre blog <strong>Manga Paradise</strong> </p>
        <img class="mangas" src="image/mangas.png" alt="" >
        </aside>
        <main>
            <article>
                <h2>Connexion</h2>
                <?php
                /**
                 * TRAITEMENT DU FORMULAIRE
                 */
                // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
                // si on recoit un champs email rempli il y a une chance que ce soit un traitement
                $enCoursDeTraitement = isset($_POST['email']);
                if ($enCoursDeTraitement) {
                    // on ne fait ce qui suit que si un formulaire a été soumis.
                    // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                    // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                    echo "<pre>" . print_r($_POST, 1) . "</pre>";
                    // et complétez le code ci dessous en remplaçant les ???
                    $emailAVerifier = $_POST['email'];
                    $passwdAVerifier = $_POST['motpasse'];


                    //Etape 3 : Ouvrir une connexion avec la base de donnée.
                    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
                    //Etape 4 : Petite sécurité
                    // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                    $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                    $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                    // on crypte le mot de passe pour éviter d'exposer notre utilisatrice en cas d'intrusion dans nos systèmes
                    $passwdAVerifier = md5($passwdAVerifier);
                    // NB: md5 est pédagogique mais n'est pas recommandée pour une vraies sécurité
                    //Etape 5 : construction de la requete
                    $lInstructionSql = "SELECT * "
                        . "FROM users "
                        . "WHERE "
                        . "email LIKE '" . $emailAVerifier . "'";
                    // Etape 6: Vérification de l'utilisateur
                    $res = $mysqli->query($lInstructionSql);
                    $user = $res->fetch_assoc();
                    if (!$user or $user["password"] != $passwdAVerifier) {
                        echo "La connexion a échouée. ";
                    } else {
                        echo "Votre connexion est un succès : " . $user['alias'] . ".";
                        // Etape 7 : Se souvenir que l'utilisateur s'est connecté pour la suite
                        // documentation: https://www.php.net/manual/fr/session.examples.basic.php
                        $_SESSION['connected_id'] = $user['id'];
                    }
                }
                ?>
                <form action="authentifier.php" method="post">
                    <input type='hidden' name='user_id' value='<?php echo $_POST['auteur'] ?>'>
                    <dl>
                        <dt><label for='email'>E-Mail</label></dt>
                        <dd><input type='email' name='email' required></dd>
                        <dt><label for='motpasse'>Mot de passe</label></dt>
                        <dd><input type='password' name='motpasse' required></dd>
                    </dl>
                    <input type='submit' value="se connecter">
                </form>
                <p>
                    Pas de compte?
                    <a href='registration.php'>Inscrivez-vous.</a>
                </p>

            </article>
        </main>
    </div>
    <div class="image">

<img src="image/image1.webp" class="cube1" alt="">
<img src="image/image1.webp" class="cube2" alt="">
<img src="image/image3.webp" class="carre" alt="">
<img src="image/image2.webp" class="carre2" alt="">
<img src="image/image2.webp" class="carre3" alt="">
</div>
</body>

</html>