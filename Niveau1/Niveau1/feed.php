<?php
session_start();
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Flux</title>
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
    <div id="wrapper">
        <?php
        /**
         * Cette page est TRES similaire à wall.php. 
         * Vous avez sensiblement à y faire la meme chose.
         * Il y a un seul point qui change c'est la requete sql.
         */
        /**
         * Etape 1: Le mur concerne un utilisateur en particulier
         */
        $userId = intval($_GET['user_id']);
        ?>
        <?php
        /**
         * Etape 2: se connecter à la base de donnée
         */

        include "config.php";
        ?>

        <aside>
            <?php
            /**
             * Etape 3: récupérer le nom de l'utilisateur
             */
            $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
            // echo "<pre>" . print_r($user, 1) . "</pre>";
            ?>
            <img src="Haganezuka.Hotaru.png" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Hotaru 🈴 ㊙️ </h3>
                <p>Sur cette page vous trouverez tous les message des utilisatrices
                    auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                    (n° <?php echo $userId ?>)
                </p>

            </section>
        </aside>
        <main>
            <?php
            /**
             * Etape 3: récupérer tous les messages des abonnements
             */
            $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             * A vous de retrouver comment faire la boucle while de parcours...
             */

            while ($post = $lesInformations->fetch_assoc()) {
                //la ligne ci-dessous doit etre supprimée mais regardez ce 
                //qu'elle affiche avant pour comprendre comment sont organisées les information dans votre 
                // echo "<pre>" . print_r($post, 1) . "</pre>";

                // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                // on vous met le pied à l'étrier avec created
                // 
                // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
            ?>
                <article>
                    <h3>
                        <time><?php echo $post['created'] ?></time>
                    </h3>
                    <address><?php echo $post['author_name'] ?></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <small>❤️ <?php echo $post['like_number'] ?> </small>
                        <?php
                        $str = $post['taglist'];
                        $delimeter = ",";
                        $parts = explode($delimeter, $str);
                        for ($i = 0; $i < count($parts); $i++) {
                            echo '<a href="#"> #' . $parts[$i] . '' . '</a>';
                        }
                        ?>
                    </footer>
                </article>
            <?php
                // avec le <?php ci-dessus on retourne en mode php 
            } // cette accolade ferme et termine la boucle while ouverte avant.
            ?>


        </main>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <script src="index.js"></script>
</body>

</html>