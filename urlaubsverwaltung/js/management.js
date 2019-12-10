let names = document.getElementById("names");
let depart = document.getElementById("depart");
let days = document.getElementById("days");
names.addEventListener("click", ()=>{sortTable(0)});
depart.addEventListener("click", ()=>{sortTable(1)});
days.addEventListener("click", ()=>{sortTable(2)});
function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, direction, switchcount = 0;
    table = document.getElementById("managementTable");
    switching = true;
    direction = "asc";

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            console.log("Number" + Number(x.firstElementChild.innerHTML));

            if (n == 2) {
                if (direction == "asc") {
                    if (Number(x.firstElementChild.innerHTML) > Number(y.firstElementChild.innerHTML)) {
                        console.log("Nummer" + Number(x.firstElementChild.innerHTML));
                        shouldSwitch = true;
                        break;
                    }
                }
                else if (direction == "desc") {
                    if (Number(x.firstElementChild.innerHTML) < Number(y.firstElementChild.innerHTML)) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            else if (direction == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
            else if (direction == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i+1], rows[i]);
            switching = true;
            switchcount++;
        }
        else {
            if (switchcount == 0 && direction == "asc") {
                direction = "desc";
                switching = true;
            }
        }
    }
}
