<?php
namespace classes\model;

class Urlaub {
    private $unr;
    private $pnr;
    private $beginn;
    private $ende;
    private $tage;
    private $uart;
    private $status;

    public function __construct($unr, $pnr, $beginn, $ende, $tage, $uart, $status) {
        $this->unr = $unr;
        $this->pnr = $pnr;
        $this->beginn = $beginn;
        $this->ende = $ende;
        $this->tage = $tage;
        $this->uart = $uart;
        $this->status = $status;
    }



    /*
     * Setter-Methoden
     */
    public function setUnr($unr) {
        $this->unr = $unr;
    }

    public function setPnr($pnr) {
        $this->pnr = $pnr;
    }

    public function setBeginn($beginn) {
        $this->beginn = $beginn;
    }

    public function setEnde($ende) {
        $this->ende = $ende;
    }

    public function setTage($tage) {
        $this->tage = $tage;
    }

    public function setUart($uart) {
        $this->uart = $uart;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    /*
     * Getter-Methoden
     */
    public function getUnr() {
        return $this->unr;
    }

    public function getPnr() {
        return $this->pnr;
    }

    public function getBeginn() {
        return $this->beginn;
    }

    public function getEnde() {
        return $this->ende;
    }

    public function getTage() {
        return $this->tage;
    }

    public function getUart() {
        return $this->uart;
    }

    public function getStatus() {
        return $this->status;
    }

}

?>