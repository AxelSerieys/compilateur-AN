<?php

$VARIABLES = array();
$in_for = false;
$for_variable = NULL;
$for_values = array();
$for_instructions = array();

// RESSOURCES :
// Utilisation de : http://www.learn4master.com/algorithms/convert-infix-notation-to-reverse-polish-notation-java
// https://github.com/rswier/c4/blob/master/c4.c
// https://www.dcode.fr/reverse-polish-notation
function parse($string, $print_result = true, $in_function = false, $log_offset = "", $ploop_execute = NULL) {
    global $logger, $LINE, $VARIABLES, $in_for, $for_variable, $for_values, $for_instructions;
    $logger->setOffset($log_offset);
    $pile_operations = array();
    $pile_operandes = array();
    $open_parentheses = 0;
    $loop_execute = $ploop_execute; // NULL si on est PAS dans une boucle, 0 si on doit exécuter le corps de la boucle, 1 sinon.
    $PRIORITES["/"] = 5;
    $PRIORITES["*"] = 5;
    $PRIORITES["+"] = 4;
    $PRIORITES["-"] = 4;
    $PRIORITES["("] = 0;
    $PRIORITES[")"] = 0;
    $PRIORITES["<"] = 4;
    $PRIORITES[">"] = 4;
    $PRIORITES[">="] = 4;
    $PRIORITES["<="] = 4;
    $PRIORITES["=="] = 4;
    $PRIORITES["=="] = 4;
    $PRIORITES["%"] = 5;

    // PREPROCESSEUR
    $string = str_replace("%pi%", pi(), $string);

    $string = trim($string);

    $chars = calcul_as_array(trim($string));
    $logger->input(trim($string));

    // 1 : On enregistre toutes les lignes du for dans une variable
    // 2 : On les exécute 1 à 1 tant qu'il y a des i
    if($in_for) {
        if($string == "endfor") {
            $logger->print_empty_line();
            $in_for = false;
            while(sizeof($for_values) != 0) {
                $VARIABLES[$for_variable] = array_shift($for_values);
                $logger->supprimer_tab_offset();
                $logger->pour("$$for_variable", $VARIABLES[$for_variable]);
                for($ligne = 0; $ligne < sizeof($for_instructions); $ligne++) { // Pour chaque
                    $res = parse($for_instructions[$ligne], true, false, "    ");
                    if($res !== false ) {
                        echo "$for_instructions[$ligne] = $res<br/>";
                    }
                }
            }
            return false;
        } else {
            array_push($for_instructions, $string);
            return false;
        }
    }

    // si c'est un print
    if(($ret = preg_filter('/^print \"(.*)\"$/', '$1', $string)) != "") {
        while(($varname = preg_filter('/^\$\{(.*)\}$/', '$1', $ret)) != "") {
            $ret = preg_replace("@\\$\{(.*)\}@", $VARIABLES[$varname], trim($ret));
        }
        $ret = str_replace("\\n", "<br/>", $ret);
        $logger->print($ret);
        echo "--><i>$ret</i><br/>";
        return false;
    }

    // Si on affecte une valeur à une variable
    if(($ret = preg_filter('/^\$(.*) ?\= ?(.*)$/', '$1;$2', $string)) != "") {
        $name = explode(";", $ret)[0];
        $val = parse(explode(";", $ret)[1], false);
        nouvelle_variable(trim($name), trim($val));
        return false;
    }

    // Si c'est une condition
    if(($ret = preg_filter('/^if ?\((.*)\) \{$/', '$1', $string)) != "") {
        global $loop_execute;
        $res = parse($ret, false);
        $logger->cond($ret, $res);
        if($res == "true") {
            $loop_execute = 0;
            // On effectue le traitement
        } else {
            // On n'effectue pas le traitement
            $loop_execute = 1;

        }
        return false;
    }

    // Si c'est un for
    if(($ret = preg_filter("/^ *for *\(\\$(.*)= *\[(.*); *(.*)\]\).*$/", "$1;$2;$3", $string)) != "") {
        global $in_for, $for_variable;
        $params = explode(";", $ret); // case 0 : nom de la variable. case 1 : valeur initiale de la var. case 2 : valeur finale
        for($i = $params[1]; $i <= $params[2]; $i++) {
            array_push($for_values, $i);
        }

        $for_variable = trim($params[0]);
        $VARIABLES[$for_variable] = NULL;
        $in_for = true;
        return false;
    }

    global $loop_execute;

    if($loop_execute != NULL) {
        if($string != "}") {
            if($loop_execute == 0) {
                parse($string, false, false, "", true);
            }
        } else {
            $loop_execute = false;
        }
        return false;
    }

    if($string == "}") {
        $loop_execute = false;
        return false;
    }

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
            $open_parentheses++;
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
                if(--$open_parentheses == 0) {
                    $in_function = false;
                }
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

        // Si c'est une variable
        if(array_key_exists(str_replace("$", "", $char), $VARIABLES)) {
            $char = $VARIABLES[str_replace("$", "", $char)];
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

        if($char == "sin" || $char == "cos" || $char == "tan" || $char == "log" || $char == "exp") {
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
        // Si c'est une fonction sinus, cosinus, tangeante, log ou exp
        for($i = 0; $i < sizeof($pile_operandes); $i++) {
            if (($ret = preg_filter('/sin\((.*)\)/', '$1', $pile_operandes[$i])) != "") {
                $in_function = true;
                $logger->calcul($pile_operandes[$i]);
                $pile_operandes[$i] = fc_sin($ret);
            } else if(($ret = preg_filter('/cos\((.*)\)/', '$1', $pile_operandes[$i])) != "") {
                $in_function = true;
                $logger->calcul($pile_operandes[$i]);
                $pile_operandes[$i] = fc_cos($ret);
            } else if(($ret = preg_filter('/tan\((.*)\)/', '$1', $pile_operandes[$i])) != "") {
                $in_function = true;
                $logger->calcul($pile_operandes[$i]);
                $pile_operandes[$i] = fc_tan($ret);
            } else if(($ret = preg_filter('/log\((.*)\)/', '$1', $pile_operandes[$i])) != "") {
                $in_function = true;
                $logger->calcul($pile_operandes[$i]);
                $pile_operandes[$i] = fc_log($ret);
            } else if(($ret = preg_filter('/exp\((.*)\)/', '$1', $pile_operandes[$i])) != "") {
                $in_function = true;
                $logger->calcul($pile_operandes[$i]);
                $pile_operandes[$i] = fc_exp($ret);
            }
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
                        case "<":
                            if($ope2 == "=")
                                $pile_operandes[$i] = ($ope1 <= $pile_operandes[$i+1]) ? "true" : "false";
                            else
                                $pile_operandes[$i] = ($ope1 < $ope2) ? "true" : "false";
                            break;
                        case ">":
                            $logger->calcul($ope1 . ">" . $ope2);
                            $pile_operandes[$i] = ($ope1 > $ope2) ? "true" : "false";
                            break;
                        case ">=":
                            $logger->calcul($ope1 . ">=" . $ope2);
                            $pile_operandes[$i] = ($ope1 >= $ope2) ? "true" : "false";
                            break;
                        case "<=":
                            $logger->calcul($ope1 . "<=" . $ope2);
                            $pile_operandes[$i] = ($ope1 <= $ope2) ? "true" : "false";
                            break;
                        case "==":
                            $logger->calcul("$ope1 == $ope2");
                            $pile_operandes[$i] = ($ope1 == $ope2) ? "true" : "false";
                            break;
                        case "%":
                            $logger->calcul("$ope1 % $ope2");
                            $pile_operandes[$i] = ($ope1 % $ope2);
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

    if(sizeof($pile_operandes) <= 2) {
        if(($val = preg_filter('/sin\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $pile_operandes[0] = fc_sin($val);
        } else if(($val = preg_filter('/cos\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $pile_operandes[0] = fc_cos($val);
        } else if(($val = preg_filter('/tan\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $pile_operandes[0] = fc_tan($val);
        } else if(($val = preg_filter('/log\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $pile_operandes[0] = fc_log($val);
        } else if(($val = preg_filter('/exp\((.*)\)/', '$1', $pile_operandes[0])) != "") {
            $logger->calcul($pile_operandes[0]);
            $pile_operandes[0] = fc_exp($val);
        }
        if(sizeof($pile_operandes) == 1) { // S'il ne reste plus qu'un truc, c'est le résultat
            if($print_result) {
                $logger->resultat($pile_operandes[0], true);
            } else {
                $logger->resultat($pile_operandes[0], false);
            }
            return $pile_operandes[0];
        } else {
            $logger->erreur("Il manque une opérande dans la file !", $LINE, $COL);
            die();
        }
    }
}

function nouvelle_variable($nom, $valeur) {
    global $VARIABLES, $logger, $LINE, $COL;
    if(!array_key_exists($nom, $VARIABLES)) {
        $VARIABLES[$nom] = $valeur;
        $logger->var($nom, $valeur);
    } else {
        $logger->erreur("Une variable portant le même nom existe déjà : $nom", $LINE, $COL);
    }
}

function update_variable($nom, $valeur) {
    global $VARIABLES, $logger, $LINE, $COL;
    if(array_key_exists($nom, $VARIABLES)) {
        $VARIABLES[$nom] = $valeur;
        $logger->var($nom, $valeur);
    } else {
        $logger->erreur("La variable $nom n'a pas été initialisée.", $LINE, $COL);
    }
}

function fc_sin($param) {
    global $logger;
    global $pile_operandes;
    $log_offset = $logger->ajouter_tab_offset();
    //$log_offset = "    ";
    $res = parse($param, false, false, $log_offset);
    $log_offset = $logger->supprimer_tab_offset();
    //$log_offset = "";
    $logger->setOffset($log_offset);
    return sin($res);
}

function fc_cos($param) {
    global $logger;
    global $pile_operandes;
    //$log_offset = "    ";
    $log_offset = $logger->ajouter_tab_offset();
    $res = parse($param, false, false, $log_offset);
    //$log_offset = "";
    $log_offset =  $logger->supprimer_tab_offset();
    $logger->setOffset($log_offset);
    return cos($res);
}

function fc_tan($param) {
    global $logger;
    global $pile_operandes;
    //$log_offset = "    ";
    $log_offset = $logger->ajouter_tab_offset();
    $res = parse($param, false, false, $log_offset);
    //$log_offset = "";
    $log_offset = $logger->supprimer_tab_offset();
    $logger->setOffset($log_offset);
    return tan($res);
}

function fc_log($param) {
    global $logger;
    global $pile_operandes;
    //$log_offset = "    ";
    $log_offset = $logger->ajouter_tab_offset();
    $res = parse($param, false, false, $log_offset);
    //$log_offset = "";
    $log_offset = $logger->supprimer_tab_offset();
    $logger->setOffset($log_offset);
    return log($res, 10);
}

function fc_exp($param) {
    global $logger;
    global $pile_operandes;
    //$log_offset = "    ";
    $log_offset = $logger->ajouter_tab_offset();
    $res = parse($param, false, false, $log_offset);
    //$log_offset = "";
    $log_offset = $logger->supprimer_tab_offset();
    $logger->setOffset($log_offset);
    return exp($res);
}

function calcul_as_array($calcul) {
    $arr = array();
    $index = 0;

    for($i = 0; $i < strlen($calcul); $i++) {
        $char = $calcul[$i];
        if($char == " " || $char == "\n")
            continue;

        // Tous les caractères qui peuvent séparer les nombres
        if($char == "+" || $char == "-" || $char == "/" || $char == "*" || $char == "(" || $char == ")" || $char == ">"
        || $char == "<" || $char == "%") {
            if (strlen($arr[$index]) > 0)
                $index++;
            $arr[$index] = $char;
            $index++;
        } else if($char == "=" && ($arr[$index-1] == "<" || $arr[$index-1] == ">")) {
            $arr[$index - 1] .= "=";
        } else if($char == "=" && ($calcul[$i+1] == "=" || $calcul[$i-1] == "=")) {
            if($arr[$index] !== "=")
                $index++;
            $arr[$index] .= "=";
            if($arr[$index] == "==")
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
        if($ope == "*" || $ope == "+" || $ope == "-" || $ope == "/" || $ope == ">" || $ope == "<" || $ope == ">=" || $ope == "<=" || $ope == "==" || $ope == "%") {
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