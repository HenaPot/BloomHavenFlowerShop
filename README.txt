DEPLOYED URL: https://bloomhaven-frontend-app-smnzi.ondigitalocean.app/#landing

--------------------------------------------------------------------------------------

Bloomhaven - Web Project:

A full-stack web application for managing user orders and interactions, built with a modern and lightweight tech stack.

Description:

Bloomhaven is a web application designed with the goal of showcasing a clean architecture and practical functionality for user management and order processing.

This project uses FlightPHP as a lightweight backend microframework to handle REST APIs, MySQL as the relational database, and standard HTML, CSS, and JavaScript for the frontend.

The backend is organized using clean routing, middleware for error handling, and secure database interactions via PDO (PHP Data Objects).

Tech Stack:

Layer	Technology	Description
Frontend	HTML, CSS, JavaScript	Interface design, dynamic interactions
Backend	FlightPHP (Microframework)	Routing, API endpoints, error handling
Database	MySQL	Persistent storage (users, orders)
Server Tools	XAMPP (Apache, MySQL, PHP)	Local development environment

Main Features:

User Management

Create, update, and delete user profiles

User IDs linked properly with orders table using foreign keys

Order Management

Create and manage orders

Orders associated with users

Error Handling

Global error handling using FlightPHP middleware

Responsive HTML/CSS interface

JavaScript for form handling and dynamic UI behavior

Database Integrity

Proper MySQL foreign key constraints between user and orders

Safe deletion or updates on foreign key relations (ON DELETE SET NULL, ON UPDATE RESTRICT)


How to Run the Project:

Download and install XAMPP.

Clone or Copy the Project Files

Place the project folder (Bloomhaven/) inside your XAMPP htdocs/ directory.

Import the Database

Open phpMyAdmin at localhost/phpmyadmin.

Create a new database called bloomhaven.

Import the provided bloomhaven.sql file into it.

Composer Install

Navigate to backend/ folder.

Install dependencies:
composer install
Run Apache and MySQL from XAMPP Control Panel

Visit the Website

Open your browser and go to: http://localhost/Bloomhaven/index.html

Password Handling:

Passwords are securely hashed (e.g., using bcrypt format, like $2y$10$...).

Author
Project Name: Bloomhaven

Stack: Full-stack Web Development

Technologies: HTML, CSS, JavaScript, PHP (FlightPHP), MySQL

Environment: XAMPP (Apache, PHP, MySQL)

Current Milestone: 3 (fully complete)








