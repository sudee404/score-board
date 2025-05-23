<?php
/**
 * Score Board - Judge User Management
 * This file allows judges to manage users they're voting for
 */

require_once __DIR__ . '/../includes/init.php';

// Get judge ID from URL parameter
$judge_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Fetch judge details if ID is provided
$judge = null;
if ($judge_id) {
    $judge = $db->query("SELECT * FROM judges WHERE id = ?", [$judge_id])->fetch();
    if (!$judge) {
        header('Location: /judge/');
        exit;
    }
} else {
    // Redirect to judge selection if no judge ID provided
    header('Location: /judge/');
    exit;
}

// Handle user CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new user
    if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        try {
            $db->insert('users', [
                'name' => $_POST['name']
            ]);
            
            $user_id = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            
            setToast('success',
                'User Added',
                'User "' . htmlspecialchars($_POST['name']) . '" has been added successfully.'
            );
        } catch (Exception $e) {
            setToast('error',
                'Error',
                'Failed to add user: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $judge_id);
        exit;
    }
    
    // Update existing user
    if (isset($_POST['action']) && $_POST['action'] === 'update_user') {
        try {
            $db->update('users', [
                'name' => $_POST['name']
            ], 'id = ?', [$_POST['user_id']]);
            
            setToast('success',
                'User Updated',
                'User has been updated successfully.'
            );
        } catch (Exception $e) {
            setToast('error',
                'Error',
                'Failed to update user: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $judge_id);
        exit;
    }
    
    // Delete user
    if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
        try {
            // First check if user has any scores
            $scores = $db->query("SELECT COUNT(*) FROM scores WHERE user_id = ?", [$_POST['user_id']])->fetchColumn();
            
            if ($scores > 0) {
                // Delete associated scores first
                $db->delete('scores', 'user_id = ?', [$_POST['user_id']]);
            }
            
            // Now delete the user
            $db->delete('users', 'id = ?', [$_POST['user_id']]);
            
            setToast('success',
                'User Deleted',
                'User and all associated scores have been deleted.'
            );
        } catch (Exception $e) {
            setToast('error',
                'Error',
                'Failed to delete user: ' . $e->getMessage()
            );
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $judge_id);
        exit;
    }
}

// Get all users with their scores from this judge (if any)
$users = $db->query("
    SELECT 
        u.id, 
        u.name, 
        u.created_at,
        COALESCE(s.points, 0) as points,
        s.id as score_id,
        CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END as has_voted,
        (SELECT COUNT(*) FROM scores WHERE user_id = u.id) as total_scores,
        (SELECT COALESCE(SUM(points), 0) FROM scores WHERE user_id = u.id) as total_points
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id AND s.judge_id = ?
    ORDER BY u.name
", [$judge_id])->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Board - Manage Users</title>
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
                        <a class="nav-link active" href="/judge/"><i class="bi bi-star"></i> Judge Portal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/"><i class="bi bi-gear"></i> Admin Panel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Navigation Indicator -->
    <div class="nav-indicator"></div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="app-title">Manage Users</h1>
            <p class="app-subtitle">Add, edit, and manage participants as judge: <?php echo htmlspecialchars($judge['display_name']); ?></p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3"><i class="bi bi-people-fill me-2"></i>Manage Participants</h2>
                    <div>
                        <a href="/judge/?id=<?php echo htmlspecialchars($judge_id); ?>" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-left me-1"></i> Back to Scoring
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Add New User
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow animate-fade-in">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <div>
                    <i class="bi bi-people-fill me-2"></i>
                    <h2 class="h4 d-inline mb-0">Registered Users</h2>
                </div>
                <span class="badge bg-light text-dark"><?php echo count($users); ?> Users</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                <div class="alert alert-info m-4">
                    <i class="bi bi-info-circle-fill me-2"></i> No users have been registered yet. Add your first user using the button above.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%"><i class="bi bi-person-fill me-1"></i>Name</th>
                                <th width="10%" class="text-center"><i class="bi bi-trophy-fill me-1"></i>Your Score</th>
                                <th width="10%" class="text-center"><i class="bi bi-people-fill me-1"></i>Total Judges</th>
                                <th width="10%" class="text-center"><i class="bi bi-calculator me-1"></i>Total Points</th>
                                <th width="15%"><i class="bi bi-calendar-event me-1"></i>Created</th>
                                <th width="25%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="stagger-children">
                            <?php foreach ($users as $index => $user): 
                                $scoreClass = '';
                                if ($user['points'] >= 90) {
                                    $scoreClass = 'bg-success';
                                } elseif ($user['points'] >= 70) {
                                    $scoreClass = 'bg-primary';
                                } elseif ($user['points'] >= 50) {
                                    $scoreClass = 'bg-info';
                                } elseif ($user['points'] >= 30) {
                                    $scoreClass = 'bg-warning';
                                } elseif ($user['points'] > 0) {
                                    $scoreClass = 'bg-danger';
                                } else {
                                    $scoreClass = 'bg-secondary';
                                }
                            ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-secondary text-white me-2" style="width: 2rem; height: 2rem; font-size: 0.8rem;">
                                            <?php echo substr(htmlspecialchars($user['name']), 0, 1); ?>
                                        </div>
                                        <span class="fw-medium"><?php echo htmlspecialchars($user['name']); ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="points-badge <?php echo $scoreClass; ?>">
                                        <?php echo htmlspecialchars($user['points']); ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars($user['total_scores']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($user['total_points']); ?></td>
                                <td><?php echo htmlspecialchars(date('M d, Y g:i A', strtotime($user['created_at']))); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary edit-user-btn" 
                                                data-id="<?php echo htmlspecialchars($user['id']); ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                title="Edit User">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                                data-id="<?php echo htmlspecialchars($user['id']); ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                title="Delete User">
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
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">
                        <div class="mb-3">
                            <label for="name" class="form-label">User Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-text">Enter the participant's full name</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_user">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">User Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_user_name"></strong>?</p>
                    <p class="text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i> This will also delete all scores associated with this user and cannot be undone.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete User
                        </button>
                    </div>
                </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/toast.js"></script>
    <script>
        // Display toast notification if available in session
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
            
            // Set up edit user modal
            const editUserBtns = document.querySelectorAll('.edit-user-btn');
            editUserBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_user_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_name').value = this.getAttribute('data-name');
                });
            });
            
            // Set up delete user modal
            const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
            deleteUserBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('delete_user_id').value = this.getAttribute('data-id');
                    document.getElementById('delete_user_name').textContent = this.getAttribute('data-name');
                });
            });
        });
        
        // Add scroll event listener for navigation indicator
        window.addEventListener('scroll', function() {
            const indicator = document.querySelector('.nav-indicator');
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            indicator.style.width = scrolled + '%';
        });
    </script>
</body>
</html>
