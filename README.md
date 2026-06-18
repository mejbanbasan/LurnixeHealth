# Lurnixe Family Health Card System

A professional, secure, and fully dynamic **Core PHP** web application designed for managing family health cards, registering members, customizing public site content, and handling visitor contact inquiries.

---

## 🚀 Key Features

1. **Family Health Cards**:
   - Register and manage members with dynamic QR codes.
   - Generate premium, high-quality front-and-back health card PDFs using **TCPDF**.
2. **Dynamic preferences panel**:
   - Manage branding (site logo, name, tagline, favicon).
   - Dynamic navbar configuration with dropdown support.
   - Editable home page counters and social links.
3. **Contact Inquiries**:
   - Professional communication workflow (*New* ➔ *Read* ➔ *Replied* ➔ *Closed*).
   - In-app email response composing and sent response viewer.
4. **Activity Logs & Audit Trail**:
   - Comprehensive tracking of admin actions, target members, timestamps, and IP addresses.

---

## 🛠️ Local Installation & Setup

### Prerequisites
- PHP 8.0 or higher.
- MySQL / MariaDB.
- Apache Server (Apache `mod_rewrite` enabled).

### Setup Instructions
1. **Clone or Download** the project to your local web root directory (e.g. `xampp/htdocs/LurnixeHealth`).
2. **Import Database**:
   - Create a database named `lurnixe_health` in phpMyAdmin.
   - Import the `database.sql` file located in the root of the project.
3. **Configure Database Connection**:
   - Open `config/config.php` and update the credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'lurnixe_health');
     ```
4. **Access the Site**:
   - Public Website: `http://localhost/LurnixeHealth/`
   - Admin Control Panel: `http://localhost/LurnixeHealth/admin/login.php`
5. **Default Admin Credentials**:
   - **Email**: `admin@lurnixehealth.com`
   - **Password**: `Admin@123`

---

## 🌐 Hostinger Production Deployment Checklist

If deploying to Hostinger (or other shared hosting under the domain `lurnixehealth.com`):

1. **Delete WordPress**: Wipe out default WordPress files inside your `public_html/` directory.
2. **Upload Core Files**: Zip and upload the project files directly to the `public_html/` root.
   - *Note: Do not upload the `scratch/` folder or `database.sql` to your public folder for security reasons.*
3. **Set File Permissions**:
   - Directories: `0755`
   - Files: `0644`
   - Uploads folder: Ensure `public_html/uploads/` is writeable (`0755` recursively).
4. **Import DB**: Create a database in your Hostinger panel and import `database.sql` via phpMyAdmin.
5. **Update DB Config**: Edit `public_html/config/config.php` with Hostinger's database host, user, database name, and password.
6. **Force SSL (HTTPS)**: Set up SSL on Hostinger and add these redirect rules to the top of your `.htaccess` file:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```
