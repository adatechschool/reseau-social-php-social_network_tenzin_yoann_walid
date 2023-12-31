<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Administration</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style 2.css" />
</head>

<body>
    <header>
        <img src="image/png-k.png" alt="Logo de notre réseau social" />
        <nav id="menu">

            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Flux</a>
            <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">Mots-clés</a>
            <a href="usurpedpost.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">message</a>
            <input id="searchbar" onkeyup="search_tag()" type="text"
            name="search" placeholder="Search tag..">
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mes abonnements</a></li>
                <li><a href="deconnexion.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Se déconnecter</a></li>
            </ul>

        </nav>
    </header>

    <?php include "config.php"; ?>

    <div id="wrapper" class='admin'>
        <aside>
            <h2>Mots-clés</h2>
            <?php
            /*
                 * Etape 2 : trouver tous les mots clés
                 */
            $laQuestionEnSql = "SELECT * FROM `tags` LIMIT 50";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérification
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
                exit();
            }

            /*
                 * Etape 3 : @todo : Afficher les mots clés en s'inspirant de ce qui a été fait dans news.php
                 * Attention à en pas oublier de modifier tag_id=321 avec l'id du mot dans le lien
                 */
            while ($tag = $lesInformations->fetch_assoc()) {
                // echo "<pre>" . print_r($tag, 1) . "</pre>";
            ?>
                <article>
                    <h3>#<?php echo $tag['label'] ?></h3>
                    <p><?php echo $tag['id'] ?></p>
                    <nav>
                        <a href="tags.php?tag_id=<?php echo $tag['id'] ?>">Messages</a>
                    </nav>
                </article>
            <?php } ?>
        </aside>
        <main>
            <h2>Utilisatrices</h2>
            <?php
            /*
                 * Etape 4 : trouver tous les mots clés
                 * PS: on note que la connexion $mysqli à la base a été faite, pas besoin de la refaire.
                 */
            $laQuestionEnSql = "SELECT * FROM `users` LIMIT 50";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérification
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
                exit();
            }

            /*
                 * Etape 5 : @todo : Afficher les utilisatrices en s'inspirant de ce qui a été fait dans news.php
                 * Attention à en pas oublier de modifier dans le lien les "user_id=123" avec l'id de l'utilisatrice
                 */
            while ($tag = $lesInformations->fetch_assoc()) {
                // echo "<pre>" . print_r($tag, 1) . "</pre>";
            ?>
                <article>
                    <h3><a href="wall.php?user_id=<?php echo $tag['id'] ?>"><?php echo $tag['alias'] ?></a></h3>
                    <p><?php echo $tag['id'] ?></p>
                    <nav>
                        <a href="wall.php?user_id=<?php echo $tag['id'] ?>">Mur</a>
                        | <a href="feed.php?user_id=<?php echo $tag['id'] ?>">Flux</a>
                        | <a href="settings.php?user_id=<?php echo $tag['id'] ?>">Paramètres</a>
                        | <a href="followers.php?user_id=<?php echo $tag['id'] ?>">Suiveurs</a>
                        | <a href="subscriptions.php?user_id=<?php echo $tag['id'] ?>">Abonnements</a>
                    </nav>
                </article>
            <?php } ?>
        </main>
    </div>
    <div class="image">

<img src="image/image1.webp" class="cube1" alt="">
<img src="image/image1.webp" class="cube2" alt="">
<img src="image/image3.webp" class="carre" alt="">
<img src="image/image2.webp" class="carre2" alt="">
<img src="image/image2.webp" class="carre3" alt="">
</div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <script src="index.js"></script>
</body>

</html>