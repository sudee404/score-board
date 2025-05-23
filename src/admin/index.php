<?php
require_once __DIR__ . '/../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new judge
    if (isset($_POST['action']) && $_POST['action'] === 'add_judge') {
        try {
            $db->insert('judges', [
                'username' => $_POST['username'],
                'display_name' => $_POST['display_name']
            ]);
            // Set success message using helper function
            setToast('success',
                'Judge Added',
                'Judge "' . htmlspecialchars($_POST['display_name']) . '" has been added successfully.'
            );
        } catch (Exception $e) {
            // Set error message using helper function
            setToast('error',
                'Error',
                'Failed to add judge: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Update existing judge
    if (isset($_POST['action']) && $_POST['action'] === 'update_judge') {
        try {
            $db->update('judges', [
                'username' => $_POST['username'],
                'display_name' => $_POST['display_name']
            ], 'id = ?', [$_POST['judge_id']]);
            
            setToast('success',
                'Judge Updated',
                'Judge has been updated successfully.'
            );
        } catch (Exception $e) {
            setToast('error',
                'Error',
                'Failed to update judge: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Delete judge
    if (isset($_POST['action']) && $_POST['action'] === 'delete_judge') {
        try {
            // First check if judge has any scores
            $scores = $db->query("SELECT COUNT(*) FROM scores WHERE judge_id = ?", [$_POST['judge_id']])->fetchColumn();
            
            if ($scores > 0) {
                // Delete associated scores first
                $db->delete('scores', 'judge_id = ?', [$_POST['judge_id']]);
            }
            
            // Now delete the judge
            $db->delete('judges', 'id = ?', [$_POST['judge_id']]);
            
            setToast('success',
                'Judge Deleted',
                'Judge and all associated scores have been deleted.'
            );
        } catch (Exception $e) {
            setToast('error',
                'Error',
                'Failed to delete judge: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$judges = $db->query("SELECT * FROM judges ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Board - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="/public/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><span class="highlight">Score</span> Board</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/public/"><i class="bi bi-trophy"></i> Scoreboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/judge/"><i class="bi bi-star"></i> Judge Portal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/"><i class="bi bi-gear"></i> Admin Panel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="app-title">Admin Panel</h1>
            <p class="app-subtitle">Manage judges and system settings</p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3"><i class="bi bi-person-badge-fill me-2"></i>Manage Judges</h2>
                    <div>
                        <a href="/admin/users.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-people-fill me-1"></i> Manage Users
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJudgeModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Add New Judge
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow card-hover-shine animate-fade-in">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0"><i class="bi bi-info-circle-fill me-2"></i>System Information</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-4 border-end">
                                <div class="p-4 text-center h-100 d-flex flex-column justify-content-center" style="background-color: rgba(13, 110, 253, 0.05);">
                                    <div class="bg-primary text-white rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-speedometer2 fs-1"></i>
                                    </div>
                                    <h5 class="mb-2">System Status</h5>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="badge bg-success p-2 px-3 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> All systems operational</span>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">Server Load: <span class="text-success">Normal</span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 border-end">
                                <div class="p-4 text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="bg-info text-white rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-clock-history fs-1"></i>
                                    </div>
                                    <h5 class="mb-2">System Information</h5>
                                    <p class="mb-1"><i class="bi bi-calendar3 me-1"></i> <strong>Date:</strong> <?php echo date('M d, Y'); ?></p>
                                    <p class="mb-1"><i class="bi bi-clock me-1"></i> <strong>Time:</strong> <?php echo date('g:i A'); ?></p>
                                    <p class="mb-0"><i class="bi bi-hdd-stack me-1"></i> <strong>Version:</strong> 1.0.0</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 text-center h-100 d-flex flex-column justify-content-center" style="background-color: rgba(13, 110, 253, 0.05);">
                                    <div class="bg-success text-white rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-people-fill fs-1"></i>
                                    </div>
                                    <h5 class="mb-3">Quick Links</h5>
                                    <div class="d-grid gap-2">
                                        <a href="/judge/" class="btn btn-primary">
                                            <i class="bi bi-star me-1"></i> Judge Portal
                                        </a>
                                        <a href="/public/" class="btn btn-success">
                                            <i class="bi bi-trophy me-1"></i> Scoreboard
                                        </a>
                                        <a href="/admin/users.php" class="btn btn-info text-white">
                                            <i class="bi bi-person-fill me-1"></i> Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow animate-fade-in">
            <div class="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                </div>
                <span class="badge bg-light text-dark"><?php echo count($judges); ?> Judges</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($judges)): ?>
                <div class="alert alert-info m-4">
                    <i class="bi bi-info-circle-fill me-2"></i> No judges have been registered yet. Add your first judge using the button above.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="5%"><i class="bi bi-hash me-1"></i>ID</th>
                                <th width="15%"><i class="bi bi-at me-1"></i>Username</th>
                                <th width="20%"><i class="bi bi-person-fill me-1"></i>Display Name</th>
                                <th width="20%"><i class="bi bi-calendar-event me-1"></i>Created</th>
                                <th width="40%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="stagger-children">
                            <?php foreach ($judges as $judge): ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo htmlspecialchars($judge['id']); ?></td>
                                <td><code><?php echo htmlspecialchars($judge['username']); ?></code></td>
                                <td class="fw-medium"><?php echo htmlspecialchars($judge['display_name']); ?></td>
                                <td><?php echo htmlspecialchars(date('M d, Y g:i A', strtotime($judge['created_at']))); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="/judge/?id=<?php echo htmlspecialchars($judge['id']); ?>" class="btn btn-sm btn-primary" title="Open Judge Portal">
                                            <i class="bi bi-star-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info edit-judge-btn" data-id="<?php echo htmlspecialchars($judge['id']); ?>" data-username="<?php echo htmlspecialchars($judge['username']); ?>" data-display-name="<?php echo htmlspecialchars($judge['display_name']); ?>" data-bs-toggle="modal" data-bs-target="#editJudgeModal" title="Edit Judge">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-judge-btn" data-id="<?php echo htmlspecialchars($judge['id']); ?>" data-name="<?php echo htmlspecialchars($judge['display_name']); ?>" data-bs-toggle="modal" data-bs-target="#deleteJudgeModal" title="Delete Judge">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><span class="highlight">Score</span> Board</h5>
                    <p>A comprehensive solution for managing judges and scoring participants in events.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/public/" class="text-white">Scoreboard</a></li>
                        <li><a href="/judge/" class="text-white">Judge Portal</a></li>
                        <li><a href="/admin/" class="text-white">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>About</h5>
                    <p>Built on LAMP stack technology. Version 1.0.0</p>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Score Board. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Add Judge Modal -->
    <div class="modal fade" id="addJudgeModal" tabindex="-1" aria-labelledby="addJudgeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJudgeModalLabel"><i class="bi bi-person-plus-fill"></i> Add New Judge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="add_judge">
                        
                        <div class="text-center mb-4">
                            <div class="avatar-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary-light" style="width: 80px; height: 80px; border-radius: 50%;">
                                <i class="bi bi-person-badge text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="badge bg-light text-secondary px-3 py-2">
                                <i class="bi bi-info-circle me-1"></i> Create a new judge account
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-text">This will be used for login purposes</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="display_name" class="form-label fw-bold">Display Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-card-heading"></i></span>
                                <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Enter display name" required>
                            </div>
                            <div class="form-text">This name will be displayed publicly</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add Judge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Judge Modal -->
    <div class="modal fade" id="editJudgeModal" tabindex="-1" aria-labelledby="editJudgeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJudgeModalLabel"><i class="bi bi-pencil-square"></i> Edit Judge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="update_judge">
                        <input type="hidden" name="judge_id" id="edit_judge_id">
                        
                        <div class="text-center mb-4">
                            <div class="avatar-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary-light" style="width: 80px; height: 80px; border-radius: 50%;">
                                <i class="bi bi-person-fill text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="badge bg-light text-secondary px-3 py-2">
                                <i class="bi bi-info-circle me-1"></i> Update judge information
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="edit_username" class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control" id="edit_username" name="username" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_display_name" class="form-label fw-bold">Display Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-card-heading"></i></span>
                                <input type="text" class="form-control" id="edit_display_name" name="display_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Judge Modal -->
    <div class="modal fade" id="deleteJudgeModal" tabindex="-1" aria-labelledby="deleteJudgeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-danger">
                    <h5 class="modal-title" id="deleteJudgeModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Delete Judge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-danger-light" style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-trash-fill text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="mb-3 fw-bold">Confirm Deletion</h4>
                        <p class="mb-1">Are you sure you want to delete <strong id="delete_judge_name" class="text-danger"></strong>?</p>
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <strong>Warning:</strong> This will also delete all scores assigned by this judge and cannot be undone.
                        </div>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="delete_judge">
                    <input type="hidden" name="judge_id" id="delete_judge_id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete Judge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/toast.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['toast'])): ?>
            // Display toast notification
            toast.show({
                type: '<?php echo $_SESSION['toast']['type']; ?>',
                title: '<?php echo addslashes($_SESSION['toast']['title']); ?>',
                message: '<?php echo addslashes($_SESSION['toast']['message']); ?>',
                duration: 5000
            });
            <?php 
            // Clear the toast message from session after displaying
            unset($_SESSION['toast']);
            endif; ?>
            
            // Add animation class to cards with a slight delay for each
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 150);
            });
            
            // Add hover effect to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
                    this.style.transition = 'all 0.3s ease';
                    this.style.zIndex = '1';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                    this.style.zIndex = 'auto';
                });
            });
            
            // Set up edit judge modal
            const editJudgeBtns = document.querySelectorAll('.edit-judge-btn');
            editJudgeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_judge_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_username').value = this.getAttribute('data-username');
                    document.getElementById('edit_display_name').value = this.getAttribute('data-display-name');
                });
            });
            
            // Set up delete judge modal
            const deleteJudgeBtns = document.querySelectorAll('.delete-judge-btn');
            deleteJudgeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('delete_judge_id').value = this.getAttribute('data-id');
                    document.getElementById('delete_judge_name').textContent = this.getAttribute('data-name');
                });
            });
        });
    </script>
</body>
</html>
