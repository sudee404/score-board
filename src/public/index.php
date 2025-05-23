<?php
require_once __DIR__ . '/../includes/init.php';

// Get users and their total scores
$users = $db->query("
    SELECT 
        u.id,
        u.name,
        COALESCE(SUM(s.points), 0) as total_points,
        COUNT(DISTINCT s.judge_id) as judges_count
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id
    GROUP BY u.id, u.name
    ORDER BY total_points DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Board - Scoreboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
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
                        <a class="nav-link active" href="/public/"><i class="bi bi-trophy"></i> Scoreboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/judge/"><i class="bi bi-star"></i> Judge Portal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/"><i class="bi bi-gear"></i> Admin Panel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="app-title">Score Board</h1>
            <p class="app-subtitle">Real-time participant rankings and scores</p>
        </div>
        
        <div class="card mb-4 shadow">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-primary rounded-circle p-3 me-3 text-white">
                    <i class="bi bi-arrow-clockwise fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1">Live Scoreboard</h5>
                    <p class="mb-0 text-muted">Last updated: <?php echo date('M d, Y g:i:s A'); ?></p>
                    <p class="mb-0 text-muted"><small>Auto-refreshes every 30 seconds</small></p>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-repeat"></i> Refresh Now
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow scoreboard animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="bi bi-trophy-fill me-2"></i>Participant Rankings</h3>
            </div>
            <?php if (empty($users)): ?>
            <div class="card-body">
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <div>No participants or scores found in the system. Add participants through the Admin Panel.</div>
                </div>
            </div>
            <?php else: ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 scoreboard-table">
                        <thead>
                            <tr>
                                <th width="10%" class="text-center"><i class="bi bi-hash"></i> Rank</th>
                                <th width="40%"><i class="bi bi-person"></i> Participant</th>
                                <th width="25%" class="text-center"><i class="bi bi-trophy"></i> Total Points</th>
                                <th width="25%" class="text-center"><i class="bi bi-people"></i> Judges</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): 
                                $rowClass = '';
                                $medalIcon = '';
                                
                                if ($index == 0) {
                                    $rowClass = 'position-1';
                                    $medalIcon = '<i class="bi bi-trophy-fill text-warning fs-5" title="Gold Medal"></i>';
                                } else if ($index == 1) {
                                    $rowClass = 'position-2';
                                    $medalIcon = '<i class="bi bi-trophy-fill text-secondary fs-5" title="Silver Medal"></i>';
                                } else if ($index == 2) {
                                    $rowClass = 'position-3';
                                    $medalIcon = '<i class="bi bi-trophy-fill text-danger fs-5" title="Bronze Medal"></i>';
                                }
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td class="text-center fw-bold">
                                    <?php if (!empty($medalIcon)) echo $medalIcon; else echo ($index + 1); ?>
                                </td>
                                <td class="fw-medium"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="text-center">
                                    <?php
                                    $scoreClass = '';
                                    $points = (int)$user['total_points'];
                                    if ($points >= 90) {
                                        $scoreClass = 'bg-success text-white';
                                    } elseif ($points >= 70) {
                                        $scoreClass = 'bg-primary text-white';
                                    } elseif ($points >= 50) {
                                        $scoreClass = 'bg-info text-white';
                                    } elseif ($points >= 30) {
                                        $scoreClass = 'bg-warning';
                                    } elseif ($points > 0) {
                                        $scoreClass = 'bg-danger text-white';
                                    } else {
                                        $scoreClass = 'bg-secondary text-white';
                                    }
                                    ?>
                                    <span class="points-badge <?php echo $scoreClass; ?>"><?php echo htmlspecialchars($user['total_points']); ?></span>
                                </td>
                                <td class="text-center">
                                     <?php
                                     $judgeClass = '';
                                     $judgeCount = (int)$user['judges_count'];
                                     if ($judgeCount >= 5) {
                                         $judgeClass = 'bg-success';
                                     } elseif ($judgeCount >= 3) {
                                         $judgeClass = 'bg-primary';
                                     } elseif ($judgeCount >= 2) {
                                         $judgeClass = 'bg-info';
                                     } elseif ($judgeCount == 1) {
                                         $judgeClass = 'bg-warning';
                                     } else {
                                         $judgeClass = 'bg-secondary';
                                     }
                                     ?>
                                     <span class="badge <?php echo $judgeClass; ?>">
                                         <i class="bi bi-people-fill me-1"></i>
                                         <?php echo htmlspecialchars($user['judges_count']); ?> judge<?php echo $user['judges_count'] != 1 ? 's' : ''; ?>
                                     </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
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
    
    <script>
        let previousScores = {};
        let firstLoad = true;
        
        // Function to fetch scores via AJAX
        function fetchScores() {
            fetch('/public/fetch_scores.php')
                .then(response => response.json())
                .then(data => {
                    updateScoreboard(data);
                })
                .catch(error => {
                    console.error('Error fetching scores:', error);
                });
        }
        
        // Function to update the scoreboard with new data
        function updateScoreboard(users) {
            const tbody = document.querySelector('#scoreboard-table tbody');
            if (!tbody) return;
            
            // Clear the table
            tbody.innerHTML = '';
            
            // Populate with new data
            users.forEach((user, index) => {
                const row = document.createElement('tr');
                
                // Check if score changed and this isn't the first load
                let scoreChanged = false;
                if (!firstLoad && previousScores[user.id] !== undefined && previousScores[user.id] !== user.total_points) {
                    scoreChanged = true;
                    
                    // Show toast notification for score change
                    const pointDiff = user.total_points - previousScores[user.id];
                    const message = `${user.name}'s score ${pointDiff > 0 ? 'increased by' : 'decreased by'} ${Math.abs(pointDiff)} points`;
                    
                    toast.show({
                        type: pointDiff > 0 ? 'success' : 'info',
                        title: 'Score Updated',
                        message: message,
                        duration: 4000
                    });
                }
                
                // Store current score for next comparison
                previousScores[user.id] = user.total_points;
                
                // Create rank cell with medal for top 3
                const rankCell = document.createElement('td');
                rankCell.className = 'text-center';
                if (index < 3) {
                    const medalColors = ['gold', 'silver', '#CD7F32'];
                    rankCell.innerHTML = `<div class="medal" style="background-color: ${medalColors[index]}"><i class="bi bi-trophy-fill"></i></div>`;
                } else {
                    rankCell.textContent = index + 1;
                }
                row.appendChild(rankCell);
                
                // Create name cell
                const nameCell = document.createElement('td');
                nameCell.className = 'fw-medium';
                nameCell.textContent = user.name;
                row.appendChild(nameCell);
                
                // Create points cell
                const pointsCell = document.createElement('td');
                pointsCell.className = 'text-center';
                const pointsSpan = document.createElement('span');
                
                // Determine score class based on points
                let scoreClass = '';
                const points = parseInt(user.total_points);
                if (points >= 90) {
                    scoreClass = 'bg-success text-white';
                } else if (points >= 70) {
                    scoreClass = 'bg-primary text-white';
                } else if (points >= 50) {
                    scoreClass = 'bg-info text-white';
                } else if (points >= 30) {
                    scoreClass = 'bg-warning';
                } else if (points > 0) {
                    scoreClass = 'bg-danger text-white';
                } else {
                    scoreClass = 'bg-secondary text-white';
                }
                
                pointsSpan.className = 'points-badge ' + scoreClass + (scoreChanged ? ' highlight-change' : '');
                pointsSpan.textContent = user.total_points;
                pointsCell.appendChild(pointsSpan);
                row.appendChild(pointsCell);
                
                // Create judges count cell
                const judgesCell = document.createElement('td');
                judgesCell.className = 'text-center';
                
                // Determine judge count class based on number of judges
                let judgeClass = '';
                const judgeCount = parseInt(user.judges_count);
                if (judgeCount >= 5) {
                    judgeClass = 'bg-success';
                } else if (judgeCount >= 3) {
                    judgeClass = 'bg-primary';
                } else if (judgeCount >= 2) {
                    judgeClass = 'bg-info';
                } else if (judgeCount == 1) {
                    judgeClass = 'bg-warning';
                } else {
                    judgeClass = 'bg-secondary';
                }
                
                const judgeSpan = document.createElement('span');
                judgeSpan.className = 'badge ' + judgeClass;
                judgeSpan.innerHTML = `<i class="bi bi-people-fill me-1"></i> ${user.judges_count} judge${user.judges_count != 1 ? 's' : ''}`;
                judgesCell.appendChild(judgeSpan);
                row.appendChild(judgesCell);
                
                // Add row to table
                tbody.appendChild(row);
                
                // Add animation if score changed
                if (scoreChanged) {
                    row.classList.add('highlight-row');
                    setTimeout(() => {
                        row.classList.remove('highlight-row');
                    }, 3000);
                }
            });
            
            // Update last refresh time
            document.getElementById('last-refresh').textContent = new Date().toLocaleTimeString();
            firstLoad = false;
        }
        
        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            fetchScores();
            
            // Setup refresh button
            document.getElementById('refresh-btn').addEventListener('click', function() {
                this.classList.add('spin');
                fetchScores();
                
                // Show refresh notification
                toast.info('Scoreboard has been refreshed', 'Refreshed', 2000);
                
                // Remove spin class after animation completes
                setTimeout(() => {
                    this.classList.remove('spin');
                }, 1000);
            });
            
            // Auto refresh every 30 seconds
            setInterval(fetchScores, 30000);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/toast.js"></script>
</body>
</html>
