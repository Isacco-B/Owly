# Owly Courses API

- Owly Courses API is a RESTful service designed to support the management of cross-functional courses. These courses combine multiple subjects to foster curiosity and enhance learning experiences.


## Features
- **Subjects Management**:
  - Create, update, delete, and filter available subjects.
- **Courses Management**:
  - Create, update, delete, and filter available courses.
  - Filter courses by: Name, Subjects ID, Available seats.
- **Database**:
  - MySQL used for persistent storage.
  - Includes a *migrations.sql* file to recreate the database structure.
- **Security**:
  - Sanitized database queries using PDO to prevent SQL Injection attacks.
- **Routing**:
  - Integrated the AltoRouter library to simplify routing.


## Tech Stack Client

- **Backend:** PHP
- **Database:** MySQL
- **Architecture:** RESTful JSON API
- **Security:** PDO for secure database interactions


### Prerequisites
- **PHP**: This project requires PHP version 8.1 or higher
- **MySQL**: This project requires MySQL version 8.0 or higher.


### API Endpoints

## Subjects
| Method | Endpoint | Description | Parameters |
| --- | --- | --- | --- |
| GET | /api/subjects | Returns a list of all available subjects. |
| GET | /api/subjects/{id} | Returns the details of a specific subject. |
| GET | /api/subjects?name={name} | Returns the details of a specific subject by name. |
| POST | /api/subjects | Creates a new subject. | name: string |
| PUT | /api/subjects/{id} | Updates an existing subject. | name: string |
| DELETE | /api/subjects/{id} | Deletes a subject. |

## Courses
| Method | Endpoint | Description | Parameters |
| --- | --- | --- | --- |
| GET | /api/courses | Returns a list of all available courses. |
| GET | /api/courses/{id} | Returns the details of a specific course. |
| GET | /api/courses?name={name} | Returns the details of a specific course by name. |
| GET | /api/courses?available_seats={available_seats} | Returns the details of a specific course by available seats. |
| GET | /api/courses?subject_ids={subject_ids,subject_ids} | Returns the details of a specific course by subject IDs. |
| GET | /api/courses?subject_ids={subject_ids,subject_ids}&available_seats={available_seats} | Returns the details of a specific course by subject IDs and available seats. |
| POST | /api/courses | Creates a new course. | name: string, subjects_id: array(subject_id), available_seats: int |
| PUT | /api/courses/{id} | Updates an existing course. | name: string, subjects_id: array(subject_id), available_seats: int |
| DELETE | /api/courses/{id} | Deletes a course. |

## Getting Up and Running Locally

- Clone this repository to your local machine:

```bash
git clone https://github.com/Isacco-B/Owly.git
```

- Import the migrations.sql file into your MySQL database:

- Move to the cloned directory

```bash
cd Owly
```

- Rename the ./config/'config.example.php' file to 'config.php' and update the database credentials.

- Start the development server:

```bash
php -S localhost:8000 ./public/index.php
```

## 🔗 Links

[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/isacco-bertoli-10aa16252/)
