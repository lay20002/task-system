<?php /* views/users/add.php */ ?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/theme.css">

<style>
/* ===== Modern Add User (standard, clean) ===== */
.useradd-shell{ width:100%; padding: 12px 28px 24px; max-width: 900px; }
.useradd-topbar{
  display:flex; justify-content:space-between; align-items:center;
  gap:16px; margin-bottom:14px;
}
.useradd-sub{ margin:4px 0 0; color:#6c757d; font-size:.95rem; }

.useradd-card{
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 14px;
  box-shadow: 0 8px 24px rgba(0,0,0,.06);
  overflow:hidden;
  background:#fff;
}
.useradd-card-head{
  padding: 14px 16px;
  background:#fafafa;
  border-bottom: 1px solid rgba(0,0,0,.06);
}
.useradd-card-body{ padding: 16px; }

.form-control, .form-select{ border-radius: 10px; padding: 10px 12px; }
.btn{ border-radius: 10px; }

@media (max-width: 768px){
  .useradd-shell{ padding: 10px 12px 18px; }
}
</style>

<div class="container-fluid useradd-shell">

  <div class="useradd-topbar">
    <div>
      <h3 class="mb-0">Add User</h3>
      <div class="useradd-sub">Create a new account</div>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="index.php?c=user&a=list">← Back</a>
    </div>
  </div>

  <div class="useradd-card">
    <div class="useradd-card-head">
      <div class="fw-semibold">User Details</div>
      <div class="text-muted small">Fill the fields below to create a new user</div>
    </div>

    <div class="useradd-card-body">

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-3">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" autocomplete="off">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Username</label>
            <input type="text"
                   name="username"
                   class="form-control"
                   placeholder="e.g. john"
                   required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select">
              <option value="user">user</option>
              <option value="admin">admin</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Minimum 6+ characters"
                   required>
            <div class="form-text">Use at least 6 characters for better security.</div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a class="btn btn-outline-secondary" href="index.php?c=user&a=list">Cancel</a>
          <button class="btn btn-primary" type="submit">Create User</button>
        </div>

      </form>

    </div>
  </div>

</div>
