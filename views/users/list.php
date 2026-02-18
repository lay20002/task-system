<?php /* views/users/list.php */ ?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$role = $_SESSION['user']['role'] ?? 'user';
$isAdmin = ($role === 'admin');

/* ✅ FIX: if $users is mysqli_result, convert to array */
if (isset($users) && $users instanceof mysqli_result) {
  $tmp = [];
  while ($row = $users->fetch_assoc()) $tmp[] = $row;
  $users = $tmp;
}
$users = $users ?? [];

function roleKey(string $r): string {
  $r = strtolower(trim($r));
  return ($r === 'admin') ? 'admin' : 'user';
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/theme.css">

<style>
/* ===== Modern Users List (standard, clean) ===== */
.users-shell{ width:100%; padding: 12px 28px 24px; }
.users-topbar{
  display:flex; justify-content:space-between; align-items:center;
  gap:16px; margin-bottom:14px;
}
.users-sub{ margin:4px 0 0; color:#6c757d; font-size:.95rem; }

.users-card{
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 14px;
  box-shadow: 0 8px 24px rgba(0,0,0,.06);
  overflow:hidden;
  background:#fff;
}
.users-card-head{
  padding: 14px 16px;
  background:#fafafa;
  border-bottom: 1px solid rgba(0,0,0,.06);
}
.users-toolbar{
  display:flex; align-items:center; justify-content:space-between;
  gap:12px; flex-wrap:wrap;
}
.users-search{
  display:flex; align-items:center; gap:10px;
  flex:1; min-width: 320px;
}
.users-search input{
  border-radius: 10px;
  padding: 10px 12px;
}
.counter-pill{
  display:inline-flex; align-items:center; justify-content:center;
  gap:6px; white-space:nowrap;
  min-width: 110px;
  padding: 8px 12px;
  border-radius: 999px;
  font-size: .9rem;
  color:#111827;
  background:#fff;
  border: 1px solid rgba(0,0,0,.12);
}

.users-table thead th{
  font-size:.78rem;
  text-transform: uppercase;
  letter-spacing: .06em;
  color:#6c757d;
  background:#fff;
  border-bottom:1px solid rgba(0,0,0,.06);
}
.users-table td{ vertical-align: middle; }
.col-id{ width:90px; color:#6c757d; }
.col-role{ width:160px; }
.col-actions{ width:160px; }

.role-pill{
  display:inline-flex; align-items:center; gap:8px;
  padding: 6px 10px;
  border-radius: 999px;
  font-weight: 600;
  font-size: .85rem;
  border: 1px solid transparent;
  white-space:nowrap;
}
.dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }
.role-admin{ background: rgba(13,110,253,.12); border-color: rgba(13,110,253,.28); color:#0b3d91; }
.role-admin .dot{ background:#0d6efd; }
.role-user{ background: rgba(108,117,125,.12); border-color: rgba(108,117,125,.28); color:#343a40; }
.role-user .dot{ background:#6c757d; }

.action-wrap{ display:flex; gap:8px; align-items:center; }
.btn-sm{ border-radius: 10px; }

@media (max-width: 768px){
  .users-shell{ padding: 10px 12px 18px; }
  .col-actions{ width:auto; }
}
</style>

<div class="container-fluid users-shell">

  <div class="users-topbar">
    <div>
      <h3 class="mb-0">Users</h3>
      <div class="users-sub">Manage accounts and roles</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <?php if ($isAdmin): ?>
        <a class="btn btn-primary" href="index.php?c=user&a=create">+ Add User</a>
      <?php endif; ?>
      <?php if ($isAdmin): ?>
        <a class="btn btn-danger" href="index.php?c=auth&a=resetLock"
           onclick="return confirm('Clear login lock now?');">
          🔓 Clear Lock
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="users-card">
    <div class="users-card-head">
      <div class="users-toolbar">
        <div class="users-search">
          <input id="search" type="text" class="form-control"
                 placeholder="Search by username or role...">
          <span class="counter-pill"><span id="shownCount">0</span> / <span id="totalCount"><?= (int)count($users) ?></span> users</span>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table users-table table-hover mb-0" id="usersTable">
        <thead>
          <tr>
            <th class="col-id">ID</th>
            <th>Username</th>
            <th class="col-role">Role</th>
            <?php if ($isAdmin): ?>
              <th class="col-actions">Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="<?= $isAdmin ? 4 : 3 ?>" class="text-center text-muted py-4">
                No users found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <?php
                $rk = roleKey((string)($u['role'] ?? 'user'));
                $isMe = ((int)($u['id'] ?? 0) === (int)($_SESSION['user']['id'] ?? 0));
                $rowText = strtolower(($u['username'] ?? '').' '.($u['role'] ?? ''));
              ?>
              <tr data-text="<?= htmlspecialchars($rowText) ?>">
                <td class="col-id"><?= htmlspecialchars($u['id'] ?? '') ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($u['username'] ?? '') ?></td>
                <td class="col-role">
                  <span class="role-pill role-<?= htmlspecialchars($rk) ?>">
                    <span class="dot"></span>
                    <?= htmlspecialchars($u['role'] ?? 'user') ?>
                  </span>
                </td>

                <?php if ($isAdmin): ?>
                  <td class="col-actions">
                    <div class="action-wrap">
                      <?php if (!$isMe): ?>
                        <a class="btn btn-sm btn-danger"
                           href="index.php?c=user&a=delete&id=<?= (int)($u['id'] ?? 0) ?>"
                           onclick="return confirm('Are you sure you want to delete this user?');">
                          Delete
                        </a>
                      <?php else: ?>
                        <span class="text-muted">You</span>
                      <?php endif; ?>
                    </div>
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

<script>
  const search = document.getElementById('search');
  const rows = Array.from(document.querySelectorAll('#usersTable tbody tr'));
  const shownCount = document.getElementById('shownCount');

  function applyFilter(){
    const q = (search.value || '').toLowerCase().trim();
    let shown = 0;

    rows.forEach(tr => {
      const text = (tr.getAttribute('data-text') || '').toLowerCase();
      const ok = !q || text.includes(q);
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });

    if (shownCount) shownCount.textContent = String(shown);
  }

  search.addEventListener('input', applyFilter);
  applyFilter();
</script>
