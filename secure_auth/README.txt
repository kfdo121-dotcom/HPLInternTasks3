SECUREAUTH - PHP/MySQL Authentication (XAMPP)

REQUIREMENTS
- XAMPP with Apache, MySQL/MariaDB and PHP
- A modern web browser
- Internet connection for the Bootstrap CDN (optional for backend testing)

INSTALLATION
1. Extract the secure_auth folder into:
   C:\xampp\htdocs\secure_auth

2. Open XAMPP Control Panel and start Apache and MySQL.

3. Open:
   http://localhost/phpmyadmin

4. Select the SQL tab and run the contents of setup.sql.

5. Check config/db.php. The default XAMPP local setup is:
   username = root
   password = ''
   If your MySQL root account has a password, update it in config/db.php.

6. Open:
   http://localhost/secure_auth/

7. Register a user, log in, test the dashboard, and log out.

PROJECT FILES
- index.php                 Home page
- register.php              Registration form and processing
- login.php                 Login form and processing
- dashboard.php             Protected account dashboard
- logout.php                CSRF-protected POST logout
- setup.sql                 Database creation script
- config/db.php             PDO database connection
- config/session.php        Session cookie configuration and inactivity timeout
- includes/auth.php         HTML escaping, CSRF and login helpers

SECURITY NOTES
- Passwords are hashed with PHP password_hash() using bcrypt.
- Login uses password_verify().
- Database queries use PDO prepared statements.
- Forms use CSRF tokens.
- Protected pages call require_login().
- Session IDs are regenerated after login.
- Output is HTML-escaped to help prevent XSS.
- This is a local educational project, not a complete production deployment.
  Add login rate limiting, email verification, password reset safeguards,
  HTTPS, monitoring, and a least-privilege database account before deployment.
- For local HTTP, the session cookie's Secure flag is disabled automatically.
  Use HTTPS for public deployments.
