/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* GLOBAL VARIABLES + Event Listeners
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
let modalBox = document.getElementsByClassName("modal");
let span = document.getElementsByClassName("close")[0];
let cancel = document.getElementsByClassName("cancel")[0];
let spanBU;
let cancelBU;
let spanPW;
let cancelPW;
let spanMAA;
let cancelMAA;
let maNames;
let urlaubPlanen;
let passwortAendern;
let planComVac;
let editVac;


span.addEventListener("click", ()=>{close()});
cancel.addEventListener("click", ()=>{close()});
if(spanBU = document.getElementsByClassName("close-BU")[0]) {
    spanBU.addEventListener("click", ()=>{closeBU()});
}
if(cancelBU = document.getElementsByClassName("cancel-BU")[0]) {
    cancelBU.addEventListener("click", ()=>{closeBU()});
}
if(spanPW = document.getElementsByClassName("close-PW")[0]) {
    spanPW.addEventListener("click", ()=>{closePW()});
}
if(cancelPW = document.getElementsByClassName("cancel-PW")[0]) {
    cancelPW.addEventListener("click", ()=>{closePW()});
}
if(spanMAA = document.getElementsByClassName("close-MAA")[0]) {
    spanMAA.addEventListener("click", ()=>{closeMAA()});
}
if(cancelMAA = document.getElementsByClassName("cancel-MAA")[0]) {
    cancelMAA.addEventListener("click", ()=>{closeMAA()});
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* NUTZER VERWALTUNG - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (maNames = document.getElementsByClassName("ma-row")) {
    for (let i = 0; i < maNames.length; i++) {
        maNames[i].addEventListener("click", (e)=>{openModalBoxMAAendern(e)});
    }
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* URLAUB PLANEN - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (urlaubPlanen = document.getElementById("urlaubPlanen")) {
    urlaubPlanen.addEventListener("click", ()=>{openModalBoxUP()});
}

function openModalBoxUP() {
    modalBox[0].style.display = "block";

    let buttonEinreichen = document.getElementById('send');
    let buttonAbbrechen = document.getElementById('beantragen_abbrechen');
    let formular = document.getElementById('form_urlaub_einreichen');
    let meldungAusgabe = document.getElementById('modal_urlaub_planen_meldung');
    let buttonOK = document.getElementById('beantragen_OK');
    let buttonOKdiv = document.getElementById('beantragen_OK_div');

    buttonOKdiv.style.display = "none";

    function einreichen() {
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        let formularDaten = new FormData(formular);

        formularDaten.append('isAjax', 'true');
        formularDaten.append('isUrlaubPlanenAjax', 'true');

        xhr.addEventListener("readystatechange", function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText === 'fehler') {
                    meldungAusgabe.innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                                                + 'oder anderer Fehler - '
                                                + 'bitte <a href="index.php">neu einloggen!</a>';
                } else {
                    if(xhr.responseText === 'ok') {
                        formular.style.display = "none";
                        span.style.display = "none";
                        buttonOKdiv.style.display = "";
                        buttonOK.addEventListener("click", function() {
                            location = "index.php?cmd=MeinUrlaub";
                        });
                        meldungAusgabe.innerHTML = '<br>Ihr Antrag wurde gespeichert';
                    } else {
                        meldungAusgabe.innerHTML = xhr.responseText;
                    }
                }
            }
        });
        xhr.open("POST", "index.php?cmd=MeinUrlaub", true);
        xhr.send(formularDaten);
    }

    function abbrechen() {
        buttonEinreichen.removeEventListener("click", einreichen);
        buttonAbbrechen.removeEventListener("click", abbrechen);
    }

    buttonEinreichen.addEventListener("click", einreichen);
    buttonAbbrechen.addEventListener("click", abbrechen);
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* PASSWORT ÄNDERN - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (passwortAendern = document.getElementById("passwortAendern")) {
    passwortAendern.addEventListener("click", ()=>{openModalBoxPW()});
}

function openModalBoxPW() {
    modalBox[1].style.display = "block";

    let buttonPwAendern = document.getElementById('send_pw');
    let buttonAbbrechenPw = document.getElementById('pw_aendern_abbrechen');
    let buttonClosePw = document.getElementById('pw_aendern_close');
    let formularPw = document.getElementById('form_passwort_aendern');
    let meldungAusgabePw = document.getElementById('modal_pw_aendern_meldung');
    let buttonOKPw = document.getElementById('pw_aendern_OK');
    let buttonOKPwdiv = document.getElementById('pw_aendern_OK_div');
    let inputPwNeu = document.getElementById('pw_aendern_neu');
    let sicherheitsindexAusgabe = document.getElementById('sicherheitsindex_ausgabe');

    buttonPwAendern.style.width ="125px";
    buttonOKPwdiv.style.display = "none";
    sicherheitsindexAusgabe.parentElement.style.fontStyle = "normal";
    sicherheitsindexAusgabe.parentElement.parentElement.style.textAlign = "center";
    for(let i=1;i<=90;i++) {
        sicherheitsindexAusgabe.nextElementSibling.innerHTML += '&nbsp;';
    }
    sicherheitsindexAusgabe.nextElementSibling.style.backgroundColor = "#ececec";
    sicherheitsindexAusgabe.nextElementSibling.style.fontSize = "11px";
    sicherheitsindexAusgabe.style.fontSize = "11px";
    let sicherheitsindex = 0;

    function pwAendern() {
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        let formularDatenPw = new FormData(formularPw);

        formularDatenPw.append('isAjax', 'true');
        formularDatenPw.append('isPasswortAendernAjax', 'true');

        xhr.addEventListener("readystatechange", function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText === 'fehler') {
                    meldungAusgabePw.innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                                                    + 'oder anderer Fehler - '
                                                    + 'bitte <a href="index.php">neu einloggen!</a>';
                } else {
                    if(xhr.responseText === 'ok') {
                        formularPw.style.display = "none";
                        spanPW.style.display = "none";
                        buttonOKPwdiv.style.display = "";
                        buttonOKPw.addEventListener("click", function() {
                            location = "index.php?cmd=MeinUrlaub";
                        });
                        meldungAusgabePw.innerHTML = '<br>Ihr Passwort wurde geändert.<br>Bitte loggen Sie sich erneut ein.';
                    } else {
                        meldungAusgabePw.innerHTML = xhr.responseText;
                    }
                }
            }
        });
        xhr.open("POST", "index.php?cmd=MeinUrlaub", true);
        xhr.send(formularDatenPw);
    }

    function abbrechenPw() {
        formularPw.reset();
        sicherheitsindexAusgabe.innerHTML = '';
        sicherheitsindexAusgabe.nextElementSibling.innerHTML = '';
        sicherheitsindex = 0;
        sicherheitsindexAusgabe.nextElementSibling.nextElementSibling.innerHTML = '';
        meldungAusgabePw.innerHTML = '';
        buttonPwAendern.removeEventListener("click", pwAendern);
        buttonAbbrechenPw.removeEventListener("click", abbrechenPw);
        buttonClosePw.removeEventListener("click", abbrechenPw);
        inputPwNeu.removeEventListener("input", calcSicherheitsindex);
    }

    function calcSicherheitsindex() {
        sicherheitsindex = inputPwNeu.value.length;
        if(inputPwNeu.value.match(/[a-z]/)) {
            sicherheitsindex += 3;
        }
        if(inputPwNeu.value.match(/[A-Z]/)) {
            sicherheitsindex += 3;
        }
        if(inputPwNeu.value.match(/[0-9]/)) {
            sicherheitsindex += 4;
        }
        if(inputPwNeu.value.match(/[-,.;:_]/)) {
            sicherheitsindex += 5;
        }
        sicherheitsindexAusgabe.innerHTML = '';
        for(let i=1;i<=sicherheitsindex;i++) {
            sicherheitsindexAusgabe.innerHTML += '&nbsp;&nbsp;&nbsp;';
            if(i >= 30) {
                break;
            }
        }
        sicherheitsindexAusgabe.nextElementSibling.innerHTML = '';
        for(let i=29;i>=sicherheitsindex;i--) {
            sicherheitsindexAusgabe.nextElementSibling.innerHTML += '&nbsp;&nbsp;&nbsp;';
        }
        if(sicherheitsindex >= 0 && sicherheitsindex < 10) {
            sicherheitsindexAusgabe.style.backgroundColor = "red";
            sicherheitsindexAusgabe.nextElementSibling.nextElementSibling.innerHTML = '<br>niedrige Sicherheit';
        } else {
            if(sicherheitsindex >= 10 && sicherheitsindex < 20) {
                sicherheitsindexAusgabe.style.backgroundColor = "yellow";
                sicherheitsindexAusgabe.nextElementSibling.nextElementSibling.innerHTML = '<br>mittlere Sicherheit';
            } else {
                if(sicherheitsindex >= 20) {
                    sicherheitsindexAusgabe.style.backgroundColor = "green";
                    sicherheitsindexAusgabe.nextElementSibling.nextElementSibling.innerHTML = '<br>hohe Sicherheit';
                }
            }
        }

    }

    buttonPwAendern.addEventListener("click", pwAendern);
    buttonAbbrechenPw.addEventListener("click", abbrechenPw);
    buttonClosePw.addEventListener("click", abbrechenPw);
    inputPwNeu.addEventListener("input", calcSicherheitsindex);
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* BETRIEBSURLAUB PLANEN - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (planComVac = document.getElementById("betriebsurlaubPlanen")) {
    planComVac.addEventListener("click", ()=>{openModalBoxBU()});
}

function openModalBoxBU() {
    modalBox[0].style.display = "block";
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* URLAUBSANTRAG EDITIEREN - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (editVac = document.getElementsByClassName("edit")) {
    for (let i = 0; i < editVac.length; i++) {
        editVac[i].addEventListener("click", (e)=>{openModalBoxEV(e)});
    }
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Mitarbeiter verwalten - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function openModalBoxMAAendern(e) {
    modalBox[0].style.display = "block";
    let currentPNR = e.currentTarget.getAttribute("id");
    let modalLabels = modalBox[0].firstElementChild.children[1].firstElementChild.firstElementChild.children;
    let modalInputs = modalBox[0].firstElementChild.children[1].firstElementChild.lastElementChild.children;

    // Formular per Ajax senden:
    let buttonMAAendernSpeichern = document.getElementById('send');
    let buttonMAAendernAbrechen = document.getElementById('maaendern_anlegen_abbrechen');
    let buttonCloseMAAendern = document.getElementById('maaendern-close');
    let formularMAAendern = document.getElementById('form_maaendern');
    let meldungAusgabeMAAendern = document.getElementById('modal_verwaltung_aendern_meldung');
    let buttonOKMAAendern = document.getElementById('maaendern_OK');
    let buttonOKMAAenderndiv = document.getElementById('maaendern_OK_div');

    buttonOKMAAenderndiv.style.display = "none";

    function maaendernSpeichern() {
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        let formularDatenMAAendern = new FormData(formularMAAendern);

        formularDatenMAAendern.append('isAjax', 'true');
        formularDatenMAAendern.append('isMAAendernAjax', 'true');

        xhr.addEventListener("readystatechange", function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText === 'fehler') {
                    meldungAusgabeMAAendern.innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                                                    + 'oder anderer Fehler - '
                                                    + 'bitte <a href="index.php">neu einloggen!</a>';
                } else {
                    if(xhr.responseText === 'ok') {
                        formularMAAendern.style.display = "none";
                        span.style.display = "none";
                        buttonOKMAAenderndiv.style.display = "";
                        buttonOKMAAendern.addEventListener("click", function() {
                            location = "index.php?cmd=Verwaltung";
                        });
                        meldungAusgabeMAAendern.innerHTML = '<br>Die Änderungen wurden eingetragen.';
                    } else {
                        meldungAusgabeMAAendern.innerHTML = xhr.responseText;
                    }
                }
            }
        });
        xhr.open("POST", "index.php?cmd=Verwaltung", true);
        xhr.send(formularDatenMAAendern);

    }

    function abbrechenMAAendern() {
        buttonMAAendernSpeichern.removeEventListener("click", maaendernSpeichern);
        buttonMAAendernAbrechen.removeEventListener("click", abbrechenMAAendern);
        buttonCloseMAAendern.removeEventListener("click", abbrechenMAAendern);
        meldungAusgabeMAAendern.innerHTML = '';
    }

    buttonMAAendernSpeichern.addEventListener("click", maaendernSpeichern);
    buttonMAAendernAbrechen.addEventListener("click", abbrechenMAAendern);
    buttonCloseMAAendern.addEventListener("click", abbrechenMAAendern);

    // Modalbox per Ajax vorbelegen:
    let xhr = new XMLHttpRequest();
    xhr.addEventListener("readystatechange", function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // console.log(xhr.responseText);
            if(xhr.responseText === 'fehler') {
                document.getElementById('modal_verwaltung_aendern_meldung').innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                                                                                        + 'oder anderer Fehler - '
                                                                                        + 'bitte <a href="index.php">neu einloggen!</a>';
            } else {
                let ObjValues = JSON.parse(xhr.responseText);
                modalInputs[0].value = currentPNR;
                modalInputs[1].firstElementChild.value = ObjValues.anrede;
                modalInputs[2].firstElementChild.value = ObjValues.titel;
                modalInputs[3].firstElementChild.value = ObjValues.vorname;
                modalInputs[4].firstElementChild.value = ObjValues.nachname;
                modalInputs[5].firstElementChild.value = ObjValues.abteilung;
                modalInputs[6].firstElementChild.value = ObjValues.funktion;
                modalInputs[7].firstElementChild.value = ObjValues.bstufe;
                if(ObjValues.current_bstufe >= 3) {
                    modalInputs[7].firstElementChild.disabled = false;
                } else {
                    modalInputs[7].firstElementChild.disabled = true;
                }
                modalInputs[8].firstElementChild.value = ObjValues.durchwahl;
                modalInputs[9].firstElementChild.value = ObjValues.email;
                let urlaube = Object.values(ObjValues.urlaube);
                let urlaubeString= '<table>';
                for(let urlaub of urlaube) {
                    urlaubeString += urlaub;
                }
                urlaubeString += '</table>';
                modalInputs[10].firstElementChild.innerHTML = urlaubeString;
                modalInputs[11].firstElementChild.value = (ObjValues.aucurrent>0)?ObjValues.aucurrent:0;
                modalInputs[12].firstElementChild.value = ObjValues.uges;
                modalInputs[13].firstElementChild.innerHTML = ObjValues.urestcurrent;
                modalInputs[14].firstElementChild.innerHTML = ObjValues.urestnext;
                modalInputs[15].firstElementChild.innerHTML = ObjValues.nutzername;
                if(ObjValues.pwdIsStandard === '1') {
                    modalInputs[16].firstElementChild.checked = true;
                    modalInputs[16].firstElementChild.disabled = true;
                } else {
                    modalInputs[16].firstElementChild.checked = false;
                    modalInputs[16].firstElementChild.disabled = false;
                }
                if(urlaube.length == 0) {
                    modalLabels[9].style.height = "20px";
                    modalInputs[10].style.height = "20px";
                    modalInputs[10].firstElementChild.innerHTML = 'Noch keine Einträge!';
                } else {
                    modalLabels[9].style.height = parseInt(modalInputs[10].firstElementChild.firstElementChild.offsetHeight) + "px";
                    modalInputs[10].style.height = parseInt(modalInputs[10].firstElementChild.firstElementChild.offsetHeight) + "px";
                }
            }
        }
    });
    xhr.open("POST", "index.php?cmd=Verwaltung", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("isAjax=true&ajaxPnr=" + e.currentTarget.getAttribute("id"));
}

function openModalBoxEV(e) {
    modalBox[0].style.display = "block";
    let currentUNR = e.currentTarget.getAttribute("id");
    let modalInputs = modalBox[0].firstElementChild.children[1].firstElementChild.lastElementChild.children;

    let xhr = new XMLHttpRequest();
    xhr.addEventListener("readystatechange", function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if(xhr.responseText === 'fehler') {
                document.getElementById('modal_verwaltung_aendern_meldung').innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                    + 'oder anderer Fehler - '
                    + 'bitte <a href="index.php">neu einloggen!</a>';
            }
            else {
                let ObjValues = JSON.parse(xhr.responseText);

                modalInputs[0].value = currentUNR;
                modalInputs[1].firstElementChild.value = ObjValues.name;
                modalInputs[2].firstElementChild.value = ObjValues.beginn;
                modalInputs[3].firstElementChild.value = ObjValues.ende;
                modalInputs[4].firstElementChild.value = ObjValues.tage;
            }
        }
    });
    xhr.open("POST", "index.php?cmd=UrlaubGenehmigen", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("isAjax=true&ajaxUnr=" + e.currentTarget.getAttribute("id"));
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Neuen Mitarbeiter anlegen - Modal Box
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if (mitarbeiterAnlegen = document.getElementById("mitarbeiterAnlegen")) {
    mitarbeiterAnlegen.addEventListener("click", ()=>{openModalBoxMAAnlegen()});
}

function openModalBoxMAAnlegen() {
    modalBox[1].style.display = "block";

    let buttonMAASpeichern = document.getElementById('send_maa');
    let buttonMAAAbrechen = document.getElementById('maa_anlegen_abbrechen');
    let buttonCloseMAA = document.getElementById('maa-close');
    let formularMAA = document.getElementById('form_maa');
    let meldungAusgabeMAA = document.getElementById('modal_verwaltung_maa_meldung');
    let buttonOKMAA = document.getElementById('maa_OK');
    let buttonOKMAAdiv = document.getElementById('maa_OK_div');

    buttonOKMAAdiv.style.display = "none";

    function maaSpeichern() {
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        let formularDatenMAA = new FormData(formularMAA);

        formularDatenMAA.append('isAjax', 'true');
        formularDatenMAA.append('isMAAAjax', 'true');

        xhr.addEventListener("readystatechange", function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText === 'fehler') {
                    meldungAusgabeMAA.innerHTML = '<br>Aufgrund von Inaktivität automatisch ausgeloggt<br>'
                                                    + 'oder anderer Fehler - '
                                                    + 'bitte <a href="index.php">neu einloggen!</a>';
                } else {
                    if(xhr.responseText === 'ok') {
                        formularMAA.style.display = "none";
                        spanMAA.style.display = "none";
                        buttonOKMAAdiv.style.display = "";
                        buttonOKMAA.addEventListener("click", function() {
                            location = "index.php?cmd=Verwaltung";
                        });
                        meldungAusgabeMAA.innerHTML = '<br>Mitarbeiter wurde erfolgreich angelegt.';
                    } else {
                        meldungAusgabeMAA.innerHTML = xhr.responseText;
                    }
                }
            }
        });
        xhr.open("POST", "index.php?cmd=Verwaltung", true);
        xhr.send(formularDatenMAA);

    }

    function abbrechenMAA() {
        buttonMAASpeichern.removeEventListener("click", maaSpeichern);
        buttonMAAAbrechen.removeEventListener("click", abbrechenMAA);
        buttonCloseMAA.removeEventListener("click", abbrechenMAA);
    }

    buttonMAASpeichern.addEventListener("click", maaSpeichern);
    buttonMAAAbrechen.addEventListener("click", abbrechenMAA);
    buttonCloseMAA.addEventListener("click", abbrechenMAA);
}

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* FUNCTIONS
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
function close() {
    modalBox[0].style.display = "none";
}


function closeBU() {
    modalBox[0].style.display = "none";
}


function closePW() {
    modalBox[1].style.display = "none";
}


function closeMAA() {
    modalBox[1].style.display = "none";
}