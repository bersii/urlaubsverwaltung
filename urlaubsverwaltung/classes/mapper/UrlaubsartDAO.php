<?php
namespace classes\mapper;

use classes\model\Urlaubsart;

class UrlaubsartDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    /*
     *  Erzeugt einen Urlaubsart-Datensatz in der Datenbank.
     */
    public function create(Urlaubsart $urlaubsart) {
        $uart = $urlaubsart->getUart();
        $bez = $urlaubsart->getBez();

        $sql = "INSERT INTO urlaubsart (uart, bez) VALUES (?, ?)";

        if(!$prepState = $this->dbConnect->prepare($sql)) {
           echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("is", $uart, $bez)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }

    /*
     * Erzeugt ein Urlaubsart-Objekt anhand der UART-Nummer
     */
    public function readByUart(Urlaubsart $urlaubsart) {
        $uart = $urlaubsart->getUart();

        $sql = "SELECT * FROM urlaubsart WHERE uart = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("i", $uart)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($uart, $bez)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        if (!$prepState->fetch()) {
                            echo "Fehler! Fetchen nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                        }
                        else {
                            $urlaubsart = new Urlaubsart($uart, $bez);
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $urlaubsart;
    }

    public function readAll() {
        $sql = "SELECT * FROM urlaubsart";
        $urlaubsartList = array();

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->bind_result($uart, $bez)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->fetch()) {
                        echo "Fehler! Fetchen nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        while ($prepState->fetch()) {
                            $urlaubsart = new Urlaubsart($uart, $bez);
                            $urlaubsartList[] = $urlaubsart;
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $urlaubsartList;
    }
}
?>