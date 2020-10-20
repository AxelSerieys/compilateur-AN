<?php

// Redirection si le fichier n'a pas été choisi
if(!isset($_FILES["file"]["name"])) {
    header("Location: /index.php");
}
$file_name = $_FILES["file"]["name"];

// Début de la lecture
if($file = fopen($file_name, "r")) {
    while(!feof($file)) { // Tant qu'on est pas à la fin du fichier...
        $line = fgets($file);
        parse($line);
    }
}


// Utilisation de : http://www.learn4master.com/algorithms/convert-infix-notation-to-reverse-polish-notation-java
// https://github.com/rswier/c4/blob/master/c4.c
function parse($string) {
    $pile_operations = array();
    $pile_operandes = array();
    $PRIORITES["/"] = 5;
    $PRIORITES["*"] = 5;
    $PRIORITES["+"] = 4;
    $PRIORITES["-"] = 4;

    $string = str_replace(' ', '', $string);
    $chars = str_split($string);

    // On va tester le caractère courant
    foreach($chars as $char) {
        // Si c'est un nombre
        if(is_numeric($char)) {
            array_push($pile_operandes, $char);
        }

        // Si c'est une parenthèse ouvrante
        if("(" == $char) {
            array_unshift($pile_operations, $char);
        }

        // Si c'est un opérateur
        if(array_key_exists($char, $PRIORITES)) {
            if(empty($pile_operations)) {
                array_push($pile_operations, $char);
            } else if($PRIORITES[$char] <= $PRIORITES[$pile_operations[0]]) {
            }else {
                array_unshift($pile_operations, $char);
            }
        }

        // Suppression des parenthèses dans la pile des opérateurs :
    }

    echo "Opérations : ";
    print_array($pile_operations);
    echo "Opérandes : ";
    print_array($pile_operandes);
    echo "<br/>Ce qu'on veut obtenir : <br/>operations = [-, *, +] <br/> operandes = [2, 6, 3, 4]";
}

function print_array($array) {
    echo "[";
    foreach($array as $cell) {
        echo "$cell, ";
    }
    echo "]<br/>";
}