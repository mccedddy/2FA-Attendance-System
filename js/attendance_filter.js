document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('attendanceTable');
    var tbody = table.querySelector('tbody');
    var dateFilter = document.getElementById('date');

    // Declare originalData outside the fetchAttendance function
    var originalData = [];

    // Fetch attendance data on page load
    fetchAttendance(dateFilter.value);

    // Initial sort
    sortTable();

    // Initial filter
    filterTable(originalData);

    // Event listeners
    document.getElementById('startTime').addEventListener('change', () => { filterTable(originalData); });
    document.getElementById('endTime').addEventListener('change', () => { filterTable(originalData); });
    dateFilter.addEventListener('change', () => {
        fetchAttendance(dateFilter.value);
    });

    console.log('=== END OF STARTUP ===');

    function updateOriginalData(newData) {
        originalData = newData;
        console.log('Original Data updated:', originalData);
    
        // Perform necessary operations after updating originalData
        sortTable();
        filterTable(originalData);
    
        // After sorting and filtering, update the displayed table
        displayAttendanceData(originalData);
    }

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
                updateOriginalData(data);
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
});