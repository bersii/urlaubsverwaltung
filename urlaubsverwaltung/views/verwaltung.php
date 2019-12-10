<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Login">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.png" type="image/png">
    <link href="./css/style.css" rel="stylesheet" media="all">
    <link href='./fullcalendar/core/main.css' rel='stylesheet' />
    <link href='./fullcalendar/daygrid/main.css' rel='stylesheet' />

    <script src='./fullcalendar/core/main.js'></script>
    <script src='./fullcalendar/daygrid/main.js'></script>
    <script src='./fullcalendar/interaction/main.js'></script>
    <script src='./js/sorttable.js'></script>

    <title>Urlaubsverwaltung - BFW Hamburg</title>
</head>
<body>

<?php include('nav.php');?>

<div class="page">
    <div class="page-content">
        <div class="main">
            <div class="main-left verwaltung">
                <div class="main-left-header">
                    <div class="title">
                        <h2>Mitarbeiterverwaltung</h2>
                    </div>
                    <div>
                        &nbsp;
                    </div>
                    <div class="searchbar">
                        <input type="text" id="searchbar" placeholder="Mitarbeiter suchen">
                        <span class="fa fa-search"></span>
                    </div>
                    <div class="new-ma">
                        <button type="button" id="mitarbeiterAnlegen" class="button">Neuen Mitarbeiter anlegen</button>
                    </div>
                </div>
                <br>
                <div class="ma-list">
                    <table id="managementTable" class="sortable">
                        <tr>
                            <th id="names" class="management">Name</th>
                            <th id="depart" class="management">Abteilung</th>
                            <th id="uges" class="management uges">Gesamturlaub</th>
                            <th id="currYear" class="management currYear">Resturlaub (<?= date("Y")?>)</th>
                            <th id="nextYear" class="management nextYear">Resturlaub (<?= date("Y", strtotime("+1 year"))?>)</th>
                        </tr>
                        <?php
                            foreach($this->arrObjMitarbeiter as $objMitarbeiter) {
                                echo '<tr id="' . $objMitarbeiter->getPnr() . '" class="management ma-row">
                                          <td class="management"><a class="ma-links">' . $objMitarbeiter->getNachname() . ', ' . $objMitarbeiter->getVorname() . '</a></td>
                                          <td class="management"><a class="ma-links">' . $objMitarbeiter->getAbtName() . '</a></td>
                                          <td class="management uges"><a class="ma-links">' . $objMitarbeiter->getUges() . '</a></td>
                                          <td class="management currYear"><a class="ma-links">' . $this->arrRestUrlaubCurrentYear[$objMitarbeiter->getPnr()] . '</a></td>
                                          <td class="management nextYear"><a class="ma-links">' . $this->arrRestUrlaubNextYear[$objMitarbeiter->getPnr()] . '</a></td>
                                      </tr>';
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="footer">
            <?php include("footer.php"); ?>
        </div>
    </div>
</div>

<!----------------------------------------------------------------------------------------------------------------------
        MITARBEITERVERWALTUNG - Mitarbeiter bearbeiten - Modal Box
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Mitarbeiter verwalten</h2>
            </div>
            <div>
                <span id="maaendern-close" class="close">&times;</span>
            </div>
        </div>
        <form id="form_maaendern" action="index.php?cmd=Verwaltung" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Anrede:</label>
                    </div>
                    <div>
                        <label>Titel:</label>
                    </div>
                    <div>
                        <label>Vorname:</label>
                    </div>
                    <div>
                        <label>Nachname:</label>
                    </div>
                    <div>
                        <label>Abteilung:</label>
                    </div>
                    <div>
                        <label>Funktion:</label>
                    </div>
                    <div>
                        <label>Berechtigungsstufe:</label>
                    </div>
                    <div>
                        <label>Durchwahl:</label>
                    </div>
                    <div>
                        <label>E-Mail:</label>
                    </div>
                    <div>
                        <label>Urlaube:</label>
                    </div>
                    <div>
                        <label>Tage AU im Url. <?= date('Y')  ?>:</label>
                    </div>
                    <div>
                        <label>Urlaubstage (gesamt):</label>
                    </div>
                    <div>
                        <label>Resturlaub <?= date('Y')  ?>:</label>
                    </div>
                    <div>
                        <label>Resturlaub <?= date('Y', strtotime('+1 year'))  ?>:</label>
                    </div>
                    <div>
                        <label>Nutzername:</label>
                    </div>
                    <div>
                        <label>Standard Passwort:</label>
                    </div>
                </div>
                <div class="modal-inputs">
                    <input type="hidden" class="modal-input" name="verw_ma_aendern_pnr">
                    <div>
                        <select class="modal-input" name="anrede" required>
                            <option value="m">Herr</option>
                            <option value="w">Frau</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="titel">
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="vorname" required>
                    </div>
                    <div>
                        <input type="text" class="modal-input"name="nachname" required>
                    </div>
                    <div>
                        <select class="modal-input" name="abteilung" required>
                            <?php
                                foreach($this->arrObjAbteilung as $objAbteilung) {
                                    echo '<option value="' . $objAbteilung->getAbtnr() . '">' . $objAbteilung->getName() . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                        <select class="modal-input" name="funktion" required>
                        <?php
                                foreach($this->arrObjFunktion as $objFunktion) {
                                    echo '<option value="' . $objFunktion->getFnr() . '">' . $objFunktion->getFbez() . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                    <select class="modal-input" name="bstufe" required disabled>
                        <option value="1">1: Mein Urlaub</option>
                        <option value="2">2: Mein Urlaub, Verwaltung</option>
                        <option value="3">3: Mein Urlaub, Verwaltung, genehmigen</option>
                    </select>
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="durchwahl">
                    </div>
                    <div>
                        <input type="email" class="modal-input" name="email">
                    </div>
                    <div>
                        <span class="modal_no_edit"></span>
                    </div>
                    <div>
                        <input type="number" min="0" class="modal-input" name="aucurrent">
                    </div>
                    <div>
                        <input type="number" min="0" class="modal-input" name="uges">
                    </div>
                    <div>
                        <span class="modal_no_edit"></span>
                    </div>
                    <div>
                        <span class="modal_no_edit"></span>
                    </div>
                    <div>
                        <span class="modal_no_edit"></span>
                    </div>
                    <div>
                        <input type="checkbox" name="st_pwd">
                    </div>
                </div>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send" class="button" name="verw_ma_aendern_submit" value="Speichern">
                    <button id="maaendern_anlegen_abbrechen" type="button" class="button cancel">Abbrechen</button>
                </div>
            </div>
        </form>
        <div id="modal_verwaltung_aendern_meldung" class="meldung">

        </div>
        <div id="maaendern_OK_div" class="modal-buttons">
            <button type="button" id="maaendern_OK" class="button ok-button">OK</button>
        </div>
    </div>
</div>

<!----------------------------------------------------------------------------------------------------------------------
        MITARBEITERVERWALTUNG - Neuen Mitarbeiter anlegen Modal Box
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Neuen Mitarbeiter anlegen</h2>
            </div>
            <div>
                <span id="maa-close" class="close-MAA">&times;</span>
            </div>
        </div>
        <form id="form_maa" action="index.php?cmd=Verwaltung" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Anrede:</label>
                    </div>
                    <div>
                        <label>Titel:</label>
                    </div>
                    <div>
                        <label>Vorname:</label>
                    </div>
                    <div>
                        <label>Nachname:</label>
                    </div>
                    <div>
                        <label>Abteilung:</label>
                    </div>
                    <div>
                        <label>Funktion:</label>
                    </div>
                    <div>
                        <label>Berechtigungsstufe:</label>
                    </div>
                    <div>
                        <label>Durchwahl:</label>
                    </div>
                    <div>
                        <label>E-Mail:</label>
                    </div>
                    <div>
                        <label>Urlaubstage (gesamt):</label>
                    </div>
                    <div>
                        <label>Nutzername:</label>
                    </div>
                </div>
                <div class="modal-inputs">
                    <input type="hidden" class="modal-input" name="verw_ma_aendern_pnr">
                    <div>
                        <select class="modal-input" name="anrede_maa" required>
                            <option value=""></option>
                            <option value="m">Herr</option>
                            <option value="w">Frau</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="titel_maa">
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="vorname_maa" required>
                    </div>
                    <div>
                        <input type="text" class="modal-input"name="nachname_maa" required>
                    </div>
                    <div>
                        <select class="modal-input" name="abteilung_maa" required>
                            <option value=""></option>
                            <?php
                                foreach($this->arrObjAbteilung as $objAbteilung) {
                                    echo '<option value="' . $objAbteilung->getAbtnr() . '">' . $objAbteilung->getName() . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                        <select class="modal-input" name="funktion_maa" required>
                            <option value=""></option>
                        <?php
                                foreach($this->arrObjFunktion as $objFunktion) {
                                    echo '<option value="' . $objFunktion->getFnr() . '">' . $objFunktion->getFbez() . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                    <select class="modal-input" name="bstufe_maa" required <?= ($_SESSION['bstufe'] >= 3)?"":"disabled" ?>>
                        <option value="1">1: Mein Urlaub</option>
                        <option value="2">2: Mein Urlaub, Verwaltung</option>
                        <option value="3">3: Mein Urlaub, Verwaltung, genehmigen</option>
                    </select>
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="durchwahl_maa">
                    </div>
                    <div>
                        <input type="email" class="modal-input" name="email_maa">
                    </div>
                    <div>
                        <input type="number" class="modal-input" name="uges_maa">
                    </div>
                    <div>
                        <input type="text" class="modal-input" name="nutzername_maa">
                    </div>
                </div>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send_maa" class="button" name="verw_ma_anlegen_submit" value="Speichern">
                    <button type="button" id="maa_anlegen_abbrechen" class="button cancel-MAA">Abbrechen</button>
                </div>
            </div>
        </form>
        <div id="modal_verwaltung_maa_meldung" class="meldung">

        </div>
        <div id="maa_OK_div" class="modal-buttons">
            <button type="button" id="maa_OK" class="button ok-button">OK</button>
        </div>
    </div>
</div>

<script src="./js/main.js"></script>
<script src="./js/modalBox.js"></script>
<script src="./js/search.js"></script>
</body>
</html>