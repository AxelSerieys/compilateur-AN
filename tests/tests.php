<?php

require_once "../process.php";
require_once "../Logger.php";

$PRECISION = 7;
$SUCCES = 0;
$ECHECS = 0;
$testId = 0;

$logger = new Logger("tests.log");
demarrer_tests();

function demarrer_tests() {
    test1();
    test2();
    test3();
    test4();
    test5();
    test6();
    test7();
    test8();
    test9();
    test10();
    test11();
    test12();
    test13();
    test14();

    conclusion();
}

function test1() {
    $calc = "2+3";
    $expected = 5;
    test_wrapper($calc, $expected);
}

function test2() {
    $calc = "sin(5)";
    $expected = "-0.95892427466314";
    test_wrapper($calc, $expected);
}

function test3() {
    $calc = "2 + 6 * (3 - 4)";
    $expected = -4;
    test_wrapper($calc, $expected);
}

function test4() {
    $calc = "2 + 6 * 3 - 4";
    $expected = 16;
    test_wrapper($calc, $expected);
}

function test5() {
    $calc = "2 + 6 * (3 - 4) + 3/4";
    $expected = -3.25;
    test_wrapper($calc, $expected);
}

function test6() {
    $calc = "23 + 27 / 001";
    $expected = 50;
    test_wrapper($calc, $expected);
}

function test7() {
    $calc = "3 * sin((3+4)*5 + 2)";
    $expected = -1.930614400071;
    test_wrapper($calc, $expected);
}

function test8() {
    $calc = "cos((3+4)*5)";
    $expected = -0.90369220509151;
    test_wrapper($calc, $expected);
}

function test9() {
    $calc = "tan(3+2)";
    $expected = -3.38051500625;
    test_wrapper($calc, $expected);
}

function test10() {
    $calc = "sin(2) + cos(3)";
    $expected = -0.080695069774764;
    test_wrapper($calc, $expected);
}

function test11() {
    $calc = "cos(1.5)";
    $expected = 0.07073720167;
    test_wrapper($calc, $expected);
}

function test12() {
    $calc = "cos(2*%pi%)";
    $expected = 1;
    test_wrapper($calc, $expected);
}

function test13() {
    $calc = "print \"Test\"";
    $expected = false;
    test_wrapper($calc, $expected);
}

function test14() {
    $calc = "3 > 2";
    $expected = "false";
    test_wrapper($calc, $expected);
}

function conclusion() {
    global $logger, $SUCCES, $ECHECS;
    $ccl = "SUCCÈS : $SUCCES, ECHECS : $ECHECS";
    $logger->fin();
    $logger->log($ccl);
    echo "<br/>$ccl";
}

function test_wrapper($calc, $expected) {
    global $PRECISION, $logger, $SUCCES, $ECHECS, $testId;
    $logger->test(++$testId, $calc);
    $ret = parse($calc, false);
    if(is_string($ret)) {
        if(strcmp($ret, $expected)) {
            echo "Succès<br/>";
            $logger->resultat("SUCCÈS\n", false);
            $SUCCES++;
        } else {
            echo "Erreur<br/>";
            $logger->resultat("ERREUR : Obtenu '" . round($ret, $PRECISION) . "' mais attendu '" . round($expected, $PRECISION) . "'.\n", false);
            $ECHECS++;
        }
    } else {
        if (round($ret, $PRECISION) == round($expected, $PRECISION)) {
            echo "Succès<br/>";
            $logger->resultat("SUCCÈS\n", false);
            $SUCCES++;
        } else {
            echo "Erreur<br/>";
            $logger->resultat("ERREUR : Obtenu '" . round($ret, $PRECISION) . "' mais attendu '" . round($expected, $PRECISION) . "'.\n", false);
            $ECHECS++;
        }
    }
}

?>
<br/>
<a href='tests.log' download>
    <input type='button' value="Télécharger de compte rendu d'exécution"/>
</a>

<input type="button" value="Retour" onclick="history.back()">