<?php

require_once "Logger.php";
require_once "process.php";

$logger = new Logger("log.txt");

// Redirection si le fichier n'a pas été choisi
// Si on a cliqué sur le bouton compiler
if(!isset($_FILES["file"]["name"]) && $_SERVER['REQUEST_URI'] == "/result.php") {
    header("Location: /index.php");
}
$file_name = $_FILES["file"]["name"]; //a supprimer

$LINE = 0;
// Début de la lecture
if($file = fopen($file_name, "r")) {
    while(!feof($file)) { // Tant qu'on est pas à la fin du fichier...
        $line = explode("//", fgets($file))[0];
        $LINE++;
        if(substr(trim($line), 0, 2) !== "//" && strlen(trim($line)) > 0) {
            $res = parse($line);
            if($res !== false ) {
                echo "$line = $res<br/>";
            }
        }
    }
}

$logger->fin();

?>
<br/>
<a href='log.txt' download>
    <input type='button' value="Télécharger de compte rendu d'exécution"/>
</a>

<input type="button" value="Retour" onclick="history.back()">