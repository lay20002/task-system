<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$isComplete = (trim($task['status'] ?? '') === 'Completed');
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/theme.css">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
          <h4 class="mb-0">Edit Task</h4>
        </div>

        <div class="card-body">

          <?php if ($isComplete): ?>
            <div class="alert alert-secondary">
              This task is <strong>Completed</strong>. You can view it but cannot update.
            </div>
          <?php endif; ?>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="post" onsubmit="return confirmUpdate();">

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold">Location *</label>
              <input type="text"
                     name="location"
                     class="form-control"
                     value="<?= htmlspecialchars($task['location'] ?? '') ?>"
                     required
                     <?= $isComplete ? 'disabled' : '' ?>>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Task Description</label>
              <textarea name="task_description"
                        class="form-control"
                        rows="3"
                        <?= $isComplete ? 'disabled' : '' ?>><?= htmlspecialchars($task['task_description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Status *</label>
              <select name="status" class="form-select" required <?= $isComplete ? 'disabled' : '' ?>>
                <option value="Pending"
                  <?= ($task['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>
                  Pending
                </option>
                <option value="In Progress"
                  <?= ($task['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>
                  In Progress
                </option>
                <option value="Completed"
                  <?= ($task['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>
                  Completed
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Due Date *</label>
              <input type="date"
                     name="due_date"
                     class="form-control"
                     value="<?= htmlspecialchars($task['due_date'] ?? '') ?>"
                     min="<?= date('Y-m-d') ?>"
                     required
                     <?= $isComplete ? 'disabled' : '' ?>>
            </div>

            <div class="d-flex justify-content-between">
              <a href="index.php?c=task&a=list" class="btn btn-outline-secondary">
                ← Back
              </a>

              <?php if ($isComplete): ?>
                <!-- visible but disabled -->
                <button type="button" class="btn btn-secondary disabled-action">
                  Update Task
                </button>
              <?php else: ?>
                <!-- bright glow -->
                <button type="submit" class="btn btn-warning active-action">
                  Update Task
                </button>
              <?php endif; ?>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
function confirmUpdate() {
  // if completed, block submit (extra safety on UI)
  const isComplete = <?= $isComplete ? 'true' : 'false' ?>;
  if (isComplete) return false;

  return confirm("Are you sure you want to update this task?");
}
</script>
