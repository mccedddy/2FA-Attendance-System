document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('attendanceTable');
    const tbody = table.querySelector('tbody');

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

    console.log('haha orig');
    console.log(originalData, table, tbody);
    
    // Initial filter
    filterTable(originalData);

    // Event listeners
    document.getElementById('startTime').addEventListener('change', () => {filterTable(originalData);});
    document.getElementById('endTime').addEventListener('change', () => {filterTable(originalData);});

    console.log('=== END OF STARTUP ===');
});

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

    console.log('Original Data:');
    console.log(originalData);

    // Filter original data based on time range
    const filteredData = originalData.filter(row => {
        const time = row.time;
        return time >= startTime && time <= endTime;
    });

    console.log('Filtered Data:');
    console.log(filteredData);


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