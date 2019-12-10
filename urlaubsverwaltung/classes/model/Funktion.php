<?php
namespace classes\model;

class Funktion {
    private $fnr;
    private $fbez;

    public function __construct($fnr, $fbez) {
        $this->fnr = $fnr;
        $this->fbez = $fbez;
    }

    /*
     * Setter-Methoden
     */
    public function setFnr($fnr) {
        $this->fnr = $fnr;
    }

    public function setFbez($fbez) {
        $this->fbez = $fbez;
    }

    /*
     * Getter-Methoden
     */
    public function getFnr() {
        return $this->fnr;
    }

    public function getFbez() {
        return $this->fbez;
    }


}
?>