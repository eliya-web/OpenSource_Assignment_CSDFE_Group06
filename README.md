# Security Incident Reporting System

**Degree Program:** Bachelor of Science in Cyber Security and Digital Forensics Engineering
**Group Number:** Group 6
**Project Description:** A web-based security incident reporting system that allows organizations to report, track, and manage security incidents. Developed as part of CP 222 - Open Source Technologies assignment.

## Project Overview
The Security Incident Reporting System (SIRS) enables organizations to document and manage security incidents efficiently. Users can register new incidents, view all recorded incidents, search by incident ID, filter by status, and view detailed incident information. The system includes a complete user management module with registration, login, logout, and password recovery, all protected by session-based authentication and brute-force prevention. It features a modern, responsive user interface with dark mode support and smooth page transitions.

## Installation Steps

### Requirements:
1. XAMPP (PHP 7.4+ and MySQL)
2. Git (for version control)
3. Web browser (Chrome, Firefox, or Edge)

### Step-by-Step Installation:

1. **Install XAMPP**
   - Download from https://www.apachefriends.org/
   - Install with default settings
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Clone or Download the Repository**
   ```
   git clone https://github.com/eliya-web/OpenSource_Assignment_CSDFE_Group06.git
   ```
   Or download the ZIP and extract it.

3. **Move Project to XAMPP**
   - Move the project folder to `C:\xampp\htdocs\`

4. **Create the Database**
   - Open browser and go to http://localhost/phpmyadmin
   - Click **New**
   - Database name: `incident_db`
   - Click **Create**
   - Click the **SQL** tab
   - Paste and run the following SQL statements **in order**:

   ```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       full_name VARCHAR(100) NOT NULL,
       username VARCHAR(50) UNIQUE NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role VARCHAR(20) DEFAULT 'user',
       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
       reset_token VARCHAR(64) DEFAULT NULL,
       reset_expires DATETIME DEFAULT NULL,
       remember_token VARCHAR(64) DEFAULT NULL,
       failed_attempts INT DEFAULT 0,
       locked_until DATETIME DEFAULT NULL
   );
   ```

   ```sql
   CREATE TABLE incidents (
       id INT AUTO_INCREMENT PRIMARY KEY,
       incident_id VARCHAR(20) UNIQUE NOT NULL,
       incident_title VARCHAR(200) NOT NULL,
       incident_type VARCHAR(100) NOT NULL,
       severity VARCHAR(20) DEFAULT 'Medium',
       description TEXT NOT NULL,
       reporter_name VARCHAR(100) NOT NULL,
       date_reported DATETIME DEFAULT CURRENT_TIMESTAMP,
       status VARCHAR(50) DEFAULT 'Open'
   );
   ```

5. **Run the Application**
   - Open browser and go to:
   ```
   http://localhost/OpenSource_Assignment_CSDFE_Group06/landing.php
   ```
   - Register a new account or log in with existing credentials.

## Project Files

| File | Description |
|------|-------------|
| `db_config.php` | Database connection configuration (MySQLi, port 3307) |
| `landing.php` | Hero landing page introducing the system |
| `login.php` | User login with "Remember Me" and brute-force protection |
| `register.php` | User registration with password hashing and validation |
| `forgot_password.php` | Password reset request with secure token generation |
| `reset_password.php` | Token-based password reset with expiration check |
| `logout.php` | Session termination and persistent token cleanup |
| `index.php` | Dashboard with incident statistics and quick actions menu |
| `navbar.php` | Reusable fixed navigation bar with dark mode toggle |
| `footer.php` | Reusable footer included on all authenticated pages |
| `register_incident.php` | Incident registration form with auto-generated ID (INC-XXXXXXXX) |
| `display_incidents.php` | Paginated table of all incidents, sorted by date |
| `view_incident.php` | Detailed single-incident view page |
| `search_incident.php` | Search incidents by ID, title, or reporter name |
| `filter_status.php` | Filter incidents by status (Open, Investigating, Resolved, Closed) |
| `style.css` | Complete stylesheet with CSS variables, light/dark themes, responsive design |
| `script.js` | Client-side JavaScript: dark mode toggle, page transitions, toast notifications |
| `README.md` | This documentation file |

## Features

- **Landing Page:** Hero section with brand identity, feature grid, and responsive navigation
- **User Authentication:** Registration, login, logout, and password recovery
- **Remember Me:** Persistent login via secure cookie token
- **Brute-force Protection:** Account locking after excessive failed login attempts
- **Incident Management:** Register, view, search, and filter security incidents
- **Unique Incident IDs:** Auto-generated references in INC-XXXXXXXX format
- **Dark Mode:** One-click theme toggle with LocalStorage persistence
- **Page Transitions:** Smooth slide animations between pages
- **Toast Notifications:** Non-intrusive feedback for user actions
- **Responsive Design:** Adapts to desktop, tablet, and mobile screens
- **Sora Typography:** Premium, modern typeface for optimal readability

## Technologies Used

| Technology | Purpose |
|------------|---------|
| PHP | Server-side scripting language for backend logic |
| MySQL | Database management system for data storage |
| HTML | Structure of web pages |
| CSS | Styling and layout with CSS custom properties |
| JavaScript | Client-side interactivity and UI enhancements |
| Git | Version control system for tracking changes |
| GitHub | Cloud hosting for Git repositories |
| XAMPP | Local web server environment |

## Git Commands Used

| Command | Purpose |
|---------|---------|
| `git init` | Initialize a new Git repository |
| `git add .` | Stage all files for commit |
| `git add <filename>` | Stage a specific file for commit |
| `git commit -m "message"` | Commit staged changes with a message |
| `git branch -M main` | Rename the default branch to main |
| `git remote add origin <URL>` | Connect local repo to GitHub remote |
| `git push -u origin main` | Push local commits to GitHub (first time) |
| `git push` | Push local commits to GitHub (subsequent) |
| `git branch <name>` | Create a new branch |
| `git checkout <branch>` | Switch to a branch |
| `git checkout -b <branch>` | Create and switch to a new branch |
| `git merge <branch>` | Merge a branch into the current branch |
| `git log --oneline` | View commit history in compact form |
| `git log --oneline --graph --all` | View commit graph with all branches |
| `git branch -a` | List all branches |

## GitHub Repository Link
[https://github.com/eliya-web/OpenSource_Assignment_CSDFE_Group06](https://github.com/eliya-web/OpenSource_Assignment_CSDFE_Group06)
