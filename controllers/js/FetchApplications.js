function FetchApplications() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("application_list").innerHTML = this.responseText;
        }
    };

    xhttp.open("POST", "../controllers/ApplicationController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}