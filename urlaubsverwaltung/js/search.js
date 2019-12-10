document.getElementById("searchbar").addEventListener("keyup", function() {
    let search, filter, table, tr, td, i, a, txtValue;
    search = document.getElementById("searchbar");
    filter = search.value.toUpperCase();
    table = document.getElementById("managementTable");
    td = document.getElementsByTagName("td");
    tr = document.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            }
            else {
                tr[i].style.display = "none";
            }
        }
    }
});