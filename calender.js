let currentDate = new Date();
let selectedDate = null;

// Initialize calendar when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateClock();
    setInterval(updateClock, 1000);
    generateCalendar();
    setupEventListeners();
    populateEventSelector();
});

// Update clock display
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    const dateString = now.toLocaleDateString();
    document.getElementById('clock').innerHTML = `${timeString}<br>${dateString}`;
}

// Generate calendar grid
function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const monthYear = document.getElementById('monthYear');
    
    // Clear previous calendar
    calendar.innerHTML = '';
    
    // Update month/year display
    monthYear.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    // Create day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        dayHeader.textContent = day;
        calendar.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const firstDayWeekday = firstDay.getDay();
    const numDays = lastDay.getDate();
    
    // Add empty cells for days before first day of month
    for (let i = 0; i < firstDayWeekday; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'day empty';
        calendar.appendChild(emptyCell);
    }
    
    // Add day cells
    for (let day = 1; day <= numDays; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'day';
        
        // Add day number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = day;
        dayCell.appendChild(dayNumber);
        
        // Check if day has events
        const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
        const dayDateString = dayDate.toISOString().split('T')[0];
        const dayEvents = getEventsForDate(dayDateString);
        
        if (dayEvents.length > 0) {
            dayCell.classList.add('has-event');
            
            // Add events to day cell
            dayEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'event';
                
                // Style event based on type
                if (!event.is_single_day) {
                    if (event.is_start) {
                        eventDiv.classList.add('event-start');
                    } else if (event.is_end) {
                        eventDiv.classList.add('event-end');
                    } else {
                        eventDiv.classList.add('event-middle');
                    }
                }
                
                eventDiv.innerHTML = `
                    <div class="event-title">${event.title}</div>
                    <div class="event-instructor">${event.instructor}</div>
                    ${event.start_time ? `<div class="event-time">${event.start_time} - ${event.end_time}</div>` : ''}
                `;
                
                eventDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    populateEventForm(event);
                    showEventModal();
                });
                
                dayCell.appendChild(eventDiv);
            });
        }
        
        // Mark today
        const today = new Date();
        if (dayDate.toDateString() === today.toDateString()) {
            dayCell.classList.add('today');
        }
        
        // Add click event for day
        dayCell.addEventListener('click', function() {
            selectedDate = dayDate;
            document.querySelectorAll('.day').forEach(d => d.classList.remove('selected'));
            dayCell.classList.add('selected');
            clearForm();
            showEventModal();
        });
        
        calendar.appendChild(dayCell);
    }
}

// Get events for a specific date
function getEventsForDate(dateString) {
    return events.filter(event => event.date === dateString);
}

// Setup event listeners
function setupEventListeners() {
    // Navigation buttons
    document.querySelectorAll('.nav-btn').forEach((btn, index) => {
        btn.addEventListener('click', function() {
            if (index === 0) { // Previous month
                currentDate.setMonth(currentDate.getMonth() - 1);
            } else { // Next month
                currentDate.setMonth(currentDate.getMonth() + 1);
            }
            generateCalendar();
        });
    });
    
    // Event selector
    document.getElementById('eventSelector').addEventListener('change', function() {
        const selectedEventId = this.value;
        if (selectedEventId) {
            const event = events.find(e => e.id == selectedEventId);
            if (event) {
                populateEventForm(event);
                showEventModal();
            }
        }
    });
    
    // Modal close when clicking outside
    document.getElementById('eventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
}

// Populate event selector dropdown
function populateEventSelector() {
    const selector = document.getElementById('eventSelector');
    
    // Clear existing options except first one
    while (selector.children.length > 1) {
        selector.removeChild(selector.lastChild);
    }
    
    // Get unique events (remove duplicates from multi-day events)
    const uniqueEvents = events.filter(event => event.is_start || event.is_single_day);
    
    // Add events to selector
    uniqueEvents.forEach(event => {
        const option = document.createElement('option');
        option.value = event.id;
        option.textContent = `${event.title} - ${event.instructor}`;
        selector.appendChild(option);
    });
}

// Show event modal
function showEventModal() {
    document.getElementById('eventModal').style.display = 'flex';
    
    if (selectedDate) {
        const dateString = selectedDate.toISOString().split('T')[0];
        document.getElementById('startDate').value = dateString;
        document.getElementById('endDate').value = dateString;
    }
}

// Close modal
function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
    clearForm();
}

// Clear form
function clearForm() {
    document.getElementById('eventForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('eventId').value = '';
    document.getElementById('deleteEventId').value = '';
}

// Populate form with event data for editing
function populateEventForm(event) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('eventId').value = event.id;
    document.getElementById('deleteEventId').value = event.id;
    document.getElementById('courseName').value = event.title;
    document.getElementById('instructorName').value = event.instructor;
    document.getElementById('startDate').value = event.start_date;
    document.getElementById('endDate').value = event.end_date;
    document.getElementById('startTime').value = event.start_time || '';
    document.getElementById('endTime').value = event.end_time || '';
}

// Add event listener for cancel button
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.querySelector('button[onclick="closeModal()"]');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
});