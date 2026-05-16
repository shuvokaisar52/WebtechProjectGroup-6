document.addEventListener('DOMContentLoaded', function() {
    const jobSelector = document.getElementById('job-selector');
    const appTableBody = document.querySelector('#applications-table tbody');
    const funnelChartCtx = document.getElementById('funnelChart');
    let funnelChart = null;

    if (jobSelector) {
        jobSelector.addEventListener('change', function() {
            const jobId = this.value;
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
        fetch(`../controllers/TrackingController.php?action=get_applications&job_id=${jobId}`)
            .then(response => response.json())
            .then(data => {
                appTableBody.innerHTML = '';
                if (data.length === 0) {
                    appTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">No applications found for this job</td></tr>';
                    return;
                }

                data.forEach(app => {
                    const row = document.createElement('tr');
                    row.id = `app-row-${app.id}`;
                    row.innerHTML = `
                        <td>${app.seeker_name}</td>
                        <td>${app.headline || 'N/A'}</td>
                        <td>${new Date(app.created_at).toLocaleDateString()}</td>
                        <td><div class="cover-letter-preview" title="${app.cover_letter}">${app.cover_letter.substring(0, 50)}...</div></td>
                        <td><a href="../uploads/${app.resume_path}" class="btn-link" target="_blank">Download Resume</a></td>
                        <td>
                            <select class="status-dropdown" onchange="updateStatus(${app.id}, this.value)">
                                <option value="Submitted" ${app.status === 'Submitted' ? 'selected' : ''}>Submitted</option>
                                <option value="Reviewed" ${app.status === 'Reviewed' ? 'selected' : ''}>Reviewed</option>
                                <option value="Shortlisted" ${app.status === 'Shortlisted' ? 'selected' : ''}>Shortlisted</option>
                                <option value="Rejected" ${app.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                            </select>
                            <span class="badge badge-${app.status.toLowerCase()}" id="badge-${app.id}">${app.status}</span>
                        </td>
                    `;
                    appTableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error loading applications:', error);
                showAlert('Error loading applications', 'error');
            });
    }

    window.updateStatus = function(appId, newStatus) {
        fetch(`../controllers/TrackingController.php?action=update_status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: appId, status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById(`badge-${appId}`);
                badge.className = `badge badge-${newStatus.toLowerCase()}`;
                badge.innerText = newStatus;
                
                const jobId = document.getElementById('job-selector').value;
                loadFunnelChart(jobId);
                
                showAlert('Status updated successfully', 'success');
            } else {
                showAlert('Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            showAlert('Network error occurred', 'error');
        });
    };

    function loadFunnelChart(jobId) {
        fetch(`../controllers/TrackingController.php?action=get_stats&job_id=${jobId}`)
            .then(response => response.json())
            .then(data => {
                const labels = Object.keys(data);
                const values = Object.values(data);

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
            });
    }

    function showAlert(message, type) {
        const alert = document.getElementById('alert-message');
        alert.innerText = message;
        alert.className = `alert alert-${type}`;
        alert.style.display = 'block';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 3000);
    }
});
