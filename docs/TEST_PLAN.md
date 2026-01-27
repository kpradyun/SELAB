# Test Plan

## 1. Introduction
This document outlines the test strategy for the refactored Electricity Bill System.

## 2. Test Environment
- **OS**: Linux
- **Server**: Apache
- **Database**: MySQL

## 3. Test Cases

### 3.1 Authentication

| Test ID | Functionality | Input | Expected Output | Actual Output | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| TC-001 | Admin Login | Email: `admin@example.com`<br>Pass: `ailab` | Redirect to `admin_dashboard.php` | | Pending |
| TC-002 | Employee Login | Email: `uday@gmail.com`<br>Pass: `ailab` | Redirect to `employee_dashboard.php` | | Pending |
| TC-003 | Invalid Pass | Email: `admin@example.com`<br>Pass: `wrong` | Error: "Incorrect password." | | Pending |

### 3.2 Bill Management

| Test ID | Functionality | Input | Expected Output | Actual Output | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| TC-010 | Calculate Bill (HH, 50u) | Units: 50, Type: HOUSEHOLD | Amount: 75.00 (50*1.5) | | Pending |
| TC-011 | Calculate Bill (HH, 120u) | Units: 120, Type: HOUSEHOLD | Amount: 395.00 (50*1.5 + 50*2.5 + 20*3.5) | | Pending |
| TC-012 | Calculate Bill (Com, 60u) | Units: 60, Type: COMMERCIAL | Amount: 160.00 (50*2.5 + 10*3.5) | | Pending |
| TC-013 | Calculate Bill (Ind, Min) | Units: 0, Type: INDUSTRY | Amount: 100.00 (Min Charge) | | Pending |
| TC-014 | Calculate Bill (Ind, low) | Units: 5, Type: INDUSTRY | Amount: 17.50 (5 * 3.5) | | Pending |
| TC-011 | API Get Bill | URL: `api/get_bill.php?bill_no=1001` | JSON with Status "success" | | Passed |
| TC-012 | API Invalid | URL: `api/get_bill.php` | JSON with Status "error" | | Pending |

## 4. Test Report Template

**Date Executed**: 2024-01-27
**Tester**: Automated / Manual

*Summary of results to be filled after execution.*

