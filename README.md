# BookNest Online Bookstore System

## Introduction
BookNest is an online bookstore system that allows customers to browse books, add to cart, make payments, and leave reviews. Staff can manage orders and books, while admin has full control over users, orders, and reports.

## Technologies Used
- PHP 
- MySQL (phpMyAdmin)
- Bootstrap 5
- HTML/CSS/JavaScript

## Installation Guide

### Step 1: Clone the Repository
git clone https://github.com/your-username/booknestonlinebookstoresystem.git

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
- Place the project folder in htdocs (for XAMPP) or www (for WAMP)
- Access the website at: 
  http://localhost/finalproject/booknestonlinebookstoresystem/Customer/index.php

## Default Login Accounts

Role: Customer
Email: phangyuxue@gmail.com
Password: 12345678

Role: Staff
Email: staff123@gmail.com
Password: 0123456789

Role: Admin
Email: phangyuxue@graduate.utm.my
Password: 12345678

## User Roles & Features

### Customer
- Browse and search books
- Add to cart and checkout
- Upload payment proof
- Leave reviews and ratings
- Wishlist management
- Order history

### Staff
- Manage books (add/edit/delete)
- Update order status
- Cancel orders

### Admin
- Manage users (customers & staff)
- Manage book categories
- View sales reports
- Approve password change requests

## Project Structure

booknestonlinebookstoresystem/
├── Admin/           (Admin panel)
├── Staff/           (Staff panel)
├── Customer/        (Customer pages)
├── Customize&Database/  (Database config & shared files)
├── Image/           (Book images)
├── Payment/         (ToyyibPay integration)
├── assets/          (Uploaded payment proofs)
└── database/        (SQL file)

## Contact
For any issues, please contact: [your email]
