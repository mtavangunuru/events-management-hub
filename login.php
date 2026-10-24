<?php
require_once 'config/db.php';
$pageTitle = "Login - Nuustar Events";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errorMsg = '';

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['login_input'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($loginInput) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone LIMIT 1");
        $stmt->execute([
            'email' => $loginInput,
            'phone' => $loginInput
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check account status
            if ($user['status'] === 'pending') {
                $errorMsg = "Your account is currently awaiting approval from Nuustar Events Administrator. You will receive an alert once your account is approved.";
            } elseif ($user['status'] === 'suspended' || $user['status'] === 'rejected') {
                $errorMsg = "Your account has been suspended or declined. Please contact Nuustar Events support.";
            } else {
                // Account is active! Store session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role or intended destination
                $redirectUrl = trim($_GET['redirect'] ?? '');
                if (!empty($redirectUrl)) {
                    header("Location: " . $redirectUrl);
                    exit;
                }

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                    exit;
                } elseif ($user['role'] === 'vendor') {
                    header("Location: vendor/dashboard.php");
                    exit;
                } else {
                    header("Location: customer-dashboard.php");
                    exit;
                }
            }
        } else {
            $errorMsg = "Invalid email/phone or password. Please try again.";
        }
    } else {
        $errorMsg = "Please enter both your email/phone and password.";
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="max-width: 480px; padding: 60px 20px 100px;">
    <div class="content-box" style="padding: 35px; box-shadow: var(--card-shadow-hover);">
        <div style="text-align: center; margin-bottom: 25px;">
            <div class="category-icon" style="margin: 0 auto 15px; width: 55px; height: 55px; font-size: 1.5rem;">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--dark);">Account Login</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">For Customers, Service Vendors, and Administrators</p>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group" style="margin-bottom: 18px;">
                <label>Email Address or Phone Number</label>
                <input type="text" name="login_input" placeholder="e.g. admin@nuustar.com or 0754XXXXXX" required>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="margin-bottom: 0;">Password</label>
                    <a href="forgot-password.php" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">Forgot Password?</a>
                </div>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 25px; font-size: 0.85rem; color: var(--text-muted);">
                <input type="checkbox" id="remember" style="accent-color: var(--primary);">
                <label for="remember">Remember me on this device</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 1rem;">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <span>Don't have an account yet? </span>
            <a href="register.php" style="color: var(--primary); font-weight: 700;">Create Account</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
