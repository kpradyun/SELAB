# Electricity Bill Generator & Management System

## Project Overview

The Electricity Bill Generator is a full-stack web application designed to digitize the billing cycle for electricity distribution companies. This system replicates the official TSSPDCL experience, replacing manual paper-based processes with a centralized digital database.

The application automates the end-to-end billing process, including user registration, monthly meter reading entry, and bill calculation. It implements a 3-Tier Role-Based Access Control (RBAC) architecture, ensuring secure and segregated functionality for Administrators, Employees, and Consumers.

**Repository URL:** <https://github.com/kpradyun/EBG>

## System Architecture & Features

The system is divided into three distinct modules based on user roles:

### 1. Administrator Module
The Administrator acts as the manager of the system, handling the onboarding of service connections, staff management, and payment status updates.
* **User Registration:** Registers new service connections with differentiated Service Numbers (Household, Commercial, or Industrial). Captures essential details including Name, Initial Current Reading, and Address.
* **Account Shell Creation:** Establishes the initial profile skeleton that allows users to claim their account via their Service Number.
* **Employee Management:** Capabilities to register and view employee records.
* **Payment Status Management:** Manually updates bill status (Paid/Unpaid) for consumers.
* **Data Oversight:** Full access to view registered consumers, employees, and generate normal views of consumer bills.

### 2. Employee Module
Field agents use this module to input data and generate bills during site visits.
* **Rapid Data Entry:** streamlined workflow where the employee simply enters the **Service Number** and **Current Reading**.
* **Smart Bill Generation:**
    * **Auto-Calculation:** Automatically computes consumption (Units = Current Reading - Previous Reading).
    * **Fixed Charges:** Automatically applies a standard fixed charge if the consumption is zero (Current Reading matches Previous Reading).
    * **Rolling Arrears:** Automatically detects unpaid previous bills and adds the outstanding amount to the current month's total.
    * **Late Fines:** Automatically adds a fine to the total amount if the bill remains unpaid past the due date.

### 3. Consumer Module
This module allows consumers to monitor their electricity usage and billing history.
* **Bill History:** Provides a comprehensive tabular view of all historical electricity bills.
* **Status Tracking:** Allows users to see if their bills have been marked as "Paid" or "Unpaid" by the administration.
* **Thermal Receipt View:** Generates a digital representation of the bill that visually replicates the official TSSPDCL thermal paper receipt, styled using CSS with monospace fonts.

## Technical Specifications

* **Frontend:** HTML5, CSS3 (Custom styling for thermal receipt replication)
* **Backend:** PHP (Session management, business logic, role validation)
* **Database:** MySQL (Relational schema linking Users to Monthly Bills)
* **Server Environment:** Apache HTTP Server (via WAMP/XAMPP)

## Project Structure

**Core & Authentication**
* `index.html` - Landing page.
* `login.php` - Authentication handler.
* `logout.php` - Session termination script.
* `db.php` - Database connection configuration.
* `style.css` - Global stylesheet.

**Administrator Files**
* `admin_dashboard.php` - Central control panel for admins.
* `register_user.php` / `register_user_form.php` - Scripts for onboarding new customers.
* `register_employee.php` / `register_employee_form.php` - Scripts for onboarding staff.
* `view_users.php` - List of all registered consumers.
* `view_employees.php` - List of all registered staff.
* `view_bills.php` - Interface for admin to view consumer bills.

**Employee Files**
* `employee_dashboard.php` - Dashboard for field agents.
* `employee_reading.php` / `employee_reading_form.php` - Interface for recording meter readings.

**Consumer Files**
* `user_view.php` - Customer dashboard.
* `view_user_bills.php` - Bill history interface.
* `bill.php` - The thermal receipt generation view.

## Installation & Setup

1.  **Prerequisites:**
    Ensure you have a local server environment installed, such as XAMPP or WAMP, which includes Apache, MySQL, and PHP.

2.  **Clone the Repository:**
    Navigate to your server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP) and clone the project.

    ```bash
    git clone https://github.com/kpradyun/EBG.git
    ```

3.  **Database Configuration:**
    * Open your database management tool (e.g., phpMyAdmin).
    * Create a new database.
    * Import the project SQL schema file (if available) or configure the tables according to the PHP logic.
    * Update the `db.php` file with your local database credentials if they differ from the defaults.

4.  **Running the Application:**
    * Start the Apache and MySQL modules in your XAMPP/WAMP control panel.
    * Open a web browser and navigate to: `http://localhost/EBG`
