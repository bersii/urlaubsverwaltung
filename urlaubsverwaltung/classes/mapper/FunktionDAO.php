<?php
namespace classes\mapper;

use classes\model\Funktion;

class FunktionDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    public function readAll() {
        $sql = "SELECT f.fnr, f.fbez FROM funktion f ORDER BY f.fbez";
        $funktionList = array();

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->bind_result($fnr, $fbez)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    while ($prepState->fetch()) {
                        $funktion = new Funktion($fnr, $fbez);
                        $funktionList[] = $funktion;
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $funktionList;
    }
}
?>