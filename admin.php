<?php
require_once 'config.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['contact_id']]);
        $success_message = "Status updated successfully!";
    } catch (Exception $e) {
        $error_message = "Error updating status: " . $e->getMessage();
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact'])) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$_POST['contact_id']]);
        $success_message = "Contact deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting contact: " . $e->getMessage();
    }
}

// Fetch all contacts
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Error fetching contacts: " . $e->getMessage();
    $contacts = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Contact Messages</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .contact-grid {
            display: grid;
            gap: 20px;
        }

        .contact-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .contact-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .contact-info {
            flex: 1;
        }

        .contact-info h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .contact-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-new {
            background: #fff3cd;
            color: #856404;
        }

        .status-read {
            background: #cce5ff;
            color: #004085;
        }

        .status-replied {
            background: #d4edda;
            color: #155724;
        }

        .contact-subject {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }

        .contact-message {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }

        .contact-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(110%);
        }

        select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }

        .date {
            color: #6c757d;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .contact-header {
                flex-direction: column;
                align-items: stretch;
            }

            .contact-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Contact Messages Dashboard</h1>
            <p>Manage and respond to portfolio contact messages</p>
        </div>

        <div class="content">
            <?php if (isset($success_message)): ?>
                <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo count($contacts); ?></h3>
                    <p>Total Messages</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($contacts, fn($c) => $c['status'] === 'new')); ?></h3>
                    <p>New Messages</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($contacts, fn($c) => $c['status'] === 'replied')); ?></h3>
                    <p>Replied</p>
                </div>
            </div>

            <div class="contact-grid">
                <?php if (empty($contacts)): ?>
                    <div class="contact-card">
                        <p style="text-align: center; color: #666;">No contact messages yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <div class="contact-card">
                            <div class="contact-header">
                                <div class="contact-info">
                                    <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($contact['email']); ?></p>
                                    <span class="date"><?php echo date('M j, Y g:i A', strtotime($contact['created_at'])); ?></span>
                                </div>
                                <span class="status-badge status-<?php echo $contact['status']; ?>">
                                    <?php echo ucfirst($contact['status']); ?>
                                </span>
                            </div>

                            <div class="contact-subject">
                                📋 <?php echo htmlspecialchars($contact['subject']); ?>
                            </div>

                            <div class="contact-message">
                                <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                            </div>

                            <div class="contact-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                    <select name="status">
                                        <option value="new" <?php echo $contact['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $contact['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="replied" <?php echo $contact['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                </form>

                                <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>?subject=Re: <?php echo htmlspecialchars($contact['subject']); ?>" class="btn btn-success">
                                    Reply via Email
                                </a>

                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                    <button type="submit" name="delete_contact" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>