<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style 2.css" />
</head>

<body>
    <header>
        <a href='admin.php'><img src="image/png-k.png" alt="Logo de notre réseau social" /></a>


        <nav id="menu">
            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Flux</a>
            <a href="tags.php?tag_id=<?php echo $_SESSION['connected_id'] ?>">Mots-clés</a>
            <a href="usurpedpost.php?tag_id=<?php echo $_SESSION['connected_id'] ?> ">message</a>

            <input id="searchbar" onkeyup="search_tag()" type="text" name="search" placeholder="Search tag..">
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes abonnements</a></li>
                <li><a href="deconnexion.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Se déconnecter</a></li>

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
            <img src="Haganezuka.Hotaru.png" user_id=1 wall.php?user_id=1 alt="Portrait de l'utilisatrice" />
            
            <section>
                <h3>Hotaru 🈴 ㊙️</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                    (n° <?php echo $userId ?>)
                </p>
                <?php if ($_SESSION['connected_id'] != $userId) {
                    if (isset($_POST['submit'])) {
                        $lInstructionSql2 = "INSERT INTO followers "
                            . "(id, followed_user_id, following_user_id) "
                            . "VALUES (NULL, '"
                            . $userId . "', '"
                            . $_SESSION['connected_id'] . "')";
                        // echo $lInstructionSql2;
                        $result2 = $mysqli->query($lInstructionSql2);
                    } ?>
                    <form method="post">
                        <input class="sabonner" type="submit" name="submit" value="S'abonner">


                    </form>
                <?php } ?>
            </section>
            <?php
            if (isset($_POST['valider'])) {
                include("config.php");
                $imageData = file_get_contents($_FILES['image']['tmp_name']);

                $req = $mysqli->prepare("INSERT INTO image (image) VALUES (?)");

                $req->bind_param("b", $imageData);

                if ($req->execute()) {
                    echo "Image uploadée avec succès.";
                } else {
                    echo "Erreur lors de l'insertion de l'image : " . $req->error;
                }
            }
            ?>
          
           
            <?php
            include("config.php");

            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $reg = $mysqli->prepare("SELECT * FROM image where id=? limit 1");
                $reg->bind_param("i", $id);
                $reg->execute();
                $reg->bind_result($imageData);
                if ($reg->fetch()) {
                    header("Content-type: image/jpeg");
                    echo $imageData;
                } else {
                    echo "Image non trouvée.";
                }
                echo $tab[2]["image"];
            }
           
            ?>

        </aside>
        <main>


            <form action=<?php echo "wall.php?user_id=" . $_SESSION['connected_id'] ?> method="post">
                <input type='hidden' name='auteur' value='<?php echo $userId; ?>'>
                <input class="texte" type="text" name="content" placeholder="entrez votre post">
                <input class="submit" type="submit" name="button">
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
                    if (isset($_POST['button'])) {
                        $lInstructionSql = "INSERT INTO posts "
                            . "(id, user_id, content, created, parent_id) "
                            . "VALUES (NULL, "
                            . $_SESSION['connected_id'] . ", '"
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
                    SELECT posts.id as id, posts.content, posts.created, users.alias as author_name, 
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

                //  echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <div class="image">

                    <img src="image/image1.webp" class="cube1" alt="">
                    <img src="image/image1.webp" class="cube2" alt="">
                    <img src="image/image3.webp" class="carre" alt="">
                    <img src="image/image2.webp" class="carre2" alt="">
                    <img src="image/image2.webp" class="carre3" alt="">
                    <!-- <img src="image/image4.webp" class="rond" alt=""> -->
                </div>
                <div class="image2">

                </div>
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
                        <?php if ($_SESSION['connected_id'] != $userId) {
                            if (isset($_POST['likes'])) {
                                $lInstructionSql3 = "INSERT INTO likes "
                                    . "(id, user_id, post_id) "
                                    . "VALUES (NULL, '"
                                    . $_SESSION['connected_id'] . "', '"
                                    . $_POST['post_id'] . "')";
                                echo $lInstructionSql3;
                                $result3 = $mysqli->query($lInstructionSql3);
                            } ?>
                            <small>
                                <form method="post">
                                    <input type="hidden" name="post_id" value=<?php $post['id'] ?>>
                                    <input type="submit" name="likes" value="♥ J'aime">
                                </form><?php echo $post['like_number'] ?>
                            </small>
                        <?php } ?>

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
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <script src="index.js"></script>
</body>

</html>