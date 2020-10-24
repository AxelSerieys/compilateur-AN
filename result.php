<?php

const FILE_COMPIL = "test.src";
require_once "Logger.php";
$logger = new Logger();

// Redirection si le fichier n'a pas été choisi
if(!isset($_FILES["file"]["name"])) {
    header("Location: /index.php");
}
$file_name = $_FILES["file"]["name"];

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
function parse($string, $print_result = true, $in_function = false, $log_offset = "") {
    global $logger, $LINE;
    $logger->setOffset($log_offset);
    $pile_operations = array();
    $pile_operandes = array();
    $PRIORITES["/"] = 5;
    $PRIORITES["*"] = 5;
    $PRIORITES["+"] = 4;
    $PRIORITES["-"] = 4;
    $PRIORITES["("] = 0;
    $PRIORITES[")"] = 0;

    $chars = calcul_as_array(trim($string));
    $logger->calculer(trim($string));

    // On va tester le caractère courant
    $COL = 0;
    for($c = 0; $c < sizeof($chars); $c++) {
        $char = $chars[$c];
        $COL++;

        // Si c'est un espace, on passe au suivant
        if($char == " " || $char == "\n") {
            continue;
        }

        // Si c'est une parenthèse ouvrante
        if("(" == $char) {
            if($in_function) {
                $pile_operandes[sizeof($pile_operandes)-1] .= "(";
            }
            array_unshift($pile_operations, $char);
            continue;
        }

        // Si c'est une parenthèse fermante
        if(")" == $char) {
            while("(" != $pile_operations[0]) {
                array_push($pile_operandes, array_shift($pile_operations));
            }
            array_shift($pile_operations);
            if($in_function) {
                $pile_operandes[sizeof($pile_operandes)-1] .= ")";
                $in_function = false;
            }
            continue;
        }

        // Si c'est un opérateur
        if(array_key_exists($char, $PRIORITES)) {
            if($in_function) {
                $pile_operandes[sizeof($pile_operandes)-1] .= $char;
            } else {
                while (sizeof($pile_operations) > 0 && $PRIORITES[$char] <= $PRIORITES[$pile_operations[0]]) {
                    array_push($pile_operandes, array_shift($pile_operations));
                }
                array_unshift($pile_operations, $char);
            }
            continue;
        }

        // Si c'est un nombre
        if(is_numeric($char)) {
            if($in_function) {
                $pile_operandes[sizeof($pile_operandes) - 1] .= $char;
            } else {
                array_push($pile_operandes, $char);
            }
            continue;
        }

        if($char == "sin") {
            array_push($pile_operandes, $char);
            $in_function = true;
            continue;
        }

        $logger->erreur("ENTRÉE INVALIDE : '" . $char . "'", $LINE, $COL);
        die();
    }

    while(sizeof($pile_operations) > 0) {
        array_push($pile_operandes, array_shift($pile_operations));
    }

    $rpn = implode(' ', $pile_operandes);
    if(trim($rpn) !== trim($string)) {
        $logger->RPN($rpn);
    }

    // Réalisation du calcul
    while(sizeof($pile_operandes) > 2) {
        // Si c'est une fonction sinus
        if(($ret = preg_filter('/sin\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            echo "AAAAAH";
        }

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
                } else if($pile_operandes[$i] == "sin") {
                    echo "COUCOU";die();
                }
            }
        } else {
            $logger->erreur("Aucun opérateur restant dans la pile !", $LINE, $COL);
            die();
        }
    }

    if(sizeof($pile_operandes) <= 2) {
        if(($val = preg_filter('/sin\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $res = parse($val, false, false, "    ");
            $logger->setOffset("");
            $log_offset = "";
            $pile_operandes[0] = sin($res);
        }
        if(sizeof($pile_operandes) == 1) { // S'il ne reste plus qu'un truc, c'est le résultat
            if(!$print_result) {
                $logger->resultat($pile_operandes[0], false);
            } else {
                $logger->resultat($pile_operandes[0], true);
            }
            return $pile_operandes[0];
        } else {
            $logger->erreur("Il manque une opérande dans la file !", $LINE, $COL);
            die();
        }
    }
}

function calcul_as_array($calcul) {
    $arr = array();
    $index = 0;

    for($i = 0; $i < strlen($calcul); $i++) {
        $char = $calcul[$i];
        if($char == " " || $char == "\n")
            continue;

        // Tous les caractères qui peuvent séparer les nombres
        if($char == "+" || $char == "-" || $char == "/" || $char == "*" || $char == "(" || $char == ")") {
            if(strlen($arr[$index]) > 0)
                $index++;
            $arr[$index] = $char;
            $index++;
        } else {
            if(!isset($arr[$index]))
                $arr[$index] = "";
            $arr[$index] .= $char;
        }
    }

    return $arr;
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
