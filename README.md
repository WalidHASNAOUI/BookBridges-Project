# BookBridge Mini Project

BookBridge is a mini web application for managing and rating books. Users can search for books, view book details, rate books, and manage their profiles.

## Features

    - User registration and login
    - View and search books by categories
    - Rate books
    - View book details
    - User profile management with a list of rated books and average ratings

## Prerequisites

    - PHP 7.x or higher
    - MySQL 5.x or higher
    - Apache or any other web server
    - Composer (for dependency management, if needed)

## Configuration

### Step 1: Clone the Repository

 - Clone the repository to your local machine:
```bash
git clone https://github.com/WalidHASNAOUI/bookbridge-mini-project.git
cd bookbridge-mini-project
```

### Step 2: Configure the Database

    - Create a new MySQL database : CREATE DATABASE book_bridge;
    - Import the provided SQL file to create the necessary tables and insert initial data

### Step 3: Update Database Configuration

    - Edit the database connection settings in the PHP files (categories_books.php, profile.php, book_details.php, etc.) to match your MySQL credentials.

```bash
$servername = "localhost"; $username = "yourusername"; $password = "yourpassword"; $dbname = "book_bridge";
```

## Usage

### User Registration

    - Navigate to the signup page: http://localhost/bookbridge/signup.php
    - Fill in the required information (name, email, password, etc.) and submit the form.
    - You will be redirected to the login page upon successful registration.

### User Login

    - Navigate to the login page: http://localhost/bookbridge/login.php
    - Enter your email and password to log in.
    - You will be redirected to the main page upon successful login.

### Viewing and Rating Books

    - Navigate to the main page: http://localhost/bookbridge/categories_books.php
    - Browse or search for books by categories.
    - Click on a book to view its details.
    - To rate a book, select the number of stars (1-5) and click the "Submit" button.
    - A popup message will confirm that your rating has been submitted successfully.

### User Profile Management

    - After logging in, click the "Profile" button in the header.
    - View your profile information, including name, email, age, and the number of books you have rated.
    - The profile page also lists all the books you have rated, along with the average rating for each book.

### Updating Password

    - After logging in, click the "Update Password" button in the header.
    - Fill in the required information (current password, new password, confirm new password) to update your password.
    - You will be redirected to the login page upon successful password update.
