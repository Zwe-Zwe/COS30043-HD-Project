# BookNest - Book Store Application

## Setup Instructions

### Prerequisites

- XAMPP or similar local server environment (Apache, MySQL, PHP)
- Node.js and npm

### First-Time Setup

1. Clone this repository into your XAMPP htdocs folder
2. Create a database configuration by accessing:
   `http://localhost/COS30043-HD-Project/api/create-config.php?setup=true`
3. If the automatic setup fails, manually create a MySQL database named "booknest"
4. Import the schema from `schema.sql`
5. Install Node.js dependencies:
   ```
   npm install
   ```
6. Start the development server:
   ```
   npm run serve
   ```
7. Access the application at: `http://localhost:8080`

### Accessing on Other Devices

When opening the website on another device:

1. Ensure XAMPP is running on the host computer
2. Make sure the MySQL service is running
3. The application will automatically check and create the required database and tables if they don't exist

### Troubleshooting

- If you encounter database connection issues, check the configuration in `api/.env.php`
- For CORS issues, ensure the correct headers are set in your PHP files
- If automatic database creation fails, manually run the schema.sql script

## Features

- User registration and authentication
- Browse and search for books
- Shopping cart functionality
- Order processing and history
- Account management
