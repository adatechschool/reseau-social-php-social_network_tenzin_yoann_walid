<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Les message par mot-clé</title>
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
         * Cette page est similaire à wall.php ou feed.php 
         * mais elle porte sur les mots-clés (tags)
         */
        /**
         * Etape 1: Le mur concerne un mot-clé en particulier
         */
        $tagId = intval($_GET['tag_id']);
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
             * Etape 3: récupérer le nom du mot-clé
             */
            $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $tag = $lesInformations->fetch_assoc();
            //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
            // echo "<pre>" . print_r($tag, 1) . "</pre>";
            ?>
            <img src="Haganezuka.Hotaru.png" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Hotaru 🈴 ㊙️</h3>
                <p>Sur cette page vous trouverez les derniers messages comportant
                    le mot-clé <?php echo $tag['label'] ?>
                    (n° <?php echo $tagId ?>)
                </p>

            </section>
        </aside>
        <main>
        <div class="image">

<img src="image/image1.webp" class="cube1" alt="">
<img src="image/image1.webp" class="cube2" alt="">
<img src="image/image3.webp" class="carre" alt="">
<img src="image/image2.webp" class="carre2" alt="">
<img src="image/image2.webp" class="carre3" alt="">
</div>
            <?php
            /**
             * Etape 3: récupérer tous les messages avec un mot clé donné
             */
            $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {

                // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13'><?php echo $post['created'] ?></time>
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
            <?php } ?>


        </main>
    </div>
   <script src="index.js"></script>
</body>

</html>