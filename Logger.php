<?php

class Logger {
    private $FILE_NAME;
    private $offset = "";

    function __construct($FILE_NAME = "log.txt") {
        $offset = "";
        $this->FILE_NAME = $FILE_NAME;
        $this->empty_log();
        $this->log("DÉBUT DE LA COMPILATION (" . date('d/m/yy - H:i:s') . ")");
    }

    function test($id, $string) {
        $this->log_write("TEST-$id", $string);
    }

    function calcul($string) {
        $this->log_write("CALCUL", $string);
    }

    function calculer($string) {
        $this->log_write("CALCULER", $string);
    }

    function RPN($string) {
        $this->log_write("RPN", $string);
    }

    function resultat($string, $fin = true) {
        $this->log_write("RESULTAT", $string);
        if($fin) {
            $this->log_write("FIN", "==========");
            $this->print_empty_line();
        }
    }

    function erreur($string, $line, $col) {
        $this->log_write("ERREUR", $string . " À LA LIGNE $line, COLONNE $col", true);
        $this->log_write("FIN", "==========");
    }

    function setOffset($offset) {
        $this->offset = $offset;
    }

    function ajouter_tab_offset() {
        $this->offset = str_repeat(" ", strlen($this->offset) + 4);
        return $this->offset;
    }

    function supprimer_tab_offset() {
        if(strlen($this->offset) >= 4)
            $this->offset = substr($this->offset, 0, strlen($this->offset)-4);
        return $this->offset;
    }

    function fin() {
        global $VARIABLES;
        $this->log("État des variables : " . json_encode($VARIABLES));
        $this->log("FIN DE LA COMPILATION (" . date('d/m/yy - H:i:s') . ")");
    }

    function log($string) {
        $this->log_write("LOG", $string);
    }

    function debug($string) {
        $this->log_write("DEBUG", $string);
    }

    function cond($string, $res) {
        $this->log_write("CONDITION", "$string == $res");
    }

    function var($name, $val) {
        $this->log_write("VARIABLE", "$$name = $val");
    }

    function print($string) {
        $this->log_write("PRINT", $string);
    }

    function input($string) {
        $this->log_write("ENTRÉE", $string);
    }

    function pour($var, $val) {
        $this->log_write("POUR", "$var = $val");
    }

    function print_empty_line() {
        file_put_contents($this->FILE_NAME, "\n", FILE_APPEND);
    }

    function log_write($title, $string, $echo = false) {
        $toprint = "$this->offset[$title] : $string\n";
        file_put_contents($this->FILE_NAME, $toprint, FILE_APPEND);
        if($echo)
            echo "$toprint<br/>";
    }

    function empty_log() {
        $fh = fopen($this->FILE_NAME, 'w');
        fclose($fh);
    }
}