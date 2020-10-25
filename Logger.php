<?php

class Logger {
    private $FILE_NAME;
    private $offset = "";

    function __construct($FILE_NAME = "log.txt") {
        $offset = "";
        $this->FILE_NAME = $FILE_NAME;
        $this->empty_log();
        $this->log("DÉBUT DE LA COMPILATION (" . date('d/m/yy - H:m:s') . ")");
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
        $this->log_write("ERREUR", $string . " À LA LIGNE $line, COLONNE $col");
        $this->log_write("FIN", "==========");
    }

    function setOffset($offset) {
        $this->offset = $offset;
    }

    function fin() {
        $this->log("FIN DE LA COMPILATION (" . date('d/m/yy - H:m:s') . ")");
    }

    function log($string) {
        $this->log_write("LOG", $string);
    }

    function debug($string) {
        $this->log_write("DEBUG", $string);
    }

    function print($string) {
        $this->log_write("PRINT", $string);
    }

    function input($string) {
        $this->log_write("ENTRÉE", $string);
    }

    function print_empty_line() {
        file_put_contents($this->FILE_NAME, "\n", FILE_APPEND);
    }

    function log_write($title, $string) {
        $toprint = "$this->offset[$title] : $string\n";
        file_put_contents($this->FILE_NAME, $toprint, FILE_APPEND);
    }

    function empty_log() {
        $fh = fopen($this->FILE_NAME, 'w');
        fclose($fh);
    }
}