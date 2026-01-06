# Logis - Distribution System Setup Guide

## Database Setup

1. **Start XAMPP:**

   - Start Apache and MySQL from XAMPP Control Panel

2. **Import Database:**

   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click on "SQL" tab
   - Copy and paste the contents of `database.sql` file
   - Click "Go" to execute

   OR use command line:

   ```bash
   mysql -u root -p < database.sql
   ```

## Default Login Credentials

### Admin Account

- Username: `admin`
- Password: `admin123`

### Customer Account (Sample)

- Username: `customer`
- Password: `customer123`

## File Structure

```
Logis/
├── config/
│   └── database.php          # Database connection configuration
├── database.sql              # Database schema and sample data
├── public/
│   ├── login/
│   │   ├── login.php        # Login page
│   │   ├── login_handler.php # Login processing
│   │   └── js/
│   │       └── script.js    # Login form JavaScript
│   ├── register/
│   │   ├── register.php     # Registration page
│   │   ├── register_handler.php # Registration processing
│   │   └── js/
│   │       └── script.js    # Registration form JavaScript
│   └── Customer/
│       └── Dashboard/
│           ├── dashboard.php # Customer dashboard
│           └── logout.php   # Logout handler
└── README_SETUP.md          # This file
```

## Access URLs

- **Login Page:** `http://localhost/logis/public/login/login.php`
- **Registration Page:** `http://localhost/logis/public/register/register.php`
- **Customer Dashboard:** `http://localhost/logis/public/Customer/Dashboard/dashboard.php` (after login)

## Features

### Login System

- Email or username authentication
- Password validation
- Session management
- User type-based redirection
- Error and success messages

### Registration System

- Business name
- Full name
- Email address
- Username (unique)
- Phone number
- Business address
- Province selection (Sri Lankan provinces)
- Password with confirmation
- Auto-login after registration

### Customer Dashboard

- Session protection (redirects to login if not authenticated)
- Displays business name and user info
- Logout functionality
- Dashboard statistics (ready for future features)

## Database Configuration

Edit `config/database.php` if your database settings are different:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'logis_db');
```

## User Types

The system supports three user types:

1. **customer** - Business customers (default for new registrations)
2. **admin** - System administrators
3. **staff** - RDC and logistics staff

Each user type can be redirected to their respective dashboards.

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection protection using prepared statements
- Session-based authentication
- CSRF protection ready
- Input validation (client and server-side)

## Troubleshooting

### Login not working

1. Make sure database is imported
2. Check database connection in `config/database.php`
3. Verify Apache and MySQL are running in XAMPP

### Registration errors

1. Check that email/username is unique
2. Verify all required fields are filled
3. Password must be at least 6 characters

### Cannot access dashboard

1. Make sure you're logged in
2. Check session is working (session.auto_start in php.ini)
3. Clear browser cookies and try again

## Next Steps

You can now:

1. Access the login page
2. Register a new account
3. Login with existing credentials
4. Access the customer dashboard
5. Build additional features (orders, catalog, etc.)

## Support

For issues or questions, check:

- Database connection settings
- PHP error logs in XAMPP
- Browser console for JavaScript errors
