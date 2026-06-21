# Student Management System

A PHP and MySQL web application for managing students, subjects, marks, and
attendance records. This repository extends the original open-source project
with validation, safer record management, search and filtering, and reusable
authentication and navigation components.

## Features

### Authentication

- User registration and login
- Password confirmation and minimum eight-character password validation
- Duplicate username detection
- Session-based protection for restricted pages
- Reusable authentication checks across the application

### Student Management

- Add, view, edit, and delete student records
- Duplicate enrollment-number detection
- Phone-number validation for 10- or 11-digit numbers
- Standardized department selection
- Search by student name or enrollment number
- Filter by department

### Subject Management

- Add, view, edit, and delete subjects
- Duplicate subject-name detection
- Subject search
- Safe deletion when marks or attendance records still reference a subject

### Marks Management

- Add, view, edit, and delete marks
- Decimal marks from 0.00 to 100.00
- Duplicate student-subject marks prevention
- Search by student, enrollment number, or subject
- Filter by subject
- Display student and subject names instead of raw database IDs

### Attendance Management

- Add, view, edit, and delete attendance records
- Link attendance records to both students and subjects
- Present/Absent selection
- Prevent future dates and dates more than 30 days in the past
- Search by student, enrollment number, or subject
- Filter by subject, date, and attendance status
- Display meaningful student and subject information

### Maintainability and Record Safety

- Shared authentication component in `includes/auth.php`
- Shared sidebar navigation in `includes/sidebar.php`
- Shared escaping, prepared-statement, and status-message helpers
- Dependency checking before deleting referenced students or subjects
- User-friendly feedback instead of raw database errors
- Prepared statements in the enhanced data-management operations

## Technologies

- PHP 8.2
- MySQL / MariaDB
- HTML5 and CSS3
- Bootstrap 5
- Font Awesome
- XAMPP

## Project Structure

```text
student-management-system/
|-- assets/
|   `-- style.css
|-- includes/
|   |-- auth.php
|   |-- helpers.php
|   `-- sidebar.php
|-- add_*.php
|-- edit_*.php
|-- view_*.php
|-- delete_*.php
|-- index.php
|-- login.php
|-- register.php
|-- logout.php
|-- db.php
|-- database.sql
`-- README.md
```

## Installation

### 1. Prerequisites

Install XAMPP or another environment that provides:

- Apache
- PHP 8.2 or later
- MySQL or MariaDB

### 2. Clone the Repository

Clone the group repository into the XAMPP `htdocs` directory:

```bash
git clone https://github.com/liwei-0405/student-management-system.git
```

The expected project location on Windows is:

```text
C:\xampp\htdocs\student-management-system
```

### 3. Create the Database

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Open [phpMyAdmin](http://localhost/phpmyadmin).
3. Create a database with the exact name:

```text
student_db
```

4. Select `student_db`.
5. Open the **Import** tab.
6. Import the provided `database.sql` file.

The application is configured to use `student_db` in `db.php`. Using a
different database name requires updating the `$dbname` value in `db.php`.

The imported schema includes:

- `users`
- `students`
- `subjects`
- `marks`
- `attendance`

It also includes the student-subject relationships used by marks and the
student-subject relationships used by attendance.

### 4. Verify the Database Configuration

The default local XAMPP configuration in `db.php` is:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";
```

Update these values only if your local MySQL credentials are different.

### 5. Run the Application

Open:

[http://localhost/student-management-system/](http://localhost/student-management-system/)

Default administrator account:

```text
Username: admin
Password: admin
```

## Usage

1. Log in using an existing account or create a new account.
2. Add subjects before entering marks or attendance.
3. Add student records with a unique enrollment number and valid phone number.
4. Record marks by selecting a student and subject.
5. Record attendance by selecting a student, subject, date, and status.
6. Use the search and filter controls on the record-viewing pages.
7. Remove related marks or attendance records before deleting referenced
   students or subjects.

## Validation and Testing

To check one PHP file for syntax errors:

```bash
php -l index.php
```

To check every PHP file using PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

The enhanced system should also be tested through the browser using valid,
invalid, duplicate, and dependency-related scenarios.

## Version-Control Workflow

Contributors should work on separate feature branches and submit pull requests
instead of pushing changes directly to `main`.

```bash
git checkout main
git pull origin main
git checkout -b feature/your-feature-name
```

After testing:

```bash
git add .
git commit -m "Describe the completed change"
git push origin feature/your-feature-name
```

## Project Background

This repository is based on the original
[sayan365/student-management-system](https://github.com/sayan365/student-management-system)
project. The maintenance and enhancement work was completed for the CSE6364
Software Evolution and Maintenance group project.
