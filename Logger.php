<?php

class Logger {
    const FILE_NAME = "log.txt";

    function __construct() {
        $this->empty_log();
        $this->log_write("DEBUT", "DE LA COMPILATION (" . date('d/m/yy - H:m:s') . ")");
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

    function resultat($string) {
        $this->log_write("RESULTAT", $string);
        $this->log_write("FIN", "==========");
    }

    function erreur($string, $line, $col) {
        $this->log_write("ERREUR", $string . " À LA LIGNE $line, COLONNE $col");
        $this->log_write("FIN", "==========");
    }

    function fin() {
        $this->log_write("FIN", "DE LA COMPILATION (" . date('d/m/yy - H:m:s') . ")");
    }

    function debug($string) {
        $this->log_write("DEBUG", $string);
    }

    function log_write($title, $string) {
        $toprint = "[$title] : $string\n";
        file_put_contents(self::FILE_NAME, $toprint, FILE_APPEND);
    }

    function empty_log() {
        $fh = fopen(self::FILE_NAME, 'w');
        fclose($fh);
    }
}