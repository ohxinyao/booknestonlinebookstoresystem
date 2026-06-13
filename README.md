# BookNest Online Bookstore System

## Introduction
BookNest is an online bookstore system that allows customers to browse books, add to cart, make payments, and leave reviews. Staff can manage orders and books, while admin has full control over users, orders, and reports.

## Live Demo (Domain Server)
The system is deployed online. You can access the live version at:
https://booknestonlinebookstore.infinityfreeapp.com/finalproject/booknestonlinebookstoresystem/Customer/index.php

## Note: Online payment (ToyyibPay) is only available on the live server due to callback URL requirements. Localhost does not support payment callbacks.

## Technologies Used
- PHP 
- MySQL (phpMyAdmin)
- Bootstrap 5
- HTML/CSS/JavaScript
- ToyyibPay Payment Gateway (Online Payment)

## Installation Guide (Localhost)

### Step 1: Clone the Repository
git clone : https://github.com/ohxinyao/booknestonlinebookstoresystem.git

### Step 2: Setup Database
- Open phpMyAdmin
- Create a new database named booknest_database
- Import the SQL file from database/booknest_database.sql

### Step 3: Configure Database Connection
Edit the file: Customize&Database/setDatabase.php

$host = 'localhost';
$dbname = 'booknest_database';
$username = 'root';
$password = '';

### Step 4: Run the Project
- Place the project folder in htdocs (for XAMPP)
- Access the website at: 
  http://localhost/finalproject/booknestonlinebookstoresystem/Customer/index.php

### Important Note for Localhost
- Online payment (ToyyibPay) is disabled on localhost because payment callbacks require a public URL.
- To test online payment, please use the live server link above.
- On localhost, customers can still place orders using the "Manual Payment" option (upload bank transfer proof).

## Default Login Accounts

| Role | Email | Password |
|------|-------|----------|
| Customer | phangyuxue@gmail.com | Phang12! |
| Staff | projectfinal432@gmail.com | Projectfinal432! |
| Admin | phangyuxue@graduate.utm.my | Admin321! |

## User Roles & Features

### Customer
- Browse and search books
- Add to cart and checkout
- Online payment via ToyyibPay (on live server)
- Manual payment with bank transfer proof upload (localhost & live server)
- Leave reviews and ratings
- Wishlist management
- Order history
- Live chat (Sender/Receiver)

### Staff
- Manage books (add/edit/delete)
- Update order status
- Cancel orders
- Live Chat (Sender/Receiver)

### Admin
- Manage users (customers & staff)
- Manage book categories
- View sales reports
- Approve password change requests

## Project Structure

..htdocs/finalproject/booknestonlinebookstoresystem/
├── Admin/           (Admin panel)
├── Staff/           (Staff panel)
├── Customer/        (Customer pages)
├── Customize&Database/  (Database config & shared files)
├── Image/           (Book images)
├── Payment/         (ToyyibPay integration)
├── assets/          (Uploaded payment proofs)
└── database/        (SQL file)

## Localhost vs Live Server Differences

| Feature | Localhost | Live Server |
|---------|-----------|-------------|
| Browse books | ✅ Yes | ✅ Yes |
| Add to cart | ✅ Yes | ✅ Yes |
| Manual payment (upload proof) | ✅ Yes | ✅ Yes |
| Online payment (ToyyibPay) | ❌ No (callback requires public URL) | ✅ Yes |
| Full system functionality | ✅ Yes (except online payment) | ✅ Yes |
