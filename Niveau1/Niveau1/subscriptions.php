<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mes abonnements</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style 2.css" />
</head>

<body>
    <header>
        <a href='admin.php'><img src="image/png-k.png" alt="Logo de notre réseau social" /></a>

        <nav id="menu">

            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Flux</a>
            <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">Mots-clés</a>
            <a href="usurpedpost.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">message</a>

            <input id="searchbar" onkeyup="search_tag()" type="text" name="search" placeholder="Search tag..">
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
    <div id="wrapper">
        <aside>
            <a href="wall.php?user_id=8">

                <img src="Haganezuka.Hotaru.png" alt="Portrait de l'utilisatrice" />
                <section>
                    <h3>Hotaru 🈴 ㊙️</h3>
            </a>
            <p>Sur cette page vous trouverez la liste des personnes dont
                l'utilisatrice
                n° <?php echo intval($_GET['user_id']) ?>
                suit les messages
            </p>

            </section>
            <div class="image">

<img src="image/image1.webp" class="cube1" alt="">
<img src="image/image1.webp" class="cube2" alt="">
<img src="image/image3.webp" class="carre" alt="">
<img src="image/image2.webp" class="carre2" alt="">
<img src="image/image2.webp" class="carre3" alt="">
</div>
        </aside>
        <main class='contacts'>
            <?php
            // Etape 1: récupérer l'id de l'utilisateur
            $userId = intval($_GET['user_id']);
            // Etape 2: se connecter à la base de donnée
            include "config.php";
            // Etape 3: récupérer le nom de l'utilisateur
            $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Etape 4: à vous de jouer
            //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 


            // Vérification
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }

            while ($post = $lesInformations->fetch_assoc()) {

            ?>
                <article>
                    <img  src="user.jpg" alt="blason" />
                    <h3><?php echo $post['alias'] ?></h3>
                    <p><?php echo $post['id'] ?></p>
                </article>
            <?php
                // avec le <?php ci-dessus on retourne en mode php 
            } // cette accolade ferme et termine la boucle while ouverte avant.
            ?>

        </main>
    </div>

</body>

</html>