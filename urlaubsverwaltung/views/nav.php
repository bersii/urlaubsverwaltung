<div class="header">
    <div><h1>Urlaubsverwaltung</h1></div>
    <div><img src="./img/logo.svg" class="bfw-logo"></div>
</div>
<div class="nav-wrapper" style="height:80px;">
    <nav id="mainNav">
        <ul>
            <?php
                foreach($this->nav as $cmd) {
                    if($cmd === 'Logout' || $cmd === 'BetriebsurlaubVerwalten') {
                        continue;
                    }
                    echo '<li id="' . $cmd . '" class="nav-links '. $cmd . '"><a href="index.php?cmd=' . $cmd . '">' . ARR_DYN_NAV[$cmd] . '</a>';
                        if ($cmd === 'Verwaltung') {
                            echo '<div class="dropdown-content">
                                      <a href="index.php?cmd=Verwaltung">Mitarbeiter verwalten</a>
                                      <a href="index.php?cmd=BetriebsurlaubVerwalten">Betriebsurlaub verwalten</a>
                                  </div>';
                        }
                        if($cmd === 'UrlaubGenehmigen') {
                            echo '<span class="nav-links">&nbsp;(' . $_SESSION['pendingCount'] . ')</span>';
                        }
                    echo '</li>';
                }
            ?>
            <li class="nav-links"><a href="index.php?cmd=Logout">Logout</a></li>
        </ul>
    </nav>
</div>