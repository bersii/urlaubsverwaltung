<?php
namespace classes\model;

class Abteilung {
    private $abtnr;
    private $name;

    public function __construct($abtnr, $name) {
        $this->abtnr = $abtnr;
        $this->name = $name;
    }

    /*
     * Setter-Methoden
     */
    public function setAbtnr($abtnr) {
        $this->abtnr = $abtnr;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /*
     * Getter-Methoden
     */
    public function getAbtnr() {
        return $this->abtnr;
    }

    public function getName() {
        return $this->name;
    }


}
?>