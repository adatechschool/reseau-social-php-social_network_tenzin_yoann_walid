<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Flux</a>
            <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id'] ?>">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes abonnements</a></li>
            </ul>

        </nav>
    </header>
    <div id="wrapper">
        <?php
        /**
         * Etape 1: Le mur concerne un utilisateur en particulier
         * La première étape est donc de trouver quel est l'id de l'utilisateur
         * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
         * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
         * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
         */
        $userId = intval($_GET['user_id']);
        ?>
        <?php
        /**
         * Etape 2: se connecter à la base de donnée
         */
        $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
        ?>

        <aside>

            <?php
            /**
             * Etape 3: récupérer le nom de l'utilisateur
             */
            $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
            // echo "<pre>" . print_r($user, 1) . "</pre>";
            ?>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                    (n° <?php echo $userId ?>)
                </p> 
                <?php if ($_SESSION['connected_id'] != $userId) {
                if (isset($_POST['submit'])){
                $lInstructionSql2 = "INSERT INTO followers "
                . "(id, followed_user_id, following_user_id) "
                . "VALUES (NULL, '"
                . $userId . "', '"
                . $_SESSION['connected_id'] . "')";
                echo $lInstructionSql2;
                $result2 = $mysqli->query($lInstructionSql2);
                 } ?>
                <form method="post">
                    <input type="submit" name="submit" value="S'abonner">
                </form> 
                <?php }?>
            </section>
        </aside>
        <main>
            

            <form action=<?php echo "wall.php?user_id=" . $_SESSION['connected_id'] ?> method="post">
                <input type='hidden' name='auteur' value='<?php echo $userId; ?>'>
                    <input type="text" name="content">
                    <input type="submit" name="button">
            </form>
            <?php
            if ($_SESSION['connected_id'] == $userId) {
                $enCoursDeTraitement = isset($_POST['auteur']);
                    if ($enCoursDeTraitement) {
                    // on ne fait ce qui suit que si un formulaire a été soumis.
                    // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                    // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                    // et complétez le code ci dessous en remplaçant les ???
                    $postContent = $_POST['content'];
                    if (isset($_POST['button'])){
                        $lInstructionSql = "INSERT INTO posts "
                        . "(id, user_id, content, created, parent_id) "
                        . "VALUES (NULL, "
                        . $_SESSION['connected_id']. ", '"
                        . $postContent . "', "
                        . "NOW(), "
                        . "NULL);";
                        // echo $lInstructionSql;
                        $result = $mysqli->query($lInstructionSql);
                }
            }
            ?>
            <?php } ?>
            <?php
            /**
             * Etape 3: récupérer tous les messages de l'utilisatrice
             */
            $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
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
                        <small>♥ <?php echo $post['like_number'] ?> </small>
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
            <?php  }
            ?>
        </main>
    </div>
</body>

</html>