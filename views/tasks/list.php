<?php
/* views/tasks/list.php */
if (session_status() === PHP_SESSION_NONE) session_start();

$csrf = $_SESSION['csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf'] = $csrf;

$role = $_SESSION['user']['role'] ?? 'user';
$isAdmin = ($role === 'admin');

/* ✅ FIX: if $tasks is mysqli_result, convert to array */
if (isset($tasks) && $tasks instanceof mysqli_result) {
  $tmp = [];
  while ($row = $tasks->fetch_assoc()) $tmp[] = $row;
  $tasks = $tmp;
}
$tasks = $tasks ?? [];

function statusKey(string $s): string {
  $s = strtolower(trim($s));
  if ($s === 'completed') return 'completed';
  if ($s === 'in progress' || $s === 'in_progress' || $s === 'progress') return 'progress';
  return 'pending';
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/theme.css">

<style>
/* ===== Modern Task List UI ===== */
.task-topbar { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px; }
.task-title h3{ margin:0; }
.task-sub{ margin:4px 0 0; color:#6c757d; font-size:.95rem; }
.task-actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }

.task-card { border:1px solid rgba(0,0,0,.08); border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.06); overflow:hidden; background:#fff; }
.task-card-head{ padding:14px 14px 10px; background:linear-gradient(180deg, rgba(0,0,0,.02), rgba(0,0,0,0)); border-bottom:1px solid rgba(0,0,0,.06); }
.task-toolbar{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; }

.task-search{ display:flex; align-items:center; gap:10px; flex:1; min-width:240px; }
.task-search input{ border-radius:12px; padding:10px 12px; }

.task-filters{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.chip{ border:1px solid rgba(0,0,0,.12); background:#fff; padding:8px 12px; border-radius:999px; font-size:.9rem; cursor:pointer; user-select:none; }
.chip.active{ background:#111827; border-color:#111827; color:#fff; }

.counter-pill{
  font-size:.9rem; color:#111827;
  background: rgba(17,24,39,.06);
  padding:8px 12px; border-radius:999px;
  border:1px solid rgba(17,24,39,.08);
  white-space: nowrap;
}

.task-table thead th{ font-size:.85rem; text-transform:uppercase; letter-spacing:.04em; color:#6c757d; background:#fafafa; }
.task-table td{ vertical-align:middle; }
.col-id{ width:90px; color:#6c757d; }
.col-actions{ width:240px; }

.status-pill{ display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; font-weight:600; font-size:.85rem; border:1px solid transparent; white-space:nowrap; }
.dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }

.status-pending{ background: rgba(255,193,7,.18); border-color: rgba(255,193,7,.35); color:#7a5b00; }
.status-pending .dot{ background:#ffc107; }
.status-progress{ background: rgba(13,110,253,.12); border-color: rgba(13,110,253,.28); color:#0b3d91; }
.status-progress .dot{ background:#0d6efd; }
.status-completed{ background: rgba(25,135,84,.12); border-color: rgba(25,135,84,.25); color:#146c43; }
.status-completed .dot{ background:#198754; }

.btn-soft{ border-radius:12px; padding:6px 10px; }
.btn-soft-warning{ background: rgba(255,193,7,.16); border:1px solid rgba(255,193,7,.35); color:#7a5b00; }
.btn-soft-warning:hover{ background: rgba(255,193,7,.24); color:#664c00; }
.btn-soft-danger{ background: rgba(220,53,69,.14); border:1px solid rgba(220,53,69,.30); color:#a61e2c; }
.btn-soft-danger:hover{ background: rgba(220,53,69,.20); color:#8e1a26; }
.btn-soft-dark{ background: rgba(33,37,41,.10); border:1px solid rgba(33,37,41,.22); color:#212529; }

.disabled-action{ pointer-events:none !important; cursor: default !important; opacity:.85 !important; box-shadow:none !important; }
.disabled-action:hover{ transform:none !important; }

.task-shell-full{ width:100% !important; max-width:100% !important; padding-left:28px; padding-right:28px; }
@media (max-width:768px){ .task-shell-full{ padding-left:12px; padding-right:12px; } .col-actions{ width:auto; } }

/* ✅ FIX CLICK ISSUE (overlay / z-index blocker) */
.task-shell-full,
.task-card,
.table-responsive,
#taskTable,
#taskTable *{
  position: relative;
  z-index: 5;
}
#taskTable a,
#taskTable button{
  pointer-events: auto !important;
}
</style>

<div class="container-fluid pt-2 pb-4 task-shell-full">

  <?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['success_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <div class="task-topbar">
    <div class="task-title">
      <h3 class="mb-0">Task List</h3>
      <div class="task-sub">Search, filter, and manage tasks</div>
    </div>

    <div class="task-actions">
      <?php if ($isAdmin): ?>
        <a href="index.php?c=task&a=add" class="btn btn-primary btn-soft">+ Add Task</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="task-card">
    <div class="task-card-head">
      <div class="task-toolbar">
        <div class="task-search">
          <input id="q" type="text" class="form-control" placeholder="Search location / description / status...">
          <span class="counter-pill"><span id="shownCount">0</span> / <?= (int)count($tasks) ?></span>
        </div>

        <div class="task-filters">
          <span class="chip active" data-filter="all">All</span>
          <span class="chip" data-filter="pending">Pending</span>
          <span class="chip" data-filter="progress">In Progress</span>
          <span class="chip" data-filter="completed">Completed</span>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table task-table table-hover mb-0" id="taskTable">
        <thead>
          <tr>
            <th class="col-id">ID</th>
            <th>Location</th>
            <th>Description</th>
            <th style="width:180px;">Status</th>
            <th style="width:140px;">Due Date</th>

            <?php if ($isAdmin): ?>
              <th class="col-actions">Actions</th>
            <?php endif; ?>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($tasks)): ?>
            <tr>
              <td colspan="<?= $isAdmin ? 6 : 5 ?>" class="text-center text-muted py-4">No tasks found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($tasks as $t): ?>
              <?php
                $status = trim($t['status'] ?? '');
                $key = statusKey($status);
                $isComplete = ($key === 'completed');
                $searchText = strtolower(($t['location'] ?? '').' '.($t['task_description'] ?? '').' '.$status.' '.($t['due_date'] ?? ''));
              ?>
              <tr data-status="<?= htmlspecialchars($key) ?>" data-text="<?= htmlspecialchars($searchText) ?>">
                <td class="col-id"><?= (int)($t['id'] ?? 0) ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($t['location'] ?? '') ?></td>
                <td class="text-muted"><?= htmlspecialchars($t['task_description'] ?? '') ?></td>
                <td>
                  <span class="status-pill status-<?= htmlspecialchars($key) ?>">
                    <span class="dot"></span>
                    <?= htmlspecialchars($status ?: 'Pending') ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($t['due_date'] ?? '') ?></td>

                <?php if ($isAdmin): ?>
                  <td class="col-actions">
                    <?php if ($isComplete): ?>
                      <a class="btn btn-sm btn-soft-dark btn-soft disabled-action" href="javascript:void(0)">Edit</a>
                      <button type="button" class="btn btn-sm btn-soft-dark btn-soft disabled-action">Delete</button>
                    <?php else: ?>
                      <a class="btn btn-sm btn-soft btn-soft-warning"
                         href="index.php?c=task&a=edit&id=<?= (int)($t['id'] ?? 0) ?>">Edit</a>

                      <form method="post"
                            action="index.php?c=task&a=delete&id=<?= (int)($t['id'] ?? 0) ?>"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this task?');">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-sm btn-soft btn-soft-danger">Delete</button>
                      </form>
                    <?php endif; ?>
                  </td>
                <?php endif; ?>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  setTimeout(() => {
    const alert = document.querySelector('.alert-success');
    if (alert) {
      alert.classList.remove('show');
      setTimeout(() => alert.remove(), 500);
    }
  }, 3000);

  const q = document.getElementById('q');
  const chips = Array.from(document.querySelectorAll('.chip'));
  const rows = Array.from(document.querySelectorAll('#taskTable tbody tr'));
  const shownCount = document.getElementById('shownCount');

  let activeFilter = 'all';

  function apply() {
    const query = (q.value || '').toLowerCase().trim();
    let shown = 0;

    rows.forEach(tr => {
      const status = tr.getAttribute('data-status') || '';
      const text = tr.getAttribute('data-text') || '';
      const ok = (!query || text.includes(query)) && (activeFilter === 'all' || status === activeFilter);
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });

    if (shownCount) shownCount.textContent = String(shown);
  }

  chips.forEach(ch => {
    ch.addEventListener('click', () => {
      chips.forEach(c => c.classList.remove('active'));
      ch.classList.add('active');
      activeFilter = ch.dataset.filter || 'all';
      apply();
    });
  });

  q.addEventListener('input', apply);
  apply();
</script>
