<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$csrf = $_SESSION['csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf'] = $csrf;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/theme.css">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Add New Task</h4>
        </div>

        <div class="card-body">

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="post" onsubmit="return confirmSave();">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold">Location *</label>
              <input name="location"
                     class="form-control"
                     placeholder="Enter location"
                     required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Task Description</label>
              <textarea name="task_description"
                        class="form-control"
                        rows="3"
                        placeholder="Describe the task..."></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Status *</label>
              <select name="status" class="form-select" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Due Date *</label>
              <input type="date"
                     name="due_date"
                     class="form-control"
                     min="<?= date('Y-m-d') ?>"
                     required>
            </div>

            <div class="d-flex justify-content-between">
              <a href="index.php?c=task&a=list" class="btn btn-outline-secondary">
                ← Back
              </a>
              <button type="submit" class="btn btn-success">
                Save Task
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
function confirmSave() {
  return confirm("Are you sure you want to save this task?");
}
</script>
