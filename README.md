# Asset Management

A streamlined web application for managing a catalog of items and processing requests. Customers browse available assets and submit requests; admins manage inventory and fulfill those requests.

Built with Laravel and Livewire as a learning project to get hands-on with the Laravel ecosystem and Docker.

## Tech stack

- **Backend**: Laravel
- **Frontend**: Livewire
- **Database**: MySQL
- **Roles & Permissions**: [Spatie laravel-permission](https://spatie.be/docs/laravel-permission)
- **Containerization**: Docker

## Features

- **Authentication & roles** 
  - registration/login, with the first user (or seeded user) assigned the Admin role and all others defaulting to Customer.
- **Catalog & inventory** 
  - admin CRUD over items, stock level tracking, and a public catalog showing in-stock/out-of-stock status.
- **Request workflow** 
  - customers submit requests (pending), admins review and approve/reject/fulfill them, and inventory is automatically deducted on approval.
- **Request tracking** 
  - customers can view the real-time status of their own requests.
- **Activity logging** 
  - key actions (item changes, order submissions, status changes) are recorded with the acting user, timestamp, and affected record for basic auditability.


## Project structure notes

- `database/migrations` 
  - schema for items, orders, order_items, and activity_logs (users and roles/permissions tables are Laravel/Spatie defaults).
- `app/Livewire` 
  - Livewire components powering the catalog, request workflow, and admin dashboards.
- `docs/` 
  - The directory for all important documentations regarding the project including the `feature requirements documentation (FRD)` and `entity relationship diagram (ERD)`.

