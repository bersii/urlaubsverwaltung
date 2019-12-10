/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Fixierte Navigation am oberen Bildschrimrand beim scrollen
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
const nav = document.querySelector('#mainNav');
window.addEventListener("scroll", ()=>fixNav());

function fixNav() {
    if (window.scrollY >= nav.parentElement.previousElementSibling.offsetHeight) {
        nav.classList.add("fixedNav");
        // document.body.classList.add("fixedNav");
        nav.parentElement.classList.add("fixedNav");
    }
    else {
        nav.classList.remove("fixedNav");
        nav.parentElement.classList.remove("fixedNav");
    }
    // console.log("window.scrollY:" + window.scrollY);
    // console.log("nav.offsetTop:" + nav.offsetTop);
    // console.log("nav.offsetHeight:" + nav.offsetHeight);
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Fügt jedem Link der Nav einen Over-Effekt hinzu
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
const navLinks = document.getElementsByClassName("nav-links");

for (let i = 0; i < navLinks.length; i++) {
    navLinks[i].addEventListener("mouseover", ()=>mouseOver(navLinks[i]));
    navLinks[i].addEventListener("mouseout", ()=>mouseOut(navLinks[i]));
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Fügt Buttons einen Over Effekt hinzu
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
const buttons = document.getElementsByClassName("button");

for (let i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener("mouseover", ()=>mouseOver(buttons[i]));
    buttons[i].addEventListener("mouseout", ()=>mouseOut(buttons[i]));
}

function mouseOver(selectedElement) {
    selectedElement.classList.add("mouseOver");
}

function mouseOut(selectedElement) {
    selectedElement.classList.remove("mouseOver");
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
* Farbliche Hinterlegung der aktiven Seite
* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
let url = window.location;
// console.log("URL: " + url);
let urlObj = new URL(url);
let cmd = urlObj.searchParams.get('cmd');
// console.log("CMD:" + cmd);
let active = document.getElementById(cmd);
// console.log(active);
if (cmd == "BetriebsurlaubVerwalten") {
    document.getElementById("Verwaltung").classList.add("active");
}
if(active) {
    active.classList.add("active");
}