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
    <link href='./fullcalendar/interaction/main.css' rel='stylesheet' />

    <script src='./fullcalendar/core/main.js'></script>
    <script src='./fullcalendar/daygrid/main.js'></script>
    <script src='./fullcalendar/interaction/main.js'></script>
    <!----------------------------------------------------------------------------------------------------------------------
        Initialisierung des Kalendars
    ----------------------------------------------------------------------------------------------------------------------->
    <script>document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'dayGrid', 'interaction' ],
                selectable: true,
                locale: "de",
                height: "auto",
                firstDay: 1,
                events: [
                    <?php

                    foreach($this->arrBetriebsurlaub as $betriebsUrlaub) {
                        echo '{';
                        echo 'backgroundColor: "#EA5B0C",';
                        // echo ($urlaub->getStatus() == "P") ? '"#eaad36",':(($urlaub->getStatus() == "A") ? '"#f51010",':'"#4CAF50",');
                        echo 'title:"Betriebsurlaub",';
                        echo 'start:"' . $betriebsUrlaub->getBeginn() . '",';
                        /*Enddatum formatierung in timestamp + 86400 (Sekunden eines ganzen Tages) und Rückkonvertierung
                        in String für die Ausgabe im Kalendar. Ein tag muss adiert werden, damit der Kalendar auch den
                        letzten Tag mit anzeigt.*/
                        echo 'end:"' . date('Y-m-d', strtotime($betriebsUrlaub->getEnde())+86400) . '",';
                        echo 'allDay: true,';
                        echo '},';
                    }

                    foreach($this->arrCurrentFeiertage as $bezeichnung => $datum) {
                        echo '{';
                        echo 'backgroundColor: "#fb9d9d",';
                        echo 'title:"' . $bezeichnung . '",';
                        echo 'start:"' . date("Y-m-d", $datum) . '",';
                        echo 'rendering: "background",';
                        echo '},';
                    }
                    foreach($this->arrNextFeiertage as $bezeichnung => $datum) {
                        echo '{';
                        echo 'backgroundColor: "#fb9d9d",';
                        echo 'title:"' . $bezeichnung . '",';
                        echo 'start:"' . date("Y-m-d", $datum) . '",';
                        echo 'rendering: "background",';
                        echo '},';
                    }
                    ?>
                ]
            });
            calendar.render();
        });
    </script>
    <title>Urlaubsverwaltung - BFW Hamburg</title>
</head>
<body>

<?php include('nav.php');?>

<div class="page">
    <div class="page-content">
        <div class="main">
            <div class="main-left">
                <p>
                   <h2>Hallo <?= $this->vorname ?> </h2><br>
                   <span class="plan-vac-button">
                       <button type="button" id="passwortAendern" class="button">Passwort Ändern</button><br>
                       <span class="meldung"><?= $this->meldungPw ?><br></span>
                    </span>
                </p>
                <div class="mandatoryPwChange">
                    <p></p>
                </div>

                <div class="siteContainer-content">
                    <div>
                    </div>
                </div>
                <div class="meldung"><br><?= $this->meldung ?><br></div>
            </div>

            <div class="main-right">
                <div><h2>Betriebsurlaub:</h2></div>
                <?php
                    if(count($this->arrBetriebsurlaub) == 0) {
                        echo '<br>Noch kein Betriebsurlaub festgelegt.<br>';
                    } else {
                        echo '<table>';
                        echo '<tr><td>&nbsp;</td><td></td><td></td></tr>';
                        foreach($this->arrBetriebsurlaub as $betriebsurlaub) {
                            $buBeginn = strtotime($betriebsurlaub->getBeginn());
                            $buEnde = strtotime($betriebsurlaub->getEnde());
                            if(date('Y', $buBeginn) === date('Y')) {
                                echo '<tr>';
                                echo '<td>' . $this->arrWochentage[date('w', $buBeginn)] . ',&nbsp;</td><td>' . date('d.m.Y', $buBeginn) .
                                        '</td><td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;</td><td>' . $this->arrWochentage[date('w', $buEnde)] . ',&nbsp;</td><td>' . date('d.m.Y', $buEnde) . '</td>';
                                echo '</tr>';
                            }
                        }
                        echo '<td>&nbsp;</td><td></td><td></td><td></td><td></td>';
                        foreach($this->arrBetriebsurlaub as $betriebsurlaub) {
                            $buBeginn = strtotime($betriebsurlaub->getBeginn());
                            $buEnde = strtotime($betriebsurlaub->getEnde());
                            if(date('Y', $buBeginn) === date('Y', strtotime('+1 year'))) {
                                echo '<tr>';
                                echo '<td>' . $this->arrWochentage[date('w', $buBeginn)] . ',</td><td>' . date('d.m.Y', $buBeginn) .
                                        '</td><td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;</td><td>' . $this->arrWochentage[date('w', $buEnde)] . ',</td><td>' . date('d.m.Y', $buEnde) . '</td>';
                                echo '</tr>';
                            }
                        }
                        echo '</table>';
                    }
                ?>
                    <br>
                <div><h2>Die nächsten Feiertage:</h2></div>
                    <?php
                        echo '<table>';
                        echo '<tr><td>&nbsp;</td><td></td><td></td></tr>';
                        foreach($this->arrCurrentFeiertage as $bezeichnung => $datum) {
                            echo '<tr>';
                            echo '<td>' . $bezeichnung . ':&nbsp;&nbsp;&nbsp;</td><td>' . $this->arrWochentage[date('w', $datum)] . ',&nbsp;&nbsp;</td><td>' . date('d.m.Y', $datum) . '</td>';
                            echo '</tr>';
                        }
                        echo '<tr><td>&nbsp;</td><td></td><td></td></tr>';
                        foreach($this->arrNextFeiertage as $bezeichnung => $datum) {
                            echo '<tr>';
                            echo '<td>' . $bezeichnung . ':&nbsp;&nbsp;&nbsp;</td><td>' . $this->arrWochentage[date('w', $datum)] . ', </td><td>' . date('d.m.Y', $datum) . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    ?>
                <div id='calendar'></div>
            </div>
        </div>
        <div class="footer">
            <?php include("footer.php"); ?>
        </div>
    </div>
</div>

<!----------------------------------------------------------------------------------------------------------------------
        Aufbauen der MODAL BOX Urlaub planen
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Urlaub planen</h2>
            </div>
            <div>
                <span class="close">&times;</span>
            </div>
        </div>

        <form id="form_urlaub_einreichen" action="index.php?cmd=MeinUrlaub" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Beginn (am/vom):</label>
                    </div>
                    <div>
                        <label>Ende (bis):</label>
                    </div>
                    <div>
                        <label>Tage:</label>
                    </div>
                </div>
                <div class="modal-inputs">
                    <div>
                        <input type="date" class="modal-input" id="beantragen_beginn" name="beantragen_beginn" value="<?= $this->beantragenBeginnVorbelegen ?>" required>
                    </div>
                    <div>
                        <input type="date" class="modal-input" id="beantragen_ende" name="beantragen_ende" value="<?= $this->beantragenEndeVorbelegen ?>" required>
                    </div>
                    <div>
                        <input type="number" class="modal-input" id="beantragen_tage" name="beantragen_tage" value="<?= $this->beantragenTageVorbelegen ?>" required>
                    </div>
                </div>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send" class="button" value="Einreichen" name="beantragen_submit">
                    <button type="button" id="beantragen_abbrechen" class="button cancel">Abbrechen</button>
                </div>
            </div>
        </form>
        <div id="modal_urlaub_planen_meldung" class="meldung">

        </div>
        <div id="beantragen_OK_div" class="modal-buttons">
            <button type="button" id="beantragen_OK" class="button ok-button">OK</button>
        </div>
    </div>
</div>

<!----------------------------------------------------------------------------------------------------------------------
        Aufbauen der MODAL BOX Passwort ändern
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Passwort ändern</h2>
            </div>
            <div>
                <span id="pw_aendern_close" class="close-PW">&times;</span>
            </div>
        </div>

        <form id="form_passwort_aendern" action="index.php?cmd=MeinUrlaub" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Altes Passwort:</label>
                    </div>
                    <div>
                        <label>Neues Passwort:</label>
                    </div>
                    <div>
                        <label>Neues Passwort wiederholen:</label>
                    </div>
                </div>
                <div class="modal-inputs">
                    <div>
                        <input type="password" class="modal-input" id="pw_aendern_alt" name="pw_aendern_alt" required>
                    </div>
                    <div>
                        <input type="password" class="modal-input" id="pw_aendern_neu" name="pw_aendern_neu" required>
                    </div>
                    <div>
                        <input type="password" class="modal-input" id="pw_aendern_neu_wh" name="pw_aendern_neu_wh" required>
                    </div>
                </div>
            </div>
            <div class="pw_info_text">
                Das neue Passwort darf nur aus Groß- und Kleinbuchstaben,
                Ziffern sowie den Zeichen&nbsp;&nbsp;&nbsp;-  ,  .  ;  :  _&nbsp;&nbsp;&nbsp;bestehen!
            </div>
            <div>
                <br>
                <label>Passwortstärke: [<span id="sicherheitsindex_ausgabe"></span><span></span>]<span><br>&nbsp;</span></label>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send_pw" class="button" value="Ändern" name="pw_aendern_submit">
                    <button type="button" id="pw_aendern_abbrechen" class="button cancel-PW">Abbrechen</button>
                </div>
            </div>
        </form>
        <div id="modal_pw_aendern_meldung" class="meldung">

        </div>
        <div id="pw_aendern_OK_div" class="modal-buttons">
            <button type="button" id="pw_aendern_OK" class="button ok-button">OK</button>
        </div>
    </div>
</div>

<script src="js/main.js"></script>
<script src="js/modalBox.js"></script>
</body>
</html>