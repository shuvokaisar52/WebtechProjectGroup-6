document.addEventListener('DOMContentLoaded', function() {
    var jobSelector = document.getElementById('job-selector');
    var appTableBody = document.querySelector('#applications-table tbody');
    var funnelChartCtx = document.getElementById('funnelChart');
    var funnelChart = null;

    if (jobSelector) {
        jobSelector.addEventListener('change', function() {
            var jobId = this.value;
            if (jobId) {
                loadApplications(jobId);
                loadFunnelChart(jobId);
            } else {
                appTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">Please select a job to see applications</td></tr>';
                if (funnelChart) funnelChart.destroy();
            }
        });
    }

    function loadApplications(jobId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../controllers/TrackingController.php?action=get_applications&job_id=' + jobId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    appTableBody.innerHTML = '';
                    
                    if (data.length === 0) {
                        appTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">No applications found for this job</td></tr>';
                        return;
                    }

                    for (var i = 0; i < data.length; i++) {
                        var app = data[i];
                        var row = document.createElement('tr');
                        row.id = 'app-row-' + app.id;
                        
                        var html = '<td>' + app.seeker_name + '</td>';
                        html += '<td>' + (app.headline || 'N/A') + '</td>';
                        html += '<td>' + new Date(app.created_at).toLocaleDateString() + '</td>';
                        html += '<td><div class="cover-letter-preview" title="' + app.cover_letter + '">' + app.cover_letter.substring(0, 50) + '...</div></td>';
                        html += '<td><a href="../public/uploads/' + app.resume_path + '" class="btn-link" target="_blank">Download Resume</a></td>';
                        html += '<td>';
                        html += '<select class="status-dropdown" onchange="updateStatus(' + app.id + ', this.value)">';
                        html += '<option value="Submitted" ' + (app.status === 'Submitted' ? 'selected' : '') + '>Submitted</option>';
                        html += '<option value="Reviewed" ' + (app.status === 'Reviewed' ? 'selected' : '') + '>Reviewed</option>';
                        html += '<option value="Shortlisted" ' + (app.status === 'Shortlisted' ? 'selected' : '') + '>Shortlisted</option>';
                        html += '<option value="Rejected" ' + (app.status === 'Rejected' ? 'selected' : '') + '>Rejected</option>';
                        html += '</select>';
                        html += '<span class="badge badge-' + app.status.toLowerCase() + '" id="badge-' + app.id + '">' + app.status + '</span>';
                        html += '</td>';
                        
                        row.innerHTML = html;
                        appTableBody.appendChild(row);
                    }
                } else {
                    showAlert('Error loading applications', 'error');
                }
            }
        };
        xhr.send();
    }

    window.updateStatus = function(appId, newStatus) {
        var xhr = new XMLHttpRequest();
        xhr.open('PUT', '../controllers/TrackingController.php?action=update_status', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                var data = JSON.parse(xhr.responseText);
                if (xhr.status === 200 && data.success) {
                    var badge = document.getElementById('badge-' + appId);
                    badge.className = 'badge badge-' + newStatus.toLowerCase();
                    badge.innerText = newStatus;
                    
                    var jobId = document.getElementById('job-selector').value;
                    loadFunnelChart(jobId);
                    
                    showAlert('Status updated successfully', 'success');
                } else {
                    showAlert(data.error || 'Failed to update status', 'error');
                }
            }
        };
        xhr.send(JSON.stringify({ id: appId, status: newStatus }));
    };

    function loadFunnelChart(jobId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../controllers/TrackingController.php?action=get_stats&job_id=' + jobId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                var labels = [];
                var values = [];
                
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        labels.push(key);
                        values.push(data[key]);
                    }
                }

                if (funnelChart) funnelChart.destroy();

                funnelChart = new Chart(funnelChartCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Application Count',
                            data: values,
                            backgroundColor: ['blue', 'orange', 'green', 'red'],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Application Funnel' }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }
        };
        xhr.send();
    }

    function showAlert(message, type) {
        var alert = document.getElementById('alert-message');
        alert.innerText = message;
        alert.className = 'alert alert-' + type;
        alert.style.display = 'block';
        setTimeout(function() {
            alert.style.display = 'none';
        }, 3000);
    }
});
