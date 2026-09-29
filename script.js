// Keep one stable storage key so the list can be restored after a refresh.
const STORAGE_KEY = 'small-steps-items';

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

// The filter is only a view choice; the full list remains in `items`.
let activeFilter = 'all';
let items = loadItems();

// Show a locally formatted date without hard-coding a language or date format.
document.querySelector('#today').textContent = new Intl.DateTimeFormat(undefined, {
  weekday: 'long',
  month: 'long',
  day: 'numeric'
}).format(new Date());

function loadItems() {
  try {
    const savedItems = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]');
    // Ignore malformed saved data so one bad entry cannot break the whole app.
    return Array.isArray(savedItems)
      ? savedItems.filter(item => item && typeof item.id === 'string' && typeof item.text === 'string' && typeof item.completed === 'boolean')
      : [];
  } catch {
    return [];
  }
}

function saveItems() {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  } catch {
    // Storage can be unavailable in private or restricted browser contexts.
    progressCopy.textContent = 'Unable to save on this device';
  }
}

function render() {
  // Derive the visible rows and progress from the single source of truth.
  const visibleItems = items.filter(item => {
    if (activeFilter === 'active') return !item.completed;
    if (activeFilter === 'completed') return item.completed;
    return true;
  });
  const completedCount = items.filter(item => item.completed).length;
  const remainingCount = items.length - completedCount;
  const percentage = items.length ? Math.round((completedCount / items.length) * 100) : 0;

  list.replaceChildren();
  for (const item of visibleItems) {
    // Build elements with textContent so user-entered text is never parsed as HTML.
    const row = document.createElement('li');
    row.className = `item-row${item.completed ? ' is-complete' : ''}`;

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

    const text = document.createElement('span');
    text.className = 'item-text';
    text.textContent = item.text;

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

  emptyState.classList.toggle('is-visible', visibleItems.length === 0);
  if (!items.length) {
    emptyMessage.textContent = 'Nothing on your list yet. Start with one small thing.';
  } else if (activeFilter === 'active') {
    emptyMessage.textContent = 'All done for now. Enjoy the breathing room.';
  } else if (activeFilter === 'completed') {
    emptyMessage.textContent = 'No completed items just yet.';
  }

  itemCount.textContent = `${remainingCount} ${remainingCount === 1 ? 'left' : 'left'}`;
  progressCopy.textContent = items.length ? `${completedCount} of ${items.length} completed` : 'A fresh start';
  progressValue.textContent = `${percentage}%`;
  progressFill.style.width = `${percentage}%`;
  progressTrack.setAttribute('aria-valuenow', String(percentage));
  clearButton.disabled = completedCount === 0;
}

form.addEventListener('submit', event => {
  event.preventDefault();
  const text = input.value.trim();
  if (!text) return;

  items.unshift({ id: `${Date.now()}-${Math.random().toString(16).slice(2)}`, text, completed: false });
  input.value = '';
  // Return to All so the newly added, incomplete item is immediately visible.
  activeFilter = 'all';
  filters.forEach(filter => {
    const selected = filter.dataset.filter === activeFilter;
    filter.classList.toggle('is-active', selected);
    filter.setAttribute('aria-pressed', String(selected));
  });
  saveItems();
  render();
  input.focus();
});

filters.forEach(filter => {
  filter.addEventListener('click', () => {
    // Filtering changes what is shown, not which items are saved.
    activeFilter = filter.dataset.filter;
    filters.forEach(button => {
      const selected = button === filter;
      button.classList.toggle('is-active', selected);
      button.setAttribute('aria-pressed', String(selected));
    });
    render();
  });
});

// Delegate row actions because each render replaces the list's child elements.
list.addEventListener('click', event => {
  const button = event.target.closest('button[data-action]');
  if (!button) return;

  const { action, id } = button.dataset;
  if (action === 'toggle') {
    items = items.map(item => item.id === id ? { ...item, completed: !item.completed } : item);
  } else if (action === 'delete') {
    items = items.filter(item => item.id !== id);
  }
  saveItems();
  render();
});

clearButton.addEventListener('click', () => {
  // Remove completed entries in one operation while keeping unfinished work.
  items = items.filter(item => !item.completed);
  saveItems();
  render();
});

render();