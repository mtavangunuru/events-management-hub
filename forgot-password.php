<?php
require_once 'config/db.php';
$pageTitle = "Reset Password - Nuustar Events";

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($identifier) || empty($newPassword) || empty($confirmPassword)) {
        $errorMsg = "Please fill in all required fields.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMsg = "New passwords do not match. Please re-type.";
    } elseif (strlen($newPassword) < 6) {
        $errorMsg = "Password must be at least 6 characters long.";
    } else {
        // Find user by email or phone
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = :email OR phone = :phone LIMIT 1");
        $stmt->execute([
            'email' => $identifier,
            'phone' => $identifier
        ]);
        $user = $stmt->fetch();

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $upStmt = $pdo->prepare("UPDATE users SET password = :pwd WHERE id = :id");
            $upStmt->execute(['pwd' => $hashedPassword, 'id' => $user['id']]);
            $successMsg = "Password for " . htmlspecialchars($user['name']) . " has been reset successfully! You can now log in.";
        } else {
            $errorMsg = "No account found with that email address or phone number.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="max-width: 480px; padding: 60px 20px 100px;">
    <div class="content-box" style="padding: 35px; box-shadow: var(--card-shadow-hover);">
        <div style="text-align: center; margin-bottom: 25px;">
            <div class="category-icon" style="margin: 0 auto 15px; width: 55px; height: 55px; font-size: 1.5rem; background: #fef3c7; color: #b45309;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--dark);">Reset Your Password</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Enter your registered details to set a new password</p>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo $successMsg; ?>
                <div style="margin-top: 12px;">
                    <a href="login.php" class="btn btn-primary btn-sm btn-block">Click Here to Login Now</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($successMsg)): ?>
        <form action="forgot-password.php" method="POST">
            <div class="form-group" style="margin-bottom: 18px;">
                <label>Registered Email or Phone Number</label>
                <input type="text" name="identifier" placeholder="e.g. admin@nuustar.com or 0754XXXXXX" required>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="At least 6 characters" required>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Re-type new password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 1rem;">
                <i class="fa-solid fa-arrows-rotate"></i> Update Password
            </button>
        </form>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <span>Remember your password? </span>
            <a href="login.php" style="color: var(--primary); font-weight: 700;">Back to Login</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
