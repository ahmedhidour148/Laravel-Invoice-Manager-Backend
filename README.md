Invoice & Client Management API
A RESTful backend API for managing clients and invoices for small businesses. Users can register, verify their email, authenticate, and manage their own clients and invoices, including invoice line items.
Features
Authentication
User registration and login.
Email verification using a one-time code (OTP).
Token-based authentication with Laravel Sanctum.
Logout by revoking the current access token.
Rate limiting on registration and login endpoints.
Client Management
Create, list, view, update, and delete clients.
Each client belongs to the authenticated user.
Paginated client listing.
Invoice Management
Create, list, view, update, and delete invoices.
Associate invoices with clients.
Add invoice line items with a name, quantity, and unit price.
Calculate line-item totals and the invoice total when creating an invoice.
Automatically generate invoice numbers in the INV-001 format.
Paginated invoice listing.
Delete an individual line item while preventing deletion of the last remaining item.
Laravel Concepts Used
Laravel 12 and Eloquent ORM relationships.
Form Request validation.
API Resources for consistent JSON responses.
Laravel Sanctum.
Database migrations, factories, and seeders.
Database transactions when creating and updating invoices.
Events and listeners.
Eloquent model observers.
Rate limiting.
Pagination.
Mailables for email verification.
Tech Stack
PHP: 8.2+
Framework: Laravel 12
Authentication: Laravel Sanctum
Database: SQLite by default (can be configured for another Laravel-supported database)
API format: JSON
Getting Started
Prerequisites
Make sure the following are installed:
PHP 8.2 or later
Composer
Node.js and npm (only needed if you want to build the included frontend assets)
Installation
Clone the repository:
git clone <YOUR_GITHUB_REPOSITORY_URL>
cd <YOUR_PROJECT_DIRECTORY>
Install PHP dependencies:
composer install
Create your environment file:
cp .env.example .env
On Windows Command Prompt, use:
copy .env.example .env
Generate the application key:
php artisan key:generate
Configure your database in the .env file.
The supplied .env.example is configured to use SQLite. Create the database file if it does not exist:
touch database/database.sqlite
On Windows, you can create an empty database.sqlite file inside the database folder manually.
If you prefer MySQL, update the DB_* values in .env with your database name, host, username, and password.
Run the migrations:
php artisan migrate
Start the development server:
php artisan serve
The API will normally be available at:
http://127.0.0.1:8000/api
Email Verification Configuration
Registration sends an email containing a verification code. Configure the MAIL_* values in .env to use an email provider or a local mail testing service.
The default .env.example uses MAIL_MAILER=log, which writes outgoing email content to the Laravel log instead of delivering a real email. This is useful for local development; check storage/logs/laravel.log to retrieve the verification code when using the default configuration.
API Endpoints
All request and response bodies use JSON. Protected endpoints require the token returned by login:
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
Authentication
Method
Endpoint
Description
Authentication
POST
/api/register
Register a new user and send an email verification code
No
POST
/api/verify-email
Verify the user's email using the code
No
POST
/api/login
Log in and receive an access token
No
GET
/api/logout
Revoke the current access token
Yes
Registration example
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
Email verification example
{
  "email": "john@example.com",
  "code": "123456"
}
Login example
{
  "email": "john@example.com",
  "password": "password123"
}
Clients
All client endpoints require authentication.
Method
Endpoint
Description
GET
/api/clients
List the authenticated user's clients
POST
/api/clients
Create a client
GET
/api/clients/{clientId}
View a client
PUT
/api/clients/{clientId}
Update a client
DELETE
/api/clients/{clientId}
Delete a client
Create client example
{
  "name": "Acme Company",
  "email": "contact@acme.com",
  "phone": "+123456789",
  "address": "Main Street"
}
Invoices
All invoice endpoints require authentication.
Method
Endpoint
Description
GET
/api/invoices
List the authenticated user's invoices
POST
/api/invoices
Create an invoice with its line items
GET
/api/invoices/{invoiceId}
View an invoice
PUT
/api/invoices/{invoiceId}
Update an invoice
DELETE
/api/invoices/{invoiceId}
Delete an invoice
DELETE
/api/invoices/{invoiceId}/{itemId}
Delete a line item from an invoice
Create invoice example
{
  "client_id": 1,
  "title": "Website Development",
  "status": "draft",
  "issue_date": "2026-11-18",
  "due_date": "2026-11-30",
  "notes": "First project invoice",
  "items": [
    {
      "item_name": "Backend development",
      "quantity": 1,
      "price": 250
    },
    {
      "item_name": "API integration",
      "quantity": 2,
      "price": 75
    }
  ]
}
The line-item totals and invoice total are calculated by the application when the invoice is created; they do not need to be supplied in the request.
Data Ownership
Client and invoice queries are scoped to the authenticated user. A user can only access their own clients and invoices through the API.
Project Structure
app/
├── Events/
├── Http/
│   ├── Controllers/api/
│   ├── Requests/
│   └── Resources/
├── Listeners/
├── Mail/
├── Models/
├── Observers/
└── Providers/
database/
├── factories/
├── migrations/
└── seeders/
routes/
└── api.php
Testing
The repository currently includes Laravel's example Feature and Unit tests. Dedicated automated tests for the invoice, client, and authentication API workflows have not yet been added.
To run the existing test suite:
php artisan test
Notes
This repository contains the backend API; no separate frontend application is included.
Update the environment configuration for your own database and email service before using the application outside local development.
Do not commit your .env file or real credentials.
License
This project is provided for learning and portfolio purposes. Add a license file if you intend to distribute it under a specific open-source license.
