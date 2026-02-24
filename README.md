# Gym Management System

The Gym Management System is a web-based application developed to streamline and automate the daily operations of a gym. The system helps manage members, trainers, subscriptions, payments, and administrative tasks efficiently.

This project focuses on improving operational efficiency, reducing manual record-keeping, and providing a structured management solution for gym administrators.

---

## Live Demo

Production URL:  

GitHub Repository:  
https://github.com/suzzzel5/Gym-Management-System

---

## Project Overview

The Gym Management System was developed to digitize gym operations. It replaces manual processes such as paper-based member records and payment tracking with a centralized database-driven system.

The system supports both administrative and user-level functionalities.

---

## Core Features

### Admin Module
- Admin authentication system
- Add, update, and delete members
- Manage trainer information
- Create and manage membership plans
- Track payments and subscriptions
- Dashboard overview

### Member Management
- Register new members
- Update member details
- Assign membership plans
- Track membership validity
- Payment record management

### Trainer Management
- Add and manage trainer profiles
- Assign trainers to members
- Maintain trainer records

---

## Technology Stack

Frontend:
- HTML
- CSS
- JavaScript

Backend:
- PHP

Database:
- MySQL

Server Environment:
- XAMPP / Apache

---

## System Architecture

The system follows a client-server architecture:

- Client: Web browser interface
- Server: PHP-based backend logic
- Database: MySQL for storing member, trainer, and payment data

---

## Installation Guide

1. Clone the repository:

```bash
git clone https://github.com/suzzzel5/Gym-Management-System.git
```

2. Move the project folder to your web server directory (e.g., `htdocs` for XAMPP).

3. Open phpMyAdmin and create a new database.

4. Import the provided SQL file into the database.

5. Update the database configuration file with your credentials:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "gym_management";
```

6. Start Apache and MySQL from XAMPP.

7. Open in browser:

```
http://localhost/Gym-Management-System
```

---

## Database Structure

The database includes tables such as:

- Admin
- Members
- Trainers
- Membership Plans
- Payments

These tables are relationally connected to ensure proper data integrity and management.

---

## Security Features

- Session-based authentication
- Role-based access (Admin)
- Server-side validation
- Structured database queries

---

## Future Enhancements

- Member login portal
- Online payment integration
- Attendance tracking system
- Automated membership expiration alerts
- Reporting and analytics dashboard
- Email and SMS notifications

---

## Author

Sujal Maharjan  
Web Developer  
Kathmandu, Nepal  

---

## License

This project is developed for educational and portfolio purposes.
