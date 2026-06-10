# SMA Ananda Batam LMS - Documentation

## Overview

The SMA Ananda Batam Learning Management System (LMS) is a web-based educational platform built with Laravel that facilitates course management, student enrollment, assignment submission, and grade tracking for high school students and teachers.

## User Roles

The system has three main user roles:

### 1. Super Admin
- Full system administration access
- Manage student accounts (create, import via CSV)
- Manage teacher accounts (create, view)
- View system-wide statistics
- Access to all administrative features

### 2. Teacher
- Create and manage courses
- Add study materials (modules) with attachments
- Create and manage assignments
- View and grade student submissions
- Post course announcements
- Track student progress
- Access gradebook for their courses

### 3. Student
- Browse and enroll in published courses
- View course materials and modules
- Mark modules as complete/incomplete
- Submit assignments with file attachments
- View grades and feedback
- Track course progress
- View announcements and notifications

## Key Features

### Course Management
- Teachers can create courses with title, description, and cover image
- Courses can be published or kept as drafts
- Students can browse and enroll in published courses
- Course progress tracking based on completed modules

### Module Management
- Teachers can add study materials to courses
- Modules support file attachments (PDF, Office docs, ZIP, images)
- Modules can include text content
- Students can mark modules as complete
- Progress tracking per module

### Assignment Management
- Teachers create assignments with instructions and due dates
- Students submit assignments with file attachments
- Teachers view and grade submissions
- Gradebook shows all student grades per course
- Submission history tracking

### Announcements
- Teachers can post course-wide announcements
- Announcements can be pinned for visibility
- Students can reply to announcements
- Global announcements for all students

### Gradebook
- Teachers view all student grades for their courses
- Students view their grades across enrolled courses
- Grade tracking with submission history
- Pending submissions indicator for teachers

### Calendar
- Personal calendar for events
- Assignment due dates automatically shown
- Teachers see their course assignment deadlines
- Students see their enrolled course assignment deadlines

### Notifications
- Real-time notifications for students
- Unread notification count
- Recent updates dashboard

## Technical Architecture

### Technology Stack
- **Backend**: Laravel 11 (PHP)
- **Frontend**: Blade Templates with TailwindCSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze with Spatie Permissions
- **File Storage**: Local storage (configurable)

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Student/        # Student controllers
│   │   └── Teacher/        # Teacher controllers
│   └── Requests/           # Form validation requests
├── Models/                 # Eloquent models
├── Services/               # Business logic services
└── Support/                # Helper classes

resources/
├── views/
│   ├── admin/              # Admin views
│   ├── student/            # Student views
│   ├── teacher/            # Teacher views
│   ├── layouts/            # Layout templates
│   └── partials/           # Reusable components
└── lang/                   # Language files

routes/
└── web.php                 # Web routes
```

### Key Models

- **User**: User accounts with role-based permissions
- **Course**: Course information and settings
- **Module**: Study materials within courses
- **Assignment**: Assignments with due dates
- **Submission**: Student assignment submissions
- **Enrollment**: Student course enrollments
- **ModuleProgress**: Student module completion tracking
- **Announcement**: Course announcements
- **AnnouncementReply**: Student replies to announcements
- **Event**: Calendar events

### Authentication & Authorization

- Uses Laravel Breeze for authentication
- Spatie Permissions for role-based access control
- Roles: super-admin, teacher, student
- Policies for resource authorization

## Database Schema

### Key Tables

- **users**: User accounts with roles
- **courses**: Course information
- **modules**: Course study materials
- **assignments**: Course assignments
- **submissions**: Student assignment submissions
- **enrollments**: Student course enrollments
- **module_progress**: Module completion tracking
- **announcements**: Course announcements
- **events**: Calendar events

### Important Indexes

- `courses`: teacher_id, is_published
- `modules`: course_id, order_position
- `assignments`: course_id, order_position, due_at
- `enrollments`: student_id, course_id, status
- `submissions`: assignment_id, user_id
- `announcements`: course_id, is_pinned, created_at
- `module_progress`: user_id, module_id, is_completed

## User Workflows

### Student Workflow

1. **Login**: Use Student ID and password (default: date of birth as DDMMYYYY)
2. **Dashboard**: View enrolled courses, progress, upcoming assignments
3. **Browse Courses**: View published course catalog
4. **Enroll**: Click to enroll in available courses
5. **View Course**: Access course materials and assignments
6. **Complete Modules**: Mark modules as complete to track progress
7. **Submit Assignments**: Upload assignment files before due dates
8. **View Grades**: Check gradebook for feedback and scores

### Teacher Workflow

1. **Login**: Use email and password
2. **Dashboard**: View course statistics and pending submissions
3. **Create Course**: Add new course with details
4. **Add Materials**: Upload study materials and modules
5. **Create Assignments**: Set assignments with instructions and due dates
6. **Grade Submissions**: Review and grade student submissions
7. **Post Announcements**: Share updates with students
8. **View Gradebook**: Track student performance

### Admin Workflow

1. **Login**: Use admin credentials
2. **Dashboard**: View system-wide statistics
3. **Manage Students**: Create student accounts or import via CSV
4. **Manage Teachers**: Create teacher accounts
5. **Monitor**: Track overall system usage

## Performance Optimizations

The application includes several performance optimizations:

### Database Query Optimizations
- Eager loading of relationships to prevent N+1 queries
- Optimized DashboardService queries using joins instead of nested whereHas
- Database indexes on frequently queried columns
- Query counting for dashboard statistics

### Caching Opportunities
- Consider caching course lists for students
- Cache dashboard statistics for better performance
- Implement query caching for frequently accessed data

## Security Features

- Role-based access control
- CSRF protection on all forms
- Input validation and sanitization
- File upload restrictions (type and size limits)
- Authorization policies for resource access
- Secure password hashing

## Configuration

### Environment Variables

Key environment variables:
- `APP_NAME`: Application name
- `APP_ENV`: Environment (local/production)
- `APP_URL`: Application URL
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `MAIL_*`: Mail configuration for notifications

### File Storage

File attachments are stored in the `storage/app/public` directory and served via the `storage` symlink. Maximum file size is 20MB.

## Development

### Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `php artisan storage:link`
8. Run `npm run dev` for development
9. Run `php artisan serve` to start the server

### Testing

Run tests with:
```bash
php artisan test
```

## Support

For issues or questions, contact the development team.
