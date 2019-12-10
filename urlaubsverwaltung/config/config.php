<?php

// Datenbankparameter: ********************************************************
define ('DB_SERVER', 'localhost');
define ('DB_USER', 'root');
define ('DB_PASSWORD', '');
define ('DB_DATABASE', 'urlaubsverwaltung');



// Login-System: **************************************************************
// Startseite wenn ausgeloggt:
define ('START_LOGGED_OUT', 'Login');

// Startseite wenn eingeloggt:
define ('START_LOGGED_IN', 'MeinUrlaub');

// Autologout nach Inaktivitaet in Minuten:
define ('TIMEOUT_IN_MINUTEN', 20);
define ('TIMEOUT_IN_SEKUNDEN', TIMEOUT_IN_MINUTEN * 60);

// Festlegen der zugänglichen Seiten je Berechtigungsstufe:
define ('ARR_BSTUFE1_COMMANDS', ['Logout', 'MeinUrlaub']);
define ('ARR_BSTUFE2_COMMANDS', ['Logout', 'MeinUrlaub', 'Verwaltung', 'BetriebsurlaubVerwalten']);
define ('ARR_BSTUFE3_COMMANDS', ['Logout', 'MeinUrlaub', 'Verwaltung', 'BetriebsurlaubVerwalten', 'UrlaubGenehmigen']);

// Link-Namen für dynamische Navigation:
define ('ARR_DYN_NAV', ['MeinUrlaub' => 'Mein Urlaub',
                        'Verwaltung' => 'Verwaltung',
                        'UrlaubGenehmigen' => 'Urlaub genehmigen']);



// Passwort-Parameter: ********************************************************
// Standard-Passwort:
define ('DEFAULT_PWD', 'Hamburg1');

// Passwort-Hash-Pepper:
define ('PEPPER', '1ql/P3jFn/rQvj0o9Dcu');

// Parameter für Rundenanzahl (noch nicht benutzt, vorbereitet für spätere Anpassungen)
define ('COST', 10); //

?>