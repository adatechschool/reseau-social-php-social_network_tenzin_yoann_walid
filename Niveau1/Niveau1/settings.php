<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Paramètres</title>
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
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes abonnements</a></li>
                <li><a href="deconnexion.php?user_id=<?php echo $_SESSION['connected_id'] ?> ">Se déconnecter</a></li>

            </ul>

        </nav>
    </header>
    <div id="wrapper" class='profile'>


        <aside>
            <img src="Haganezuka.Hotaru.png" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Hotaru 🈴 ㊙️</h3>
                <p>Sur cette page vous trouverez les informations de l'utilisatrice
                    n° <?php echo intval($_GET['user_id']) ?></p>

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
    <img src="settings.php?id=2" alt="">
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
            <div class="image">

                <img src="image/image1.webp" class="cube1" alt="">
                <img src="image/image1.webp" class="cube2" alt="">
                <img src="image/image3.webp" class="carre" alt="">
                <img src="image/image2.webp" class="carre2" alt="">
                <img src="image/image2.webp" class="carre3" alt="">
            </div>

        </aside>
        <main>
            <?php
            /**
             * Etape 1: Les paramètres concernent une utilisatrice en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisatrice
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId = intval($_GET['user_id']);

            /**
             * Etape 2: se connecter à la base de donnée
             */
            include "config.php";

            /**
             * Etape 3: récupérer le nom de l'utilisateur
             */
            $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$userId' 
                    GROUP BY users.id
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            $user = $lesInformations->fetch_assoc();

            /**
             * Etape 4: à vous de jouer
             */
            //@todo: afficher le résultat de la ligne ci dessous, remplacer les valeurs ci-après puiseffacer la ligne ci-dessous
            // echo "<pre>" . print_r($user, 1) . "</pre>";
            ?>
            <article class='parameters'>
                <h3>Mes paramètres</h3>
                <dl>
                    <dt>Pseudo</dt>
                    <dd><?php echo $user['alias'] ?></dd>
                    <dt>Email</dt>
                    <dd><?php echo $user['email'] ?></dd>
                    <dt>Nombre de message</dt>
                    <dd><?php echo $user['totalpost'] ?></dd>
                    <dt>Nombre de "J'aime" donnés </dt>
                    <dd><?php echo $user['totalgiven'] ?></dd>
                    <dt>Nombre de "J'aime" reçus</dt>
                    <dd><?php echo $user['totalrecieved'] ?></dd>
                </dl>
                <form action="" name="fo" method="post" enctype="multipart/form-data">
                    <input type="file" name="image" /><br>
                    <input type="submit" name="valider" value="charger" />
                </form>
            </article>
        </main>
    </div>

</body>

</html>