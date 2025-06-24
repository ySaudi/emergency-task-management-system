
Built by https://www.blackbox.ai

---

# Emergency Task Management System

## Project Overview
This project is an Emergency Task Management System developed to streamline the registration and task assignment process for volunteer paramedics in Saudi Arabia. The system allows users to register their information, view available tasks, and accept tasks that need to be completed.

The application helps manage emergency tasks efficiently while maintaining a record of volunteer details and task assignments.

## Installation

To set up the project locally, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone <repository_url>
   cd <repository_name>
   ```

2. **Set Up a Web Server:**
   This project requires a web server with PHP support (e.g., XAMPP, WAMP, or a live server).

3. **Database Configuration:**
   - Create a new database and import the necessary SQL schema to create the required tables (not included in this repo).
   - Update `config/config.php` with your database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'your_databasename');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

4. **Required Dependencies:**
   You will need to install any required PHP extensions and libraries that your environment may lack.

5. **Start Your Web Server:**
   Visit `http://localhost/<your_folder>/index.php` in your browser.

## Usage

1. **User Registration:**
   Navigate to the registration page to enroll as a volunteer. Fill out the required forms with valid details.

2. **Task Management:**
   After registration, users can log in to view pending tasks, accept tasks, or check the status of their accepted tasks.

3. **Visual Interface:**
   The interface is user-friendly, built with modern web technologies. Follow prompts and instructions presented on the page.

## Features

- **Quick Registration:** User can easily register as a paramedic.
- **Task Management System:** Volunteers can view and accept available tasks.
- **Real-time Task Updates:** Users can track the status of tasks they have accepted.
- **Error Handling:** Ensures all input data is validated and errors are handled gracefully.
- **Responsive Design:** Works on various device sizes, providing accessibility to a broader audience.

## Dependencies

The project requires the following dependencies to function correctly:
- PHP (version 7.4 or higher recommended)
- PDO extension for database interaction
- A web server capable of running PHP (e.g., Apache, Nginx)

**No additional dependencies were specified in a `package.json` file since this is primarily a PHP application.**

## Project Structure

Here’s an overview of the project structure:

```
/project-root
├── config/
│   └── config.php         # Database configuration
├── includes/
│   ├── db.php             # Database connection logic
│   ├── footer.php         # Footer template
│   └── header.php         # Header template
├── register.php           # User registration page
├── tasks.php              # Task management page
├── accept_task.php        # Logic to accept a task
└── index.php              # Main landing page
```

Ensure you properly configure the database and other server settings as per your local environment to run the application successfully.

## Conclusion

The Emergency Task Management System aims to facilitate the organization of volunteer paramedics during emergencies efficiently. Your contributions and feedback would help enhance its functionality and usability. Feel free to reach out for any inquiries or suggestions.