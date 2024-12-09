<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../login.html");
    exit();
}
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CaneLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
           

        .dashboard-container {
            display: flex;
        }

        .main-content {
            position: relative;
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('sugarcane.webp');
            background-size: cover;
        }

        .stats-grid {
            
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .stats-grid h2 {
            font-size: 4.5em;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .main-body{
            display: flex;
            justify-content: center;
            align-items: center;
            
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'template.php'; ?>
        
        <div class="main-content">
            
            <div class="stats-grid">
            
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            </div>

               
                <div class="main-body">
                   
                </div>
                
            </div>

           
        </div>
        
    </div>
</body>
</html> 