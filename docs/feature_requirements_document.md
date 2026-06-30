# Feature Requirements Document: Asset/Order Management System (Lean V1)

## 1. System Overview

A streamlined web application designed to manage a catalog of items and process requests. Customers can browse available assets/items and submit requests, while Administrators manage the inventory and fulfill those requests.

## 2. User Roles & Access Control

The system restricts actions and views based on two distinct roles.

| Role | Core Responsibilities | Key Permissions |
|---|---|---|
| **Admin** | System oversight, inventory management, and order fulfillment. | Manage users, manage catalog/inventory, update all request statuses, full system access. |
| **Customer** | Browsing the catalog and submitting requests. | View public catalog, create requests, view their own request history. |

## 3. Core Modules & Features

### Module A: Authentication & User Management

- **Secure Login/Registration**: Standard authentication for customers to create accounts.
- **Role Assignment**: First user created (or defined in a seeder) is the Admin. All subsequent public registrations default to the Customer role.

### Module B: Catalog & Inventory Management

- **Item Masterlist (Admin)**: Admins can perform full CRUD (Create, Read, Update, Delete) operations on the items available in the system.
- **Stock Level Monitoring (Admin)**: Admins track current quantities and update stock levels.
- **Public Catalog (Customer)**: Customers can view available items and current stock status (e.g., "In Stock" or "Out of Stock").

### Module C: The Request/Order Workflow

This transaction engine is a direct two-step process between the Customer and the Admin.

- **Creation (Customer)**: Customers select items from the catalog, specify quantities, and submit a "Request Slip" or "Order." (Status: Pending).
- **Review & Approval (Admin)**: Admins view a global queue of all pending requests. They check physical stock and approve the order. (Status changes to Approved or Rejected).
- **Inventory Deduction (System)**: Upon Admin approval, the requested quantities are automatically deducted from the master inventory.
- **Tracking (Customer)**: Customers have a dashboard to see the real-time status of their specific requests.

### Module D: The Logger System

This feature is used to track who did what and when across the system, providing a basic audit trail for accountability and troubleshooting.

- **Activity Logging (System)**: Key actions are automatically recorded, including item creation/updates/deletion, order submissions, and order status changes (approved/rejected/fulfilled).
- **Log Entry Details**: Each log entry captures the acting user, the action performed, the affected record (e.g., which item or order), a timestamp, and relevant before/after values where applicable.
- **Audit View (Admin)**: Admins can view a chronological log of system activity, optionally filtered by user, action type, or date range.