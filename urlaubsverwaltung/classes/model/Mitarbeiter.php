<?php
namespace classes\model;

class Mitarbeiter {
    private $vorname;
    private $nachname;
    private $anrede;
    private $titel;
    private $pnr;
    private $abtnr;
    private $abtName;
    private $funktion;
    private $bstufe;
    private $uges;
    private $username;
    private $password;
    private $letzterLogin;
    private $durchwahl;
    private $email;

    public function __construct($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $durchwahl = NULL, $email = NULL, $anrede = Null, $titel = NULL, $letzterLogin = NULL, $username = NULL) {
        $this->vorname = $vorname;
        $this->nachname = $nachname;
        $this->anrede = $anrede;
        $this->titel = $titel;
        $this->pnr = $pnr;
        $this->abtnr = $abtnr;
        $this->abtName = $abtName;
        $this->funktion = $funktion;
        $this->bstufe = $bstufe;
        $this->uges = $uges;
        $this->letzterLogin = $letzterLogin;
        $this->username = $username;
        $this->durchwahl = $durchwahl;
        $this->email = $email;
    }

    /*
     * Setter-Methoden
     */
    public function setVorname($vorname) {
        $this->vorname = $vorname;
    }

    public function setNachname($nachname) {
        $this->nachname = $nachname;
    }

    public function setAnrede($anrede) {
        $this->anrede = $anrede;
    }

    public function setTitel($titel) {
        $this->titel = $titel;
    }

    public function setPnr($pnr) {
        $this->pnr = $pnr;
    }

    public function setAbtnr($abtnr) {
        $this->abtnr = $abtnr;
    }

    public function setAbtName($abtName) {
        $this->abtName = $abtName;
    }

    public function setFunktion($funktion) {
        $this->funktion = $funktion;
    }

    public function setBstufe($bstufe) {
        $this->bstufe = $bstufe;
    }

    public function setUges($uges) {
        $this->uges = $uges;
    }

    public function setDurchwahl($durchwahl) {
        $this->durchwahl = $durchwahl;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setLetzterLogin($letzterLogin) {
        $this->letzterLogin = $letzterLogin;
    }


    /*
     * Getter-Methoden
     */
    public function getVorname() {
        return $this->vorname;
    }

    public function getNachname() {
        return $this->nachname;
    }

    public function getAnrede() {
        return $this->anrede;
    }

    public function getTitel() {
        return $this->titel;
    }

    public function getPnr() {
        return $this->pnr;
    }

    public function getAbtnr() {
        return $this->abtnr;
    }

    public function getAbtName() {
        return $this->abtName;
    }

    public function getFunktion() {
        return $this->funktion;
    }

    public function getBstufe() {
        return $this->bstufe;
    }

    public function getUges() {
        return $this->uges;
    }

    public function getDurchwahl() {
        return $this->durchwahl;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getLetzterLogin() {
        return $this->letzterLogin;
    }

}
?>