/**
 * ============================================================================
 * FRONTEND JAVASCRIPT (script.js)
 * ============================================================================
 * 
 * Purpose:
 * Handles user interactions in the browser and connects with `api.php`
 * via asynchronous HTTP requests (`fetch`).
 * 
 * Flow for Students:
 * 1. User performs an action (types a task, clicks complete, clicks delete).
 * 2. JavaScript sends a request (`fetch`) to `api.php`.
 * 3. PHP executes the SQL query in MySQL and returns a JSON response.
 * 4. JavaScript updates the in-memory array (`items`) and re-renders the UI.
 */

// ----------------------------------------------------------------------------
// 1. DOM Element Selection
// ----------------------------------------------------------------------------
const form = document.querySelector('#add-form');
const input = document.querySelector('#new-item');
const list = document.querySelector('#items');
const emptyState = document.querySelector('#empty-state');
const emptyMessage = document.querySelector('#empty-message');
const itemCount = document.querySelector('#item-count');
const progressCopy = document.querySelector('#progress-copy');
const progressValue = document.querySelector('#progress-value');
const progressTrack = document.querySelector('#progress-track');
const progressFill = document.querySelector('#progress-fill');
const clearButton = document.querySelector('#clear-completed');
const filters = [...document.querySelectorAll('[data-filter]')];
const storageNote = document.querySelector('.storage-note');

// ----------------------------------------------------------------------------
// 2. Application State Variables
// ----------------------------------------------------------------------------
// `activeFilter`: Keeps track of active view ('all', 'active', or 'completed').
// `items`: In-memory array of task objects synced with MySQL database.
let activeFilter = 'all';
let items = [];

// Display formatted date (e.g. "Monday, October 10")
document.querySelector('#today').textContent = new Intl.DateTimeFormat(undefined, {
  weekday: 'long',
  month: 'long',
  day: 'numeric'
}).format(new Date());

// ----------------------------------------------------------------------------
// 3. Centralized API Request Helper Function
// ----------------------------------------------------------------------------
// A reusable function to make fetch() calls to `api.php`.
// It handles JSON formatting, headers, and friendly error messages for students.
async function apiRequest(data = null, method = 'POST') {
  try {
    const options = {
      method: method,
      headers: { 'Content-Type': 'application/json' }
    };
    
    // Attach payload when method is POST
    if (data && method === 'POST') {
      options.body = JSON.stringify(data);
    }
    
    const response = await fetch('api.php', options);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const result = await response.json();
    
    // If successful, show green database connection indicator
    if (storageNote) {
      storageNote.textContent = 'Connected to MySQL Database (XAMPP)';
      storageNote.style.color = '#267763';
    }
    
    return result;
  } catch (error) {
    console.error('API Error:', error);
    // Helpful debugging message if server is offline or opened via file://
    if (storageNote) {
      storageNote.textContent = '⚠️ Cannot connect to PHP backend. Ensure Apache & MySQL are running in XAMPP and open via http://localhost/...';
      storageNote.style.color = '#d96f58';
    }
    return null;
  }
}

// ----------------------------------------------------------------------------
// 4. READ: Fetch all items from Database on Page Load
// ----------------------------------------------------------------------------
async function loadItems() {
  const result = await apiRequest(null, 'GET');
  if (result && result.success && Array.isArray(result.items)) {
    items = result.items;
  }
  render();
}

// ----------------------------------------------------------------------------
// 5. RENDER: Draw the list of items onto the web page
// ----------------------------------------------------------------------------
function render() {
  // Filter items based on active tab ('all', 'active', 'completed')
  const visibleItems = items.filter(item => {
    if (activeFilter === 'active') return !item.completed;
    if (activeFilter === 'completed') return item.completed;
    return true;
  });
  
  // Calculate completed and remaining task counts
  const completedCount = items.filter(item => item.completed).length;
  const remainingCount = items.length - completedCount;
  const percentage = items.length ? Math.round((completedCount / items.length) * 100) : 0;

  // Clear existing list items before re-rendering
  list.replaceChildren();
  
  for (const item of visibleItems) {
    const row = document.createElement('li');
    row.className = `item-row${item.completed ? ' is-complete' : ''}`;

    // Check / complete button
    const checkButton = document.createElement('button');
    checkButton.className = 'check-button';
    checkButton.type = 'button';
    checkButton.dataset.action = 'toggle';
    checkButton.dataset.id = item.id;
    checkButton.setAttribute('aria-label', `${item.completed ? 'Mark' : 'Complete'} ${item.text}`);
    checkButton.setAttribute('aria-pressed', String(item.completed));
    
    const check = document.createElement('span');
    check.setAttribute('aria-hidden', 'true');
    check.textContent = item.completed ? '✓' : '';
    checkButton.append(check);

    // Task text (using textContent to prevent XSS vulnerability)
    const text = document.createElement('span');
    text.className = 'item-text';
    text.textContent = item.text;

    // Delete button (×)
    const deleteButton = document.createElement('button');
    deleteButton.className = 'delete-button';
    deleteButton.type = 'button';
    deleteButton.dataset.action = 'delete';
    deleteButton.dataset.id = item.id;
    deleteButton.setAttribute('aria-label', `Delete ${item.text}`);
    deleteButton.title = 'Delete item';
    deleteButton.textContent = '×';

    row.append(checkButton, text, deleteButton);
    list.append(row);
  }

  // Handle empty state displays
  emptyState.classList.toggle('is-visible', visibleItems.length === 0);
  if (!items.length) {
    emptyMessage.textContent = 'Nothing on your list yet. Start with one small thing.';
  } else if (activeFilter === 'active') {
    emptyMessage.textContent = 'All done for now. Enjoy the breathing room.';
  } else if (activeFilter === 'completed') {
    emptyMessage.textContent = 'No completed items just yet.';
  }

  // Update progress bar and counter indicators
  itemCount.textContent = `${remainingCount} ${remainingCount === 1 ? 'left' : 'left'}`;
  progressCopy.textContent = items.length ? `${completedCount} of ${items.length} completed` : 'A fresh start';
  progressValue.textContent = `${percentage}%`;
  progressFill.style.width = `${percentage}%`;
  progressTrack.setAttribute('aria-valuenow', String(percentage));
  clearButton.disabled = completedCount === 0;
}

// ----------------------------------------------------------------------------
// 6. CREATE: Add New Task Form Submit Event
// ----------------------------------------------------------------------------
form.addEventListener('submit', async event => {
  event.preventDefault();
  const text = input.value.trim();
  if (!text) return;

  // Send request to api.php to insert into MySQL
  const res = await apiRequest({ action: 'add', text });
  if (res && res.success && res.item) {
    // Add the newly created task to the top of the local items array
    items.unshift(res.item);
    input.value = '';
    
    // Switch filter back to 'all' so the new item is immediately visible
    activeFilter = 'all';
    filters.forEach(filter => {
      const selected = filter.dataset.filter === activeFilter;
      filter.classList.toggle('is-active', selected);
      filter.setAttribute('aria-pressed', String(selected));
    });
    
    render();
  }
  input.focus();
});

// ----------------------------------------------------------------------------
// 7. FILTER: Switch between All, To Do, and Done
// ----------------------------------------------------------------------------
filters.forEach(filter => {
  filter.addEventListener('click', () => {
    activeFilter = filter.dataset.filter;
    filters.forEach(button => {
      const selected = button === filter;
      button.classList.toggle('is-active', selected);
      button.setAttribute('aria-pressed', String(selected));
    });
    render();
  });
});

// ----------------------------------------------------------------------------
// 8. UPDATE & DELETE: Event Delegation for List Row Buttons
// ----------------------------------------------------------------------------
list.addEventListener('click', async event => {
  const button = event.target.closest('button[data-action]');
  if (!button) return;

  const { action, id } = button.dataset;
  const numId = parseInt(id, 10);

  // A. Toggle Complete / Incomplete
  if (action === 'toggle') {
    const res = await apiRequest({ action: 'toggle', id: numId });
    if (res && res.success) {
      items = items.map(item => item.id === numId ? { ...item, completed: !item.completed } : item);
      render();
    }
  } 
  // B. Delete Task
  else if (action === 'delete') {
    const res = await apiRequest({ action: 'delete', id: numId });
    if (res && res.success) {
      items = items.filter(item => item.id !== numId);
      render();
    }
  }
});

// ----------------------------------------------------------------------------
// 9. BULK DELETE: Clear All Completed Tasks
// ----------------------------------------------------------------------------
clearButton.addEventListener('click', async () => {
  const res = await apiRequest({ action: 'clear_completed' });
  if (res && res.success) {
    // Filter out completed items locally and re-render
    items = items.filter(item => !item.completed);
    render();
  }
});

// ----------------------------------------------------------------------------
// 10. Initial Application Start
// ----------------------------------------------------------------------------
loadItems();