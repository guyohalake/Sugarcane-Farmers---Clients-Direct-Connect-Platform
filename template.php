<?php
// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        width: 250px;
        background-color: #2E7D32;
        color: white;
        min-height: 100vh;
        padding: 20px;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin: 8px 0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-links a:hover {
        background-color: #1B5E20;
    }

    .nav-links a.active {
        background-color: #1B5E20;
        font-weight: bold;
    }

    .nav-links i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .nav-link {
        position: relative;
    }

    .notification-dot {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 8px;
        height: 8px;
        background-color: #f44336;
        border-radius: 50%;
        display: none;
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>CaneLink</h2>
    </div>
    <ul class="nav-links">
        <li>
            <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                <i class="fas fa-box"></i>Products
            </a>
        </li>
        <li>
            <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>Orders
            </a>
        </li>
        
        
        <li>
            <a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>Profile
            </a>
        </li>
       
     
        <li>
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>Logout
            </a>
        </li>
    </ul>
</div>

<!-- Footer Section -->

</body>
</html>