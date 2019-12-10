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

    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <script src='./fullcalendar/core/main.js'></script>
    <script src='./fullcalendar/daygrid/main.js'></script>
    <script src='./fullcalendar/interaction/main.js'></script>


    <script>document.addEventListener('DOMContentLoaded', function() {

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'dayGrid', 'interaction' ],
                selectable: true,
                locale: "de",
                height: "auto",
                firstDay: 1,
                buttonText: {
                    today: "Heute"
                },
                events: [
                    <?php
                    foreach($this->arrObjUrlaub as $key => $urlaub) {
                        echo '{';
                        echo 'backgroundColor:';
                        echo ($urlaub->getStatus() == "P") ? '"#eaad36",':(($urlaub->getStatus() == "A") ? '"#f51010",':'"#4CAF50",');
                        echo 'title:"' . $this->arrVorname[$key] . ' ' . $this->arrNachname[$key] . '",';
                        echo 'start:"' . $urlaub->getBeginn() . '",';
                        /*Enddatum formatierung in timestamp + 86400 (Sekunden eines ganzen Tages) und Rückkonvertierung
                        in String für die Ausgabe im Kalendar. Ein tag muss adiert werden, damit der Kalendar auch den
                        letzten Tag mit anzeigt.*/
                        echo 'end:"' . date('Y-m-d', strtotime($urlaub->getEnde())+86400) . '",';
                        echo 'allDay: true,';
                        echo '},';
                    }

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
                ],

            });
            calendar.render();
        });</script>
    <title>Urlaubsverwaltung - BFW Hamburg</title>
</head>
<body>

<?php include('nav.php');?>

<div class="page">
    <div class="page-content">
        <div class="main">
            <div class="main-left">
                <?php
                    if(count($this->arrObjUrlaub) == 0) {
                        echo '<div>
                                <div class="no-info">
                                    <p>Keine Anträge vorhanden.</p>
                                </div>
                              </div>';
                    }
                    else {
                        foreach ($this->arrObjUrlaub as $key=>$urlaub) {
                            echo '<div class="approveVac">
                                    <div class="approveVac-right">
                                        <div class="approveVac-name">
                                            ' . $this->arrVorname[$key] . ' ' . $this->arrNachname[$key] .
                                            ' – ' . $this->arrAbteilungen[$key] .'
                                        </div>
                                        <div class="approveVac-date">
                                            ' . date('d.m.Y', strtotime($urlaub->getBeginn())) .
                                            '&nbsp;&nbsp;&nbsp;bis&nbsp;&nbsp;&nbsp;' .
                                            date('d.m.Y', strtotime($urlaub->getEnde())) . '
                                        </div>
                                        <div class="approveVac-lower">
                                            <div class="approveVac-days">
                                                ' . $urlaub->getTage() . ' Tage
                                            </div>
                                            <form action="index.php?cmd=UrlaubGenehmigen&unr=' . $urlaub->getUnr() .'" method="POST">
                                                <div class="approveVac-buttons">

                                                    <div>
                                                        <button type="submit" class="button approve" name="btnApprove"><i class="fas fa-check fa-m"></i></button>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="button deny" name="btnDeny"><i class="fas fa-times fa-m"></i></button>
                                                    </div>
                                                    <div class="editVac-button">
                                                        <button type="button" id="' . $urlaub->getUnr() . '" class="button edit"><i class="fas fa-highlighter fa-m"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>';
                            }
                        }
                    ?>
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
        Aufbaue der MODAL BOX
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Urlaubstage verwalten</h2>
            </div>
            <div>
                <span class="close">&times;</span>
            </div>
        </div>
        <form action="index.php?cmd=UrlaubGenehmigen" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Mitarbeiter:</label>
                    </div>
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
                    <input type="hidden" class="modal-input" name="urlaub_genehm_unr">
                    <div>
                        <input type="text" class="modal-input" name="name" readonly>
                    </div>
                    <div>
                        <input type="date" class="modal-input" name="beginn" readonly>
                    </div>
                    <div>
                        <input type="date" class="modal-input" name="ende" readonly>
                    </div>
                    <div>
                        <input type="number" class="modal-input" name="tage">
                    </div>
                </div>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send" class="button" name="tage_aendern_submit" value="Speichern">
                    <button type="button" class="button cancel">Abbrechen</button>
                </div>
            </div>
        </form>
        <div id="modal_verwaltung_aendern_meldung" class="meldung">

        </div>
    </div>
</div>

<script src="./js/main.js"></script>
<script src="./js/modalBox.js"></script>
</body>
</html>