<?php
namespace classes\model;

class Urlaubsart {
    private $uart;
    private $bez;

    public function __construct($uart, $bez) {
        $this->uart = $uart;
        $this->bez = $bez;
    }

    /*
     * Setter-Methoden
     */
    public function setUart($uart) {
        $this->uart = $uart;
    }

    public function setBez($bez) {
        $this->bez = $bez;
    }

    /*
     * Getter-Methoden
     */
    public function getUart() {
        return $this->uart;
    }

    public function getBez() {
        return $this->bez;
    }


}
?>