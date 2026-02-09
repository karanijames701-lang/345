# MySQL Database Setup Instructions

This guide explains how to set up the MySQL database for your user credentials system.

## Prerequisites

- XAMPP, WAMP, MAMP, or any local server with PHP and MySQL
- phpMyAdmin access
- Web browser

## Step 1: Start Your Local Server

1. Start Apache and MySQL services from your control panel (XAMPP/WAMP/MAMP)
2. Verify services are running

## Step 2: Create Database

### Option A: Using phpMyAdmin

1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar
3. Enter database name: `user_credentials_db`
4. Choose collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Option B: Using SQL

1. Open phpMyAdmin
2. Click on "SQL" tab
3. Run this command:
   ```sql
   CREATE DATABASE user_credentials_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

## Step 3: Import Database Schema

1. In phpMyAdmin, select the `user_credentials_db` database from the left sidebar
2. Click on the "Import" tab
3. Click "Choose File" and select `database.sql`
4. Scroll down and click "Go"
5. You should see a success message

## Step 4: Configure Database Connection

1. Open `db-config.php` in a text editor
2. Update the database credentials:
   ```php
   define('DB_HOST', 'localhost');     // Usually 'localhost'
   define('DB_USER', 'root');          // Your MySQL username (default: 'root')
   define('DB_PASS', '');              // Your MySQL password (default: empty)
   define('DB_NAME', 'user_credentials_db');
   ```
3. Save the file

## Step 5: Place Files in Web Server Directory

1. Copy all project files to your web server directory:
   - **XAMPP**: `C:\xampp\htdocs\your-project-folder\`
   - **WAMP**: `C:\wamp64\www\your-project-folder\`
   - **MAMP**: `/Applications/MAMP/htdocs/your-project-folder/`

## Step 6: Update API URL

1. Open `config.js`
2. Update the API_URL to match your setup:
   ```javascript
   const API_URL = 'http://localhost/your-project-folder/api.php';
   ```
3. Save the file

## Step 7: Test the Setup

1. Open your browser and navigate to:
   ```
   http://localhost/your-project-folder/index.html
   ```

2. Test the login form:
   - Try the sample credentials:
     - Username: `demo_user`
     - Password: `demo123`

3. Check if data is being saved:
   - Go to phpMyAdmin
   - Select `user_credentials_db` database
   - Click on `user_credentials` table
   - Click "Browse" to view records

## Troubleshooting

### Database Connection Errors
- Verify MySQL service is running
- Check database credentials in `db-config.php`
- Ensure database `user_credentials_db` exists

### CORS Errors
- Make sure you're accessing via `http://localhost` not `file://`
- Check that Apache is running
- Verify `api.php` has correct CORS headers

### API Not Found (404)
- Verify the API_URL in `config.js` is correct
- Check that `api.php` is in the same folder as your HTML file
- Ensure Apache is serving files from the correct directory

## Database Structure

The `user_credentials` table has the following structure:

| Column | Type | Description |
|--------|------|-------------|
| id | VARCHAR(36) | Primary key (UUID) |
| username | VARCHAR(255) | Unique username |
| password_hash | VARCHAR(64) | SHA-256 hashed password |
| created_at | TIMESTAMP | Account creation time |
| last_login | TIMESTAMP | Last login time |
| account_type | VARCHAR(50) | 'personal' or 'business' |

## Security Notes

- The system uses SHA-256 for password hashing
- For production, consider using bcrypt or Argon2
- Always use HTTPS in production
- Never commit database credentials to version control
- Implement rate limiting for login attempts
- Add proper input validation and sanitization

## Sample Data

The database includes sample records for testing:

1. **Personal Account**
   - Username: `demo_user`
   - Password: `demo123`

2. **Business Account**
   - Username: `COMP001:USER001`
   - Password: `business123`

You can remove these by running:
```sql
DELETE FROM user_credentials WHERE username IN ('demo_user', 'COMP001:USER001');
```
