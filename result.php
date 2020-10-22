<?php

require_once "Logger.php";
$logger = new Logger();

// Redirection si le fichier n'a pas été choisi
if(!isset($_FILES["file"]["name"])) {
    header("Location: /index.php");
}
$file_name = $_FILES["file"]["name"];

$LINE = 0;
// Début de la lecture
if($file = fopen($file_name, "r")) {
    while(!feof($file)) { // Tant qu'on est pas à la fin du fichier...
        $line = fgets($file);
        $LINE++;
        if(substr(trim($line), 0, 2) !== "//" && strlen(trim($line)) > 0) {
            $res = parse($line);
            echo "$line = $res<br/>";
        }
    }
}

$logger->fin();

// RESSOURCES :
// Utilisation de : http://www.learn4master.com/algorithms/convert-infix-notation-to-reverse-polish-notation-java
// https://github.com/rswier/c4/blob/master/c4.c
// https://www.dcode.fr/reverse-polish-notation
function parse($string) {
    global $logger, $LINE;
    $pile_operations = array();
    $pile_operandes = array();
    $PRIORITES["/"] = 5;
    $PRIORITES["*"] = 5;
    $PRIORITES["+"] = 4;
    $PRIORITES["-"] = 4;
    $PRIORITES["("] = 0;
    $PRIORITES[")"] = 0;

    //$string = str_replace(' ', '', $string);
    $chars = str_split($string);

    $logger->calculer(trim($string));

    // On va tester le caractère courant
    $COL = 0;
    foreach($chars as $char) {
        $COL++;

        // Si c'est un espace, on passe au suivant
        if($char == " " || $char == "\n") {
            continue;
        }

        // Si c'est une parenthèse ouvrante
        if("(" == $char) {
            array_unshift($pile_operations, $char);
            continue;
        }

        // Si c'est une parenthèse fermante
        if(")" == $char) {
            while("(" != $pile_operations[0]) {
                array_push($pile_operandes, array_shift($pile_operations));
            }
            array_shift($pile_operations);
            continue;
        }

        // Si c'est un opérateur
        if(array_key_exists($char, $PRIORITES)) {
            while(sizeof($pile_operations) > 0 && $PRIORITES[$char] <= $PRIORITES[$pile_operations[0]]) {
                array_push($pile_operandes, array_shift($pile_operations));
            }
            array_unshift($pile_operations, $char);
            continue;
        }

        // Si c'est un nombre
        if(is_numeric($char)) {
            array_push($pile_operandes, $char);
            continue;
        }

        $logger->erreur("ENTRÉE INVALIDE : '" . $char . "'", $LINE, $COL);
        die();
    }

    while(sizeof($pile_operations) > 0) {
        array_push($pile_operandes, array_shift($pile_operations));
    }

    $logger->RPN(implode(' ', $pile_operandes));

    // Réalisation du calcul
    while(sizeof($pile_operandes) > 2) {
        if(is_operande($pile_operandes)) {
            for ($i = 0; $i < sizeof($pile_operandes); $i++) {
                if (array_key_exists($pile_operandes[$i], $PRIORITES)) { // Si on est sur une opération,
                    // On l'effectue sur les deux valeurs précédentes
                    $ope1 = $pile_operandes[$i - 2];
                    $ope2 = $pile_operandes[$i - 1];
                    $operation = $pile_operandes[$i];

                    ksort($pile_operandes);
                    array_splice($pile_operandes, $i - 2, 2);

                    $i -= 2;
                    unset($pile_operandes[$i]);

                    switch ($operation) {
                        case "-":
                            $logger->calcul($ope1 . "-" . $ope2);
                            $pile_operandes[$i] = $ope1 - $ope2;
                            break;
                        case "+":
                            $logger->calcul($ope1 . "+" . $ope2);
                            $pile_operandes[$i] = $ope1 + $ope2;
                            break;
                        case "*":
                            $logger->calcul($ope1 . "*" . $ope2);
                            $pile_operandes[$i] = $ope1 * $ope2;
                            break;
                        case "/":
                            if($ope2 == 0) {
                                $logger->erreur("Division par zéro !", $LINE, $COL);
                                die();
                            }
                            $logger->calcul($ope1 . "/" . $ope2);
                            $pile_operandes[$i] = $ope1 / $ope2;
                            break;
                        default:
                            $logger->erreur("OPERATION INCONNUE : '" . $operation . "'", $LINE, $COL);
                            die();
                    }
                    break;
                }
            }
        } else {
            $logger->erreur("Aucun opérateur restant dans la pile !", $LINE, $COL);
            die();
        }
    }

    if(sizeof($pile_operandes) == 2) {
        $logger->erreur("Il manque une opérande dans la file !", $LINE, $COL);
        die();
    }

    $logger->resultat($pile_operandes[0]);
    return $pile_operandes[0];
}

function is_operande($pile_operandes) {
    foreach($pile_operandes as $ope) {
        if($ope == "*" || $ope == "+" || $ope == "-" || $ope == "/") {
            return true;
        }
    }
    return false;
}

function print_array($array) {
    echo "[";
    foreach($array as $cell) {
        echo "$cell, ";
    }
    echo "]<br/>";
}

?>
<br/>
<a href='log.txt' download>
    <input type='button' value="Télécharger de compte rendu d'exécution"/>
</a>
