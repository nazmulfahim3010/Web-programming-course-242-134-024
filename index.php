<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f4f8f5">
  <title>Small Steps | Your list</title>
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>
<body>
  <main class="app-shell">
    <header class="topbar">
      <a class="brand" href="#" aria-label="Small Steps home"><span class="brand-mark" aria-hidden="true">s</span> small steps</a>
      <span class="today" id="today"></span>
    </header>

    <section class="list-app" aria-labelledby="page-title">
      <div class="intro">
        <p class="eyebrow">A little at a time</p>
        <h1 id="page-title">Your list<span>.</span></h1>
        <p class="subtitle">Make room for what matters today.</p>
      </div>

      <form class="add-form" id="add-form">
        <label class="visually-hidden" for="new-item">Add something to your list</label>
        <input id="new-item" name="item" type="text" maxlength="120" placeholder="What would you like to do?" autocomplete="off" required>
        <button class="add-button" type="submit" aria-label="Add item" title="Add item"><span aria-hidden="true">+</span></button>
      </form>

      <div class="list-toolbar">
        <div class="filters" role="group" aria-label="Filter list">
          <button class="filter is-active" type="button" data-filter="all" aria-pressed="true">All</button>
          <button class="filter" type="button" data-filter="active" aria-pressed="false">To do</button>
          <button class="filter" type="button" data-filter="completed" aria-pressed="false">Done</button>
        </div>
        <span class="item-count" id="item-count" aria-live="polite"></span>
      </div>

      <ul class="items" id="items" aria-label="Your list"></ul>
      <div class="empty-state" id="empty-state">
        <span class="empty-mark" aria-hidden="true">✳</span>
        <p id="empty-message">Nothing on your list yet. Start with one small thing.</p>
      </div>

      <footer class="list-footer">
        <div class="progress-copy"><span id="progress-copy">A fresh start</span><span id="progress-value">0%</span></div>
        <div class="progress-track" role="progressbar" aria-label="List completed" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="progress-track"><span id="progress-fill"></span></div>
        <button class="clear-button" type="button" id="clear-completed" disabled>Clear completed</button>
      </footer>
    </section>

    <p class="storage-note">Connecting to MySQL Database via PHP...</p>
  </main>
</body>
</html>
