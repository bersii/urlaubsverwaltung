<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Login">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.png" type="image/png">
    <link href="./css/style.css" rel="stylesheet" media="all">
    <link href='./fullcalendar/core/main.css' rel='stylesheet'/>
    <link href='./fullcalendar/daygrid/main.css' rel='stylesheet'/>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>

    <script src='./fullcalendar/core/main.js'></script>
    <script src='./fullcalendar/daygrid/main.js'></script>
    <script src='./fullcalendar/interaction/main.js'></script>
    <script src='./js/sorttable.js'></script>
    <!----------------------------------------------------------------------------------------------------------------------
       Initialisierung des Kalenders
    ----------------------------------------------------------------------------------------------------------------------->
    <script>document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid', 'interaction'],
                selectable: true,
                locale: "de",
                height: "auto",
                firstDay: 1,
                buttonText: {
                    today: "Heute"
                }
            });
            calendar.render();
        });
    </script>
    <title>Urlaubsverwaltung - BFW Hamburg</title>
</head>
<body>

<?php include('nav.php'); ?>

<div class="page">
    <div class="page-content">
        <div class="main">
            <div class="main-left">
                <div class="main-left-header">
                    <div class="title">
                        <h2>Betriebsurlaubsverwaltung</h2>
                    </div>
                </div>
                <div>
                    <br>
                    <p>Planen Sie Betriebsurlaube, die für alle Mitarbeiter des BfW-Hamburg verpflichtend zu nehmen
                        sind.</p>
                </div>
                <br>
                <div>
                    <?php
                    if(count($this->arrBetriebsurlaub) == 0) {
                        echo '<div class="no-info">
                                    <p>Noch kein Betriebsurlaub festgelegt.</p>
                              </div>';
                    }
                    else {
                        echo '<div>Nächster Betriebsurlaub:</div><br>';
                        foreach($this->arrBetriebsurlaub as $urlaub) {
                            echo '<div class="confVac-bu">
                                <div class="next-vac-blue bu">
                                </div>
                                    <div class="next-vac-grey bu">
                                        <div class="next-vac-top">
                                            <div class="next-vac-datum">' . date('d.m.Y', strtotime($urlaub->getBeginn())) .  '&nbsp;&nbsp;&nbsp;bis&nbsp;&nbsp;&nbsp;' .
                                                        date('d.m.Y', strtotime($urlaub->getEnde())) . '</div>
                                            <div class="' . $this->arrUrlaubsStatusCssClass[$urlaub->getStatus()] . '">
                                                <p class="status">' . $this->arrUrlaubsStatusSprache[$urlaub->getStatus()] . '</p>
                                            </div>
                                            <div>
                                                <form action="index.php?cmd=BetriebsurlaubVerwalten&unr=' . $urlaub->getUnr()  . '" method="POST">
                                                    <button name="antragLoeschen" type="submit" class="button deny"><i class="fas fa-times fa-m"></i></button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>';
                            }
                    }
                    ?>
                </div>
                <div class="plan-vac-button">
                    <button type="button" id="betriebsurlaubPlanen" class="button">Jetzt Urlaub planen</button>
                </div>
            </div>

            <div class="main-right">
                <div>
                    <h2>Die nächsten Feiertage:</h2>
                </div>
                <?php
                echo '<table>';
                echo '<tr><td>&nbsp;</td><td></td><td></td></tr>';
                foreach ($this->arrCurrentFeiertage as $bezeichnung => $datum) {
                echo '<tr>';
                echo '<td>' . $bezeichnung . ':&nbsp;&nbsp;&nbsp;</td><td>' . $this->arrWochentage[date('w', $datum)] . ',&nbsp;&nbsp;</td><td>' . date('d.m.Y', $datum) . '</td>';
                echo '</tr>';
                }
                echo '<tr><td>&nbsp;</td><td></td><td></td></tr>';
                foreach ($this->arrNextFeiertage as $bezeichnung => $datum) {
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
            <?php include ("footer.php"); ?>
        </div>
    </div>
</div>

<!----------------------------------------------------------------------------------------------------------------------
    BETRIEBSURLAUB PLANEN - Modal Box
----------------------------------------------------------------------------------------------------------------------->
<div id="modalBox" class="modal">
    <div class="modal-content">
        <div class="modal-head">
            <div>
                <h2>Betriebsurlaub planen</h2>
            </div>
            <div>
                <span class="close">&times;</span>
            </div>
        </div>

        <form action="index.php?cmd=BetriebsurlaubVerwalten" method="POST">
            <div class="modal-form">
                <div class="modal-labels">
                    <div>
                        <label>Beginn (am/vom):</label>
                    </div>
                    <div>
                        <label>Ende (bis):</label>
                    </div>
                </div>
                <div class="modal-inputs">
                    <div>
                        <input type="date" class="modal-input" name="beantragen_beginn" value="<?= $this->beantragenBeginnVorbelegen ?>" required>
                    </div>
                    <div>
                        <input type="date" class="modal-input" name="beantragen_ende" value="<?= $this->beantragenEndeVorbelegen ?>" required>
                    </div>
                </div>
            </div>
            <div>
                <div class="modal-buttons">
                    <input type="submit" id="send" class="button" value="Urlaub planen" name="beantragen_submit">
                    <button type="button" class="button cancel">Abbrechen</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="./js/main.js"></script>
<script src="./js/modalBox.js"></script>
</body>
</html>