# Security Incident Reporting System

**Degree Program:** Bachelor of Science in Cyber Security and Digital Forensics Engineering
**Group Number:** Group 6
**Project Description:** A web-based security incident reporting system that allows users to record, display, and search security incidents. Developed as part of CP 222 - Open Source Technologies assignment.

## Project Overview
This system enables organizations to report and track security incidents. Users can register new incidents, view all recorded incidents, search for specific incidents by their unique ID, and filter incidents by status. The system includes a complete user management module with registration, login, and session-based authentication. It is built using PHP for server-side logic and MySQL for data storage.

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
   - Paste and run the following SQL (in order):
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         full_name VARCHAR(100) NOT NULL,
         username VARCHAR(50) UNIQUE NOT NULL,
         email VARCHAR(100) UNIQUE NOT NULL,
         password VARCHAR(255) NOT NULL,
         role VARCHAR(20) DEFAULT 'user',
         created_at DATETIME DEFAULT CURRENT_TIMESTAMP
     );
     ```
     Then run:
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
   http://localhost/OpenSource_Assignment_CSDFE_Group06/index.php
   ```

## Technologies Used
| Technology | Purpose |
|------------|---------|
| PHP | Server-side scripting language for backend logic |
| MySQL | Database management system for data storage |
| HTML | Structure of web pages |
| CSS | Styling and layout of web pages |
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
