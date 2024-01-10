document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('attendanceTable');
    var tbody = table.querySelector('tbody');
    var dateFilter = document.getElementById('date');
    var roomFilter = document.getElementById('roomFilter');
    var searchInput = document.getElementById('search');
    var exportButton = document.getElementById('export');
    var originalData = [];

    // Fetch attendance data on page load
    fetchAttendance(dateFilter.value);

    // Initial sort
    sortTable();

    // Initial filter
    filterTable(originalData);

    // Event listeners
    document.getElementById('roomFilter').addEventListener('change', () => { filterTable(originalData); });
    searchInput.addEventListener('change', () => {filterTable(originalData); });
    document.getElementById('startTime').addEventListener('change', () => { filterTable(originalData); });
    document.getElementById('endTime').addEventListener('change', () => { filterTable(originalData); });
    dateFilter.addEventListener('change', () => {
        fetchAttendance(dateFilter.value);
    });

    console.log('=== END OF STARTUP ===');

    function updateOriginalData(newData) {
        originalData = newData;
    
        // Perform necessary operations after updating originalData
        sortTable();
        displayAttendanceData(originalData);
        filterTable(originalData);
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
            let row = document.createElement('tr');
            let cells = Object.values(rowData).map(value => {
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
        var startTime = document.getElementById('startTime').value;
        var endTime = document.getElementById('endTime').value;
        var selectedRoom = roomFilter.value;
        var searchValue = searchInput.value.toLowerCase();
        table = document.getElementById('attendanceTable');
        tbody = table.querySelector('tbody');

        // Filter original data based on time range, room, and search
        const filteredData = originalData.filter(row => {
            var time = row["TIME_FORMAT(a.time, '%H:%i')"];
            var room = row.room;
            var studentName = row['name'].toLowerCase();
            
            // Check if the search input is present in the student's name
            const isNameMatch = studentName.includes(searchValue);

            if (
                (selectedRoom === 'ALL' || room === selectedRoom) &&
                time >= startTime &&
                time <= endTime &&
                (searchValue === '' || isNameMatch)
            ) {
                return true;
            }
            return false;
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