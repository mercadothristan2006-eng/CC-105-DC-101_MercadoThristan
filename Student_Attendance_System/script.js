// API Base URL
const API_URL = './';

// Global data storage
let studentsData = [];
let sectionsData = [];
let attendanceData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    setTodayDate();
    loadInitialData();
});

// Tab Navigation
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Load data for the active tab
            loadTabData(targetTab);
        });
    });
}

// Load data based on active tab
function loadTabData(tab) {
    switch(tab) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'students':
            loadStudents();
            loadSections();
            break;
        case 'attendance':
            loadAttendanceByDate();
            break;
    }
}

// Load initial data
function loadInitialData() {
    loadDashboardData();
}

// Set today's date
function setTodayDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('attendanceDate').value = today;
    document.getElementById('attendanceDateInput').value = today;
}

// ===== DASHBOARD FUNCTIONS =====
async function loadDashboardData() {
    try {
        const response = await fetch(`${API_URL}READ.php?action=getTodayAttendanceSummary`);
        const result = await response.json();
        
        if (result.success) {
            displayStats(result.data);
            displaySummaryTable(result.data);
        } else {
            showAlert('Error loading dashboard data', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to load dashboard data', 'error');
    }
}

function displayStats(data) {
    const statsGrid = document.getElementById('statsGrid');
    
    // Calculate totals
    let totalStudents = 0;
    let totalPresent = 0;
    let totalAbsent = 0;
    let totalLate = 0;
    
    data.forEach(section => {
        totalStudents += parseInt(section.total_students);
        totalPresent += parseInt(section.present_count);
        totalAbsent += parseInt(section.absent_count);
        totalLate += parseInt(section.late_count);
    });
    
    const attendanceRate = totalStudents > 0 ? 
        ((totalPresent / totalStudents) * 100).toFixed(1) : 0;
    
    statsGrid.innerHTML = `
        <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">${totalStudents}</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--success-color);">
            <div class="stat-label">Present Today</div>
            <div class="stat-value" style="color: var(--success-color);">${totalPresent}</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--danger-color);">
            <div class="stat-label">Absent Today</div>
            <div class="stat-value" style="color: var(--danger-color);">${totalAbsent}</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="stat-label">Late Today</div>
            <div class="stat-value" style="color: var(--warning-color);">${totalLate}</div>
        </div>
        <div class="stat-card" style="border-left-color: var(--primary-color);">
            <div class="stat-label">Attendance Rate</div>
            <div class="stat-value">${attendanceRate}%</div>
        </div>
    `;
}

function displaySummaryTable(data) {
    const tbody = document.getElementById('summaryTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No data available</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(section => `
        <tr>
            <td><strong>${section.section_name}</strong></td>
            <td>${section.total_students}</td>
            <td><span class="status-badge status-present">${section.present_count}</span></td>
            <td><span class="status-badge status-absent">${section.absent_count}</span></td>
            <td><span class="status-badge status-late">${section.late_count}</span></td>
            <td>${section.not_marked_count}</td>
        </tr>
    `).join('');
}

// ===== STUDENT FUNCTIONS =====
async function loadStudents() {
    try {
        const response = await fetch(`${API_URL}READ.php?action=getAllStudents`);
        const result = await response.json();
        
        if (result.success) {
            studentsData = result.data;
            displayStudents(studentsData);
        } else {
            showAlert('Error loading students', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to load students', 'error');
    }
}

function displayStudents(students) {
    const grid = document.getElementById('studentsGrid');
    
    if (students.length === 0) {
        grid.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--text-secondary);">No students found</p>';
        return;
    }
    
    grid.innerHTML = students.map(student => `
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">${student.first_name} ${student.last_name}</div>
                    <div class="card-subtitle">${student.student_id}</div>
                </div>
                <span class="status-badge status-${student.status}">${student.status}</span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Section:</span>
                    <span class="info-value">${student.section_name}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Grade Level:</span>
                    <span class="info-value">${student.grade_level || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Room:</span>
                    <span class="info-value">${student.room_number || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Enrolled:</span>
                    <span class="info-value">${formatDate(student.enrollment_date)}</span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-primary btn-sm" onclick="openAttendanceModal('${student.student_id}', '${student.first_name} ${student.last_name}')">
                    ✓ Mark Attendance
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteStudent('${student.student_id}', '${student.first_name} ${student.last_name}')">
                    🗑️ Delete
                </button>
            </div>
        </div>
    `).join('');
}

async function loadSections() {
    try {
        const response = await fetch(`${API_URL}READ.php?action=getSections`);
        const result = await response.json();
        
        if (result.success) {
            sectionsData = result.data;
            populateSectionDropdowns();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function populateSectionDropdowns() {
    const filterSelect = document.getElementById('sectionFilter');
    const studentSelect = document.getElementById('studentSection');
    
    const options = sectionsData.map(section => 
        `<option value="${section.section_id}">${section.section_name}</option>`
    ).join('');
    
    filterSelect.innerHTML = '<option value="">All Sections</option>' + options;
    studentSelect.innerHTML = '<option value="">Select Section</option>' + options;
}

function openAddStudentModal() {
    document.getElementById('studentModalTitle').textContent = 'Add New Student';
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').removeAttribute('readonly');
    openModal('studentModal');
}

async function saveStudent() {
    const studentId = document.getElementById('studentId').value.trim();
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const sectionId = document.getElementById('studentSection').value;
    
    if (!studentId || !firstName || !lastName || !sectionId) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    // Validate student ID format
    const idPattern = /^\d{5}-\d{2}-\d{4}$/;
    if (!idPattern.test(studentId)) {
        showAlert('Invalid student ID format. Use: 00000-00-0000', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}CREATE.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'createStudent',
                student_id: studentId,
                first_name: firstName,
                last_name: lastName,
                section_id: sectionId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Student added successfully!', 'success');
            closeModal('studentModal');
            loadStudents();
        } else {
            showAlert(result.error || 'Failed to add student', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to add student', 'error');
    }
}

async function deleteStudent(studentId, studentName) {
    if (!confirm(`Are you sure you want to delete ${studentName}? This will also delete all attendance records.`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}DELETE.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'deleteStudent',
                student_id: studentId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(`${studentName} deleted successfully`, 'success');
            loadStudents();
        } else {
            showAlert(result.error || 'Failed to delete student', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to delete student', 'error');
    }
}

// ===== ATTENDANCE FUNCTIONS =====
async function loadAttendanceByDate() {
    const date = document.getElementById('attendanceDate').value;
    
    try {
        const response = await fetch(`${API_URL}READ.php?action=getAttendanceByDate&date=${date}`);
        const result = await response.json();
        
        if (result.success) {
            attendanceData = result.data;
            displayAttendanceTable(result.data);
        } else {
            showAlert('Error loading attendance', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to load attendance', 'error');
    }
}

function displayAttendanceTable(data) {
    const tbody = document.getElementById('attendanceTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No students found</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(record => `
        <tr>
            <td>${record.student_id}</td>
            <td><strong>${record.first_name} ${record.last_name}</strong></td>
            <td>${record.section_name}</td>
            <td>
                ${getStatusBadge(record.status)}
                ${record.notes ? `<br><small style="color: var(--text-secondary);">${record.notes}</small>` : ''}
            </td>
            <td>
                <button class="btn btn-success btn-sm" onclick="quickMarkAttendance('${record.student_id}', 'present')">✓</button>
                <button class="btn btn-danger btn-sm" onclick="quickMarkAttendance('${record.student_id}', 'absent')">✗</button>
                <button class="btn btn-warning btn-sm" onclick="quickMarkAttendance('${record.student_id}', 'late')">⏰</button>
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const statusMap = {
        'present': 'Present',
        'absent': 'Absent',
        'late': 'Late',
        'not_marked': 'Not Marked'
    };
    return `<span class="status-badge status-${status}">${statusMap[status] || status}</span>`;
}

function openAttendanceModal(studentId, studentName) {
    document.getElementById('attendanceStudentId').value = studentId;
    document.getElementById('attendanceStudentName').value = studentName;
    document.getElementById('attendanceForm').reset();
    document.getElementById('attendanceStudentId').value = studentId;
    document.getElementById('attendanceStudentName').value = studentName;
    setTodayDate();
    openModal('attendanceModal');
}

async function saveAttendance() {
    const studentId = document.getElementById('attendanceStudentId').value;
    const date = document.getElementById('attendanceDateInput').value;
    const status = document.getElementById('attendanceStatus').value;
    const notes = document.getElementById('attendanceNotes').value.trim();
    
    if (!studentId || !date || !status) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}CREATE.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'markAttendance',
                student_id: studentId,
                attendance_date: date,
                status: status,
                notes: notes || null
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Attendance marked successfully!', 'success');
            closeModal('attendanceModal');
            loadAttendanceByDate();
            loadDashboardData();
        } else {
            showAlert(result.error || 'Failed to mark attendance', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to mark attendance', 'error');
    }
}

async function quickMarkAttendance(studentId, status) {
    const date = document.getElementById('attendanceDate').value;
    
    try {
        const response = await fetch(`${API_URL}CREATE.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'markAttendance',
                student_id: studentId,
                attendance_date: date,
                status: status
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadAttendanceByDate();
            loadDashboardData();
        } else {
            showAlert(result.error || 'Failed to mark attendance', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to mark attendance', 'error');
    }
}

async function bulkMarkAttendance(status) {
    const date = document.getElementById('attendanceDate').value;
    const studentIds = attendanceData.map(record => record.student_id);
    
    if (studentIds.length === 0) {
        showAlert('No students to mark', 'error');
        return;
    }
    
    if (!confirm(`Mark all ${studentIds.length} students as ${status}?`)) {
        return;
    }
    
    let successCount = 0;
    
    for (const studentId of studentIds) {
        try {
            const response = await fetch(`${API_URL}CREATE.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'markAttendance',
                    student_id: studentId,
                    attendance_date: date,
                    status: status
                })
            });
            
            const result = await response.json();
            if (result.success) successCount++;
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    showAlert(`Marked ${successCount} students as ${status}`, 'success');
    loadAttendanceByDate();
    loadDashboardData();
}

// ===== SEARCH AND FILTER =====
document.getElementById('studentSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const filtered = studentsData.filter(student => 
        student.first_name.toLowerCase().includes(searchTerm) ||
        student.last_name.toLowerCase().includes(searchTerm) ||
        student.student_id.includes(searchTerm)
    );
    displayStudents(filtered);
});

document.getElementById('sectionFilter')?.addEventListener('change', function(e) {
    const sectionId = e.target.value;
    const filtered = sectionId ? 
        studentsData.filter(student => student.section_id == sectionId) :
        studentsData;
    displayStudents(filtered);
});

// ===== MODAL FUNCTIONS =====
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// ===== ALERT FUNCTIONS =====
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// ===== UTILITY FUNCTIONS =====
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}