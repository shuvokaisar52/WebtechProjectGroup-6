function saveJob(btn, job_id){
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            if(this.responseText == "success"){
                if(btn.innerHTML == "♡"){
                    btn.innerHTML = "❤️";
                } else {
                    btn.innerHTML = "♡";
                }
            } else {
                alert("Can't bookmark the job.");
            }
        }else{
			alert("Error.");
		}
    };

    xhttp.open("POST", "../controllers/SaveJobController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("job_id="+job_id);
}

function removeSaved(job_id){
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            location.reload();
        }else{
			alert("Error.");
		}
    };

    xhttp.open("POST", "../controllers/SaveJobController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("job_id="+job_id);
}