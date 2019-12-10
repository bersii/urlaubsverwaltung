<?php
namespace classes\mapper;

use classes\model\Login;

class LoginDAO {
    private $dbConnect;

    public function __construct() {
        $this->dbConnect = SQLDAOFactory::getInstance();
    }

    // Überprüft on es zu dem Usernamen ein Passwort gibt und ob die Hashes übereinstimmen:
    public function checkLogin(Login $login) {
        $username = $login->getUsername();
        $password = $login->getPassword();
        $passwordHash = NULL;

        $sql = "SELECT password FROM mitarbeiter WHERE username = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("s", $username)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    if (!$prepState->bind_result($passwordHash)) {
                        echo "Fehler! Ergebnis-Binding nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                    } else {
                        if ($prepState->fetch()) {
                            // Passworteingabe mit Hash in DB vergleichen:
                            if(password_verify($password . PEPPER, $passwordHash)) {
                                // Passwort neu hashen falls Änderungen an Hash-Methode:
                                if(password_needs_rehash($passwordHash, PASSWORD_DEFAULT)) {
                                    $this->setNewPassword($login);
                                }
                                $prepState->free_result();
                                $prepState->close();
                                return true;
                            }
                        }
                    }
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return false;
    }

    // Setzen eines neuen Passworts:
    public function setNewPassword(Login $login) {
        $username = $login->getUsername();
        $password = $login->getPassword();
        $passwordHash = password_hash($password . PEPPER, PASSWORD_DEFAULT);

        $sql = "UPDATE mitarbeiter
                    SET password = ?
                    WHERE username = ?";

        if (!$prepState = $this->dbConnect->prepare($sql)) {
            echo "Fehler! SQL-Vorbereitung nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
        }
        else {
            if (!$prepState->bind_param("ss", $passwordHash, $username)) {
                echo "Fehler! SQL-Abfrage nicht möglich (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
            } else {
                if (!$prepState->execute()) {
                    echo "Fehler beim Ausführen! (" . $this->dbConnect->errno . ":" . $this->dbConnect->error . ")<br>";
                } else {
                    $prepState->free_result();
                    $prepState->close();
                    return true;
                }
            }
        }
        $prepState->free_result();
        $prepState->close();
        return false;
    }

}

?>