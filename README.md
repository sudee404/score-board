# Score Board - LAMP Stack Implementation

A comprehensive Dockerized LAMP stack application for managing judges and scoring participants in an event. This Score Board system demonstrates the integration of Linux, Apache, MySQL, and PHP in a containerized environment with a modern, responsive UI and real-time score tracking.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Project Structure](#project-structure)
- [Installation and Setup](#installation-and-setup)
- [Running the Application](#running-the-application)
- [Usage Guide](#usage-guide)
- [Database Schema](#database-schema)
- [Technical Implementation](#technical-implementation)
- [Security Considerations](#security-considerations)
- [Future Enhancements](#future-enhancements)
- [Troubleshooting](#troubleshooting)

## Features

- **Admin Panel**: User-friendly interface for managing judges and users
- **Judge Portal**: Modern interface for judges to assign scores to participants
- **Public Scoreboard**: Real-time display of participant rankings with auto-refresh
- **Responsive Design**: Works on desktop and mobile devices
- **Data Visualization**: Color-coded scores and rankings with visual feedback
- **Interactive UI**: Modern modals, toast notifications, and animations
- **Icon-Based Actions**: Intuitive icon buttons for common actions
- **Consistent Styling**: Cohesive color theme and design language
- **Dockerized Environment**: Easy setup and deployment

## Prerequisites

- Docker Engine (version 20.10.0 or higher)
- Docker Compose (version 2.0.0 or higher)
- Web Browser (Chrome, Firefox, Safari, or Edge)
- Git (for cloning the repository)

## Project Structure

```text
/
├── docker/                  # Docker configuration files
│   ├── db/                  # Database configuration
│   │   └── init/            # Database initialization scripts
│   │       ├── 01-schema.sql    # Database schema
│   │       └── 02-demo_data.sql # Sample data
│   └── web/                 # Web server configuration
│       └── Dockerfile       # PHP Apache configuration
├── src/                     # Source code
│   ├── admin/               # Admin panel interface
│   │   ├── index.php        # Admin dashboard for judges
│   │   └── users.php        # User management interface
│   ├── config/              # Configuration files
│   │   └── database.php     # Database connection settings
│   ├── core/                # Core functionality
│   │   └── Database.php     # Database connection class
│   ├── database/            # Database scripts
│   │   ├── schema.sql       # Database schema
│   │   └── demo_data.sql    # Sample data
│   ├── includes/            # Shared includes
│   │   └── init.php         # Application initialization
│   ├── judge/               # Judge portal interface
│   │   ├── index.php        # Judge scoring interface
│   │   └── manage-users.php # User management for judges
│   ├── public/              # Public-facing pages
│   │   ├── css/             # CSS stylesheets
│   │   │   └── styles.css   # Main stylesheet
│   │   ├── js/              # JavaScript files
│   │   │   └── toast.js     # Toast notification system
│   │   ├── fetch_scores.php # AJAX endpoint for scores
│   │   └── index.php        # Scoreboard display
│   └── index.php            # Main entry point
├── .env                     # Environment variables (not in version control)
├── .env.example             # Example environment file template
├── .gitignore               # Git ignore configuration
├── docker-compose.yml       # Docker services configuration
└── README.md                # Project documentation
```

## Installation and Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd score-board
```

### 2. Configure Environment Variables

Copy the example environment file and modify it if needed:

```bash
cp .env.example .env
```

Edit the `.env` file to set your preferred configuration:

```env
# Database configuration
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=judge_db
MYSQL_USER=your_db_user
MYSQL_PASSWORD=your_db_password
MYSQL_HOST=db
```

> **Important**: The `.env` file contains sensitive information and is excluded from version control via `.gitignore`. Never commit your actual `.env` file to the repository.

### 3. Start the Application with Demo Data

Simply start the Docker containers with the following command:

```bash
docker compose up -d
```

This command will:

- Build and start the Docker containers using your environment variables
- Automatically initialize the database schema
- Load demo data with sample judges, users, and scores

The first run may take a few minutes as it downloads and builds the necessary images.

### Accessing the Application

Once the containers are running, you can access the application at:

- **Main Page**: [http://localhost:8080/](http://localhost:8080/)
- **Admin Panel**: [http://localhost:8080/admin/](http://localhost:8080/admin/)
- **Judge Portal**: [http://localhost:8080/judge/](http://localhost:8080/judge/)
- **Public Scoreboard**: [http://localhost:8080/public/](http://localhost:8080/public/)

## Running the Application

Once the containers are running and the database is initialized, you can access the application at:

- **Main Page**: [http://localhost:8080/](http://localhost:8080/)
- **Admin Panel**: [http://localhost:8080/admin/](http://localhost:8080/admin/)
- **Judge Portal**: [http://localhost:8080/judge/](http://localhost:8080/judge/) (add `?id=X` where X is the judge ID)
- **Public Scoreboard**: [http://localhost:8080/public/](http://localhost:8080/public/)

### Stopping the Application

To stop the application and preserve the data:

```bash
docker compose stop
```

To stop the application and remove the containers (data will be lost):

```bash
docker compose down
```

To completely remove everything including volumes (all data will be permanently deleted):

```bash
docker compose down -v
```

## Usage Guide

### Admin Panel

1. Navigate to [http://localhost:8080/admin/](http://localhost:8080/admin/)
2. **Managing Judges**:
   - Use the "Add Judge" button to create new judges with username and display name
   - View the list of registered judges with their statistics
   - Use the icon buttons to edit or delete judges
3. **Managing Users**:
   - Click on "Manage Users" or navigate to [http://localhost:8080/admin/users.php](http://localhost:8080/admin/users.php)
   - Add, edit, or delete users (participants) in the system
   - View user statistics including total points and judge count

### Judge Portal

1. Navigate to [http://localhost:8080/judge/](http://localhost:8080/judge/) and select a judge
2. **Scoring Participants**:
   - View all participants in the table with their current scores
   - Use the "Assign Score" button for new scores or "Edit Score" for existing scores
   - Adjust score using the interactive slider or direct input (0-100 points)
   - Submit the form to record the score
3. **Managing Users**:
   - Click on "Manage Users" to add, edit, or delete users specific to this judge
   - Each judge can only vote once per user
4. **Score History**:
   - View your scoring history at the bottom of the page
   - Toast notifications provide feedback on all actions

### Public Scoreboard

1. Navigate to [http://localhost:8080/public/](http://localhost:8080/public/)
2. View the real-time rankings of participants with color-coded scores:
   - 90+ points: Green (success)
   - 70-89 points: Blue (primary)
   - 50-69 points: Light blue (info)
   - 30-49 points: Yellow (warning)
   - 1-29 points: Red (danger)
   - 0 points: Gray (secondary)
3. Judge count is also color-coded based on the number of judges
4. The scoreboard automatically refreshes every 30 seconds
5. Top three participants are highlighted with gold, silver, and bronze medals
6. Score changes are animated with visual feedback

## Database Schema

The application uses three main tables:

### 1. `judges`

```sql
CREATE TABLE judges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. `users`

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. `scores`

```sql
CREATE TABLE scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points BETWEEN 0 AND 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Technical Implementation

### LAMP Stack Components

- **Linux**: Docker containers based on Debian
- **Apache**: Web server (2.4) with mod_rewrite enabled
- **MySQL**: Database server (8.0) for data storage
- **PHP**: Version 8.1 with PDO, mysqli, and other extensions

### Design Patterns

- **Singleton Pattern**: Used for database connection to ensure only one connection is maintained
- **MVC-like Structure**: Separation of data access, business logic, and presentation
- **Repository Pattern**: Abstraction for database operations

### Frontend Technologies

- **Bootstrap 5**: For responsive and modern UI components
- **Bootstrap Icons**: For consistent and accessible iconography
- **JavaScript**: For interactive features, AJAX updates, and animations
- **CSS3**: Custom styling with variables, animations, and visual effects
- **Toast Notifications**: Custom implementation for user feedback
- **Interactive UI Elements**: Modals, sliders, and color-coded indicators

## Security Considerations

- **SQL Injection Prevention**: All database queries use prepared statements with parameterized queries
- **XSS Prevention**: Output escaping using `htmlspecialchars()` for all user-generated content
- **Input Validation**: Server-side validation for all form submissions
- **Error Handling**: Proper error handling to prevent information disclosure
- **Database Security**: Limited database user permissions and secure credentials

## Future Enhancements

1. **Authentication System**
   - Secure login for judges and admins
   - Role-based access control
   - Session management and CSRF protection

2. **Advanced Scoring Features**
   - Score history tracking and editing
   - Category-based scoring
   - Statistical analysis of scores

3. **User Management**
   - User registration and profile management
   - User categorization and grouping
   - Participant registration system

4. **Reporting and Analytics**
   - Export functionality (CSV, PDF)
   - Score analytics and visualizations
   - Judge performance metrics

5. **Real-time Updates**
   - WebSockets for instant scoreboard updates
   - Push notifications for new scores
   - Live event management

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Ensure the MySQL container is running: `docker ps`
   - Check database credentials in your `.env` file
   - Verify the database has been initialized with the schema
   - Make sure your `.env` file exists and contains all required variables

2. **Web Server Issues**
   - Check Apache logs: `docker logs score-board-web`
   - Ensure ports are not in use by other applications
   - Verify the volume mounting in docker-compose.yml

3. **PHP Errors**
   - Check PHP error logs in the web container
   - Verify PHP extensions are properly installed
   - Check file permissions for PHP files

### Resetting the Application

To completely reset the application and start fresh:

```bash
# Stop and remove containers and volumes
docker compose down -v

# Start containers again
docker compose up -d

# The database will be automatically initialized with the schema and demo data
# If you need to manually initialize, you can use:
docker exec -i score-board-db mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE} < src/database/schema.sql
```
