# Wazfnee Website

This is the website frontend for the Wazfnee job portal application. The website connects to the existing API endpoints to provide a web interface for users and administrators.

## Project Structure

```
wazfnee-website/
├── index.php                 # Homepage
├── jobs.php                  # Jobs listing page
├── job-details.php           # Single job page
├── seekers.php               # Job seekers listing
├── seeker-profile.php        # Single seeker profile
├── blog.php                  # Blog listing
├── article.php               # Single article page
├── contact.php               # Contact page
├── login.php                 # User login
├── register.php              # User registration
├── profile.php               # User profile
├── post-job.php              # Post job form
├── includes/
│   ├── config.php            # Database and API configuration
│   ├── functions.php         # Helper functions
│   ├── translations.php      # Language translations
│   ├── header.php            # Site header
│   └── footer.php            # Site footer
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── login.php             # Admin login
│   ├── jobs.php              # Manage jobs
│   ├── seekers.php           # Manage seekers
│   ├── users.php             # Manage users
│   ├── articles.php          # Manage articles
│   └── includes/
│       ├── sidebar.php       # Admin sidebar
│       └── header.php        # Admin header
├── assets/
│   ├── css/
│   │   ├── style.css         # Main website styles
│   │   └── admin.css         # Admin panel styles
│   ├── js/
│   │   ├── main.js           # Main website JavaScript
│   │   └── admin.js          # Admin panel JavaScript
│   └── images/               # Website images
└── uploads/                  # File uploads directory
```

## Installation

1. Upload all files to your web server (outside the API folder)
2. Update the API_BASE_URL in `includes/config.php` to point to your API folder
3. Ensure the uploads directory is writable
4. Access the website through your domain

## Configuration

Edit `includes/config.php` to configure:
- Database connection (if needed for direct queries)
- API base URL (pointing to your existing API)
- Site settings
- Language preferences

## Features

### Frontend Features
- Responsive design with Bootstrap 5
- Multi-language support (Arabic/English)
- Job search and filtering
- Job seeker profiles
- Blog system
- User authentication
- Contact forms
- SEO optimized

### Admin Panel Features
- Dashboard with statistics
- Job management (approve/reject)
- User management
- Content management
- Blog management
- System settings

## API Integration

The website connects to your existing API endpoints:
- `get_jobs.php` - Fetch jobs
- `get_seekers.php` - Fetch job seekers
- `get_articles.php` - Fetch blog articles
- `insert_job.php` - Create new jobs
- `insert_seeker.php` - Create seeker profiles
- And all other existing endpoints

## Requirements

- PHP 7.4 or higher
- MySQL database (same as API)
- Web server (Apache/Nginx)
- cURL extension enabled

## Security

- CSRF protection
- Input sanitization
- Session security
- Admin access control
- File upload validation