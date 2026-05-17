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
                        html += '<form action="../controllers/TrackingController.php?action=delete_job" method="POST" onsubmit="return confirm(\'Close this job listing?\');">';
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
