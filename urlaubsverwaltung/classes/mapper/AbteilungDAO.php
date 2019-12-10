<?php
namespace classes\mapper;

use classes\model\Abteilung;

class AbteilungDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    public function readAll() {
        $sql = "SELECT a.abtnr, a.name FROM abteilung a ORDER BY a.abtnr";
        $abteilungList = array();

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->bind_result($abtnr, $name)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    while ($prepState->fetch()) {
                        $abteilung = new Abteilung($abtnr, $name);
                        $abteilungList[] = $abteilung;
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $abteilungList;
    }

    public function readByAbtnr($abtnr) {
        $sql = "SELECT a.name FROM abteilung a WHERE a.abtnr = ?";
        $abteilung = NULL;

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("i", $abtnr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($name)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        while ($prepState->fetch()) {
                            $abteilung = new Abteilung($abtnr, $name);
                            $prepState->free_result();
                            $prepState->close();
                            return $abteilung;
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $abteilung;
    }
}
?>