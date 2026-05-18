function SearchJobs() {
    let q = document.getElementById("search_keyword").value;

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("job_list").innerHTML = this.responseText;
        }else{
			document.getElementById("job_list").innerHTML = this.status;
		}
    };

    xhttp.open("GET", "../controllers/SearchController.php?q="+q, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

function FilterSearchJobs() {
    let q = document.getElementById("search_keyword").value;
	let category = document.getElementById("filter_category").value;
    let job_type = document.getElementById("filter_type").value;
    let location = document.getElementById("filter_location").value;
    let salary_range = document.getElementById("filter_salary").value;

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("job_list").innerHTML = this.responseText;
        }else{
			document.getElementById("job_list").innerHTML = this.status;
		}
    };

    xhttp.open("GET", "../controllers/SearchController.php?category_id="+category+ "&job_type="+job_type+"&location="+location+"&salary_range="+salary_range, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}