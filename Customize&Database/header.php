<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest - Online Bookstore</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/finalproject/booknestonlinebookstoresystem/Customize&Database/design.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/finalproject/booknestonlinebookstoresystem/Customer/index.php">📚 BookNest</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/selectBook.php"><i class="fas fa-book"></i> Books</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/shoppingCart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/orderHistory.php"><i class="fas fa-box"></i> My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/wishList.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'staff'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/manage_books.php">Manage Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/manage_orders.php">Orders</a></li>
                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/mainPage.php">Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manageBook.php"> Update Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manageOrder.php">Orders</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= $_SESSION['user_role'] ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../Customer/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
<div class="toast-container"></div>