<?php
namespace classes\mapper;

use classes\model\Mitarbeiter;
use classes\model\Login;

class MitarbeiterDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    /*
     * Legt einen neuen Mitarbeiter in der Datenbank an
     */
    public function create(Mitarbeiter $mitarbeiter) {
        $anrede = $mitarbeiter->getAnrede();
        $titel = $mitarbeiter->getTitel();
        $vorname = $mitarbeiter->getVorname();
        $nachname = $mitarbeiter->getNachname();
        $abtnr = $mitarbeiter->getAbtnr();
        $funktion = $mitarbeiter->getFunktion();
        $durchwahl = $mitarbeiter->getDurchwahl();
        $email = $mitarbeiter->getEmail();
        $uges = $mitarbeiter->getUges();
        $username = $mitarbeiter->getUsername();
        $bstufe = $mitarbeiter->getBstufe();

        $sql = "INSERT INTO mitarbeiter (anrede, titel, vorname, nachname, abtnr, funktion, durchwahl, email, uges, username, bstufe, password) VALUES (?,?,?,?,?,?,?,?,?,?,?,'dummy')";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("ssssiissisi", $anrede, $titel, $vorname, $nachname, $abtnr, $funktion, $durchwahl, $email, $uges, $username, $bstufe)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    $prepState->close();
                    $login = new Login($username, DEFAULT_PWD);
                    $loginDAO = new LoginDAO();
                    $loginDAO->setNewPassword($login);
                    return true;
                }
            }
        }
        $prepState->close();
        return false;
    }

    /*
     * Erzeugt ein Mitarbeiter-Objekt anhand des usernames
     */
    public function readByUsername($username) {
        $sql = "SELECT m.vorname, m.nachname, pnr, m.abtnr, a.name, m.funktion, m.bstufe, m.uges, m.durchwahl, m.email, m.anrede, m.titel, m.letzter_login
        FROM mitarbeiter m, abteilung a
        WHERE m.abtnr = a.abtnr
            AND m.username = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("s", $username)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            }
            else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    if (!$prepState->bind_result($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $durchwahl, $email, $anrede, $titel, $letzterLogin)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        if ($prepState->fetch()) {
                            $mitarbeiter = new Mitarbeiter($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $durchwahl, $email, $anrede, $titel, $letzterLogin);
                            $prepState->free_result();
                            $prepState->close();
                            return $mitarbeiter;
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
     * Erzeugt ein Mitarbeiter-Objekt anhand der PNR
     */
    public function readBypnr($pnr) {
        $sql = "SELECT m.vorname, m.nachname, pnr, m.abtnr, a.name, m.funktion, m.bstufe, m.uges, m.durchwahl, m.email, m.anrede, m.titel, m.letzter_login, m.username
        FROM mitarbeiter m, abteilung a
        WHERE m.abtnr = a.abtnr
            AND m.pnr = ?";

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
                    if (!$prepState->bind_result($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $durchwahl, $email, $anrede, $titel, $letzterLogin, $username)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    }
                    else {
                        if (!$prepState->fetch()) {
                            echo "Fehler! Fetchen nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                        }
                        else {
                            $mitarbeiter = new Mitarbeiter($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $durchwahl, $email, $anrede, $titel, $letzterLogin, $username);
                            $prepState->free_result();
                            $prepState->close();
                            return $mitarbeiter;
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
     * Erzeugt ein Array mit Mitarbeiter-Objekten, aller Mitarbeiter
     */
    public function readAll() {
        $sql = "SELECT m.vorname, m.nachname, m.pnr, m.abtnr, a.name, m.funktion, m.bstufe, m.uges, m.email, m.username
                    FROM mitarbeiter m, abteilung a
                    WHERE m.abtnr = a.abtnr
                        AND m.username != 'betriebsurlaub'
                    ORDER BY m.nachname";
        $mitarbeiterList = array();

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->execute()) {
                echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->bind_result($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $email, $username)) {
                    echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                }
                else {
                    while ($prepState->fetch()) {
                        $mitarbeiter = new Mitarbeiter($vorname, $nachname, $pnr, $abtnr, $abtName, $funktion, $bstufe, $uges, $email, $username);
                        $mitarbeiterList[] = $mitarbeiter;
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return $mitarbeiterList;
    }

    /*
     * Updatet Mitarbeiter-Daten
     */
    public function update(Mitarbeiter $mitarbeiter) {
        $vorname = $mitarbeiter->getVorname();
        $nachname = $mitarbeiter->getNachname();
        $anrede = $mitarbeiter->getAnrede();
        $titel = $mitarbeiter->getTitel();
        $abtnr = $mitarbeiter->getAbtnr();
        $funktion = $mitarbeiter->getFunktion();
        $durchwahl = $mitarbeiter->getDurchwahl();
        $email = $mitarbeiter->getEmail();
        $uges = $mitarbeiter->getUges();
        $bstufe = $mitarbeiter->getBstufe();
        $pnr = $mitarbeiter->getPnr();

        $sql = "UPDATE mitarbeiter SET vorname = ?, nachname = ?, anrede = ?, titel = ?, abtnr = ?, funktion = ?, durchwahl = ?, email = ?, uges = ?, bstufe = ? WHERE pnr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("ssssiissiii", $vorname, $nachname, $anrede, $titel, $abtnr, $funktion, $durchwahl, $email, $uges, $bstufe, $pnr)) {
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
     * Löscht Mitarbeiter anhand seiner pnr
     */
    public function delete(Mitarbeiter $mitarbeiter) {
        $pnr = $mitarbeiter->getPnr();
        $sql = "DELETE FROM mitarbeiter WHERE pnr = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        } else {
            if (!$prepState->bind_param("i", $pnr)) {
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
     * Setzt letzte Login-Zeit
     */
	public function setLastLoginTime($username) {
		$sql = "UPDATE mitarbeiter
					SET letzter_login = NOW()
					WHERE username = ?";
		if(!$preStmt = $this->dbConnect->prepare($sql)){
			echo "Fehler bei SQL-Vorbereitung (" . $this->dbConnect->errno . ")" . $this->dbConnect->error ."<br>";
		} else {
			if(!$preStmt->bind_param("s", $username)){
				echo "Fehler beim Binding (" . $this->dbConnect->errno . ")" . $this->dbConnect->error ."<br>";
			} else {
				if(!$preStmt->execute()){
					echo "Fehler beim Ausführen (" . $this->dbConnect->errno . ")" . $this->dbConnect->error ."<br>";
                }
			}
			$preStmt->close();
		}
	}
}

?>