<html>
    <head>
        <link rel="icon" type="image/jpeg" href="3il.jpeg" />
        <link rel="stylesheet" type="text/css" href="result.css" />
    </head>
    <body>
        <a href='log.txt' download>
            <input type='button' value="Télécharger de compte rendu d'exécution"/>
        </a>

        <input type="button" value="Retour" onclick="history.back()"><br/><br/>
<?php

const FILE_COMPIL = "test.src";
require_once "Logger.php";
require_once "process.php";

$logger = new Logger("log.txt");

// Redirection si le fichier n'a pas été choisi
// Si on a cliqué sur le bouton compiler
if(!isset($_FILES["file"]["name"]) && $_SERVER['REQUEST_URI'] == "/result.php") {
    header("Location: /index.php");
}
$file_name = $_FILES["file"]["name"]; //a supprimer

$txtarea = $_POST['texte'];
//Copie de emptyLog() pour le fichier test.src
$compilFile = fopen(FILE_COMPIL, 'w');
fclose($compilFile);
//Copie de log_write() pour le fichier test.src
$towrite = "$txtarea\n";
file_put_contents(FILE_COMPIL, $towrite, FILE_APPEND);

$LINE = 0;
// Début de la lecture
if($file = fopen(FILE_COMPIL, "r")) {
    while(!feof($file)) { // Tant qu'on est pas à la fin du fichier...
        $line = explode("//", fgets($file))[0];
        $LINE++;
        if(substr(trim($line), 0, 2) !== "//" && strlen(trim($line)) > 0) {
            $res = parse($line);
            if($res !== false ) {
                echo "<div class='resultat'>$line = $res</div>";
            }
        }
    }
}

$logger->fin();

?>
    </body>
</html>
