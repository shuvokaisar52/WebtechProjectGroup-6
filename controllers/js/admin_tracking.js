document.addEventListener('DOMContentLoaded', function () {
    var categorySelect = document.getElementById('admin-category-filter');
    var statusSelect = document.getElementById('admin-status-filter');
    var clearBtn = document.getElementById('admin-clear-filter');
    var tableBody = document.querySelector('.table-container table tbody');

    function loadFilteredJobs() {
        var categoryId = categorySelect.value;
        var status = statusSelect.value;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../controllers/TrackingController.php?action=get_admin_jobs&category=' + categoryId + '&status=' + status, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center">No jobs found</td></tr>';
                    return;
                }

                for (var i = 0; i < data.length; i++) {
                    var job = data[i];
                    var row = document.createElement('tr');

                    var html = '<td>' + escapeHtml(job.title) + '</td>';
                    html += '<td>' + escapeHtml(job.employer_name) + '</td>';
                    html += '<td>' + escapeHtml(job.category_name) + '</td>';
                    html += '<td><span class="badge badge-' + job.status + '">' + job.status + '</span></td>';
                    html += '<td>';

                    if (job.status === 'active') {
                        html += '<form class="close-job-form" action="../controllers/TrackingController.php?action=delete_job" method="POST">';
                        html += '<input type="hidden" name="job_id" value="' + job.id + '">';
                        html += '<button type="submit" class="btn btn-danger">Close Job</button>';
                        html += '</form>';
                    } else {
                        html += '<span style="color: var(--text-muted); font-style: italic;">No actions</span>';
                    }

                    html += '</td>';
                    row.innerHTML = html;
                    tableBody.appendChild(row);
                }
            }
        };
        xhr.send();
    }

    if (categorySelect && statusSelect) {
        categorySelect.addEventListener('change', loadFilteredJobs);
        statusSelect.addEventListener('change', loadFilteredJobs);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            categorySelect.value = '';
            statusSelect.value = '';
            loadFilteredJobs();
        });
    }

    document.addEventListener('submit', function (e) {
        if (e.target && e.target.nodeName === 'FORM' && e.target.classList.contains('close-job-form')) {
            e.preventDefault();
            if (confirm('Close this job listing?')) {
                var jobId = e.target.querySelector('input[name="job_id"]').value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../controllers/TrackingController.php?action=delete_job', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            var res = JSON.parse(xhr.responseText);
                            if (res.success) {
                                loadFilteredJobs();
                            } else {
                                alert('Error closing job');
                            }
                        } else {
                            alert('An error occurred.');
                        }
                    }
                };
                xhr.send('job_id=' + encodeURIComponent(jobId));
            }
        }
    });

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
             .toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
});
