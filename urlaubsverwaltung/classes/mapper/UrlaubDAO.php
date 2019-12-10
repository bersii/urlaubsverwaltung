<?php
namespace classes\mapper;

use classes\model\Mitarbeiter;
use classes\model\Urlaub;

class UrlaubDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    /*
     *  Erzeugt einen Urlaubs-Datensatz in der Datenbank.
     */
    public function create(Urlaub $urlaub) {
        $pnr = $urlaub->getPnr();
        $beginn = $urlaub->getBeginn();
        $ende = $urlaub->getEnde();
        $tage = $urlaub->getTage();
        $uart = $urlaub->getUart();
        $status = $urlaub->getStatus();

        $sql = "INSERT INTO urlaub (pnr, beginn, ende, tage, uart, status) VALUES (?, ?, ?, ?, ?, ?)";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("isssis", $pnr, $beginn, $ende, $tage, $uart, $status)) {
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
     * Erzeugt ein Array aus Urlaubs-Objekten anhand der Mitarbeiter PNR
     */
    public function readByPnr($pnr) {
        $arrObjUrlaub = [];
        $sql = "SELECT * FROM urlaub u WHERE u.pnr = ? AND u.status != 'K' AND YEAR(u.beginn) >= YEAR(NOW())-1 ORDER BY u.beginn";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("i", $pnr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($unr, $pnr, $beginn, $ende, $tage, $uart, $status)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        while($prepState->fetch()) {
                            $objUrlaub = new Urlaub($unr, $pnr, $beginn, $ende, $tage, $uart, $status);
                            $arrObjUrlaub[] = $objUrlaub;
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();

        return $arrObjUrlaub;
    }


    /*
     * Erzeugt ein Array aus Urlaubs-Objekten anhand der Mitarbeiter UNR
     */
    public function readByUnr($unr) {
        $sql = "SELECT * FROM urlaub WHERE unr = ? AND YEAR(beginn) >= YEAR(NOW())-1 ORDER BY beginn";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("i", $unr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($unr, $pnr, $beginn, $ende, $tage, $uart, $status)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        while($prepState->fetch()) {
                            $Urlaub = new Urlaub($unr, $pnr, $beginn, $ende, $tage, $uart, $status);
                            $prepState->free_result();
                            $prepState->close();
                            return $Urlaub;
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return false;
    }

    /*
     * Erzeugt ein Array aller Urlaubsdaten
     */
    public function readAll() {
        $urlaubList = array();
        $sql = "SELECT * FROM urlaub ORDER BY beginn";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->bind_result($unr, $pnr, $beginn, $ende, $tage, $uart, $status)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    while ($prepState->fetch()) {
                        $urlaub = new Urlaub($unr, $pnr, $beginn, $ende, $tage, $uart, $status);
                        $urlaubList[] = $urlaub;
                    }
                }
            }
        }
        $prepState->close();
        return $urlaubList;
    }

    public function readPendingCount() {
        $urlaubList = array();
        $sql = "SELECT COUNT(*) FROM urlaub WHERE status = 'P'";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->bind_result($pendingCount)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    $prepState->fetch();
                }
            }
        }
        $prepState->close();
        return $pendingCount;
    }

    public function readAuByPnr($pnr) {
        $sql = "SELECT u.tage FROM urlaub u WHERE YEAR(u.beginn) = YEAR(NOW()) AND u.pnr = ? AND u.status = 'K'";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("i", $pnr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($tage)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        if($prepState->fetch()) {
                            $prepState->free_result();
                            $prepState->close();
                            return $tage;
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return false;
    }


    public function writeOrUpdateAuByPnr($pnr, $tage) {
        if($this->readAuByPnr($pnr) !== false) {
            $sql = "UPDATE urlaub u
                        SET u.tage = ?
                        WHERE u.pnr = ?
                        AND u.status = 'K'
                        AND YEAR(u.beginn) = YEAR(NOW())";

            if (!$prepState = $this->dbConnect->prepare($sql)) {
                echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->bind_param("ii", $tage, $pnr)) {
                    echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    if (!$prepState->execute()) {
                        echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    } else {
                        $prepState->close();
                        return true;
                    }
                }
            }
        } else {
            $sql = 'INSERT INTO urlaub
                        (pnr, beginn, ende, tage, uart, status)
                        VALUES (?, CONCAT(YEAR(NOW()), "-01-01"), CONCAT(YEAR(NOW()), "-12-31"), ?, 2, "K")';

            if (!$prepState = $this->dbConnect->prepare($sql)) {
                echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->bind_param("ii", $pnr, $tage)) {
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
        }
        $prepState->close();
        return false;
    }








    public function readPending() {
        $urlaubList = array();
        $sql = "SELECT u.unr, u.pnr, u.beginn, u.ende, u.tage, u.uart, u.status
                    FROM urlaub u, mitarbeiter m, abteilung a
                    WHERE u.pnr = m.pnr
                    AND m.abtnr = a.abtnr
                    AND u.status = 'P'
                    ORDER BY a.abtnr";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->bind_result($unr, $pnr, $beginn, $ende, $tage, $uart, $status)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    while ($prepState->fetch()) {
                        $urlaub = new Urlaub($unr, $pnr, $beginn, $ende, $tage, $uart, $status);
                        $urlaubList[] = $urlaub;
                    }
                }

            }
        }
        $prepState->close();
        return $urlaubList;
    }

    /*
     * Einen Urlaubsantrag bewilligen
     * P = Pending
     * G = Genehmigt
     * A = Abgelehnt
     */
    public function genehmigen($unr) {
        $sql = "UPDATE urlaub SET status = 'G' WHERE unr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("i", $unr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }

    public function ablehnen($unr) {
        $sql = "UPDATE urlaub SET status = 'A' WHERE unr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("i", $unr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }

    /*
     * Update eines Urlaubs-Datensatzes anhand der UNR
     */
    public function update(Urlaub $urlaub) {
        $unr = $urlaub->getUnr();
        $pnr = $urlaub->getPnr();
        $beginn = $urlaub->getBeginn();
        $ende = $urlaub->getEnde();
        $tage = $urlaub->getTage();

        $sql = "UPDATE urlaub SET pnr = ?, beginn = ?, ende = ?, tage = ? WHERE unr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("issii", $pnr, $beginn, $ende, $tage, $unr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }

    /*
     * Löscht Urlaub anhand der UNR
     */
    public function deleteByUnr($unr) {

        $sql = "DELETE FROM urlaub WHERE unr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("i", $unr)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }
}

?>