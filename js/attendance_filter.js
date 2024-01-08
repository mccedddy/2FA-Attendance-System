document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('attendanceTable');
    const tbody = table.querySelector('tbody');
    const dateFilter = document.getElementById('date');

    fetchAttendance(dateFilter.value);

    // Initial sort
    sortTable();

    // Get the original data from the table
    const originalData = Array.from(tbody.rows).map(row => {
        return {
            name: row.cells[0].innerText,
            student_number: row.cells[1].innerText,
            room: row.cells[2].innerText,
            time: row.cells[3].innerText,
            date: row.cells[4].innerText,
        };
    });

    // Initial filter
    filterTable(originalData);

    // Event listeners
    document.getElementById('startTime').addEventListener('change', () => {filterTable(originalData);});
    document.getElementById('endTime').addEventListener('change', () => {filterTable(originalData);});
    dateFilter.addEventListener('change', () => {fetchAttendance(dateFilter.value);})

    console.log('=== END OF STARTUP ===');
});

// For attendance
function fetchAttendance(date) {
    var url = '../includes/fetch_attendance.php';
    var formData = new FormData();
    formData.append('date', date);

    fetch(url, { 
        method: 'POST', 
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        displayAttendanceData(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayAttendanceData(data) {
    const table = document.getElementById('attendanceTable');
    const tbody = table.querySelector('tbody');

    // Clear existing rows from the table
    tbody.innerHTML = '';

    // Append new rows based on the fetched data
    data.forEach(rowData => {
        const row = document.createElement('tr');
        const cells = Object.values(rowData).map(value => {
            const cell = document.createElement('td');
            cell.innerText = value;
            return cell;
        });
        cells.forEach(cell => {
            row.appendChild(cell);
        });
        tbody.appendChild(row);
    });
}

// For table sort
function compareDateTime(a, b) {
    const dateComparison = new Date(b.cells[4].innerText + ' ' + b.cells[3].innerText) - new Date(a.cells[4].innerText + ' ' + a.cells[3].innerText);
    return dateComparison;
}

function sortTable() {
    const table = document.getElementById('attendanceTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort(compareDateTime);
    tbody.innerHTML = '';

    rows.forEach(row => {
        tbody.appendChild(row);
    });
}

// For table filter
function filterTable(originalData) {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const table = document.getElementById('attendanceTable');
    const tbody = table.querySelector('tbody');

    // Filter original data based on time range
    const filteredData = originalData.filter(row => {
        const time = row.time;
        return time >= startTime && time <= endTime;
    });

    // Clear existing rows from the table
    tbody.innerHTML = '';

    // Append new rows based on the filtered data
    filteredData.forEach(rowData => {
        const row = document.createElement('tr');
        const cells = Object.values(rowData).map(value => {
            const cell = document.createElement('td');
            cell.innerText = value;
            return cell;
        });

        cells.forEach(cell => {
            row.appendChild(cell);
        });

        tbody.appendChild(row);
    });
}