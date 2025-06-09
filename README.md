# Digital Attendance System for Diploma Colleges

A comprehensive web-based attendance management system designed specifically for diploma colleges, built with PHP, MySQL, HTML, CSS, Bootstrap 5, and JavaScript.

## 🎯 Problem Statement

Many diploma colleges still rely on traditional pen-paper attendance systems, which leads to:
- Data loss and manual errors
- Difficulty in calculating attendance percentages
- No real-time access for students and teachers
- Time-consuming administrative processes

## ✅ Solution

Our Digital Attendance System provides a modern, web-based solution that enables:
- Faculty to securely log in and mark attendance
- Students to view their attendance percentages in real-time
- Automatic notifications for students with attendance below 75%
- Comprehensive reporting and analytics

## 🚀 Key Features

### 👨‍🏫 Faculty Module
- **Secure Login System**: Email/password authentication
- **Subject Management**: View assigned subjects and student lists
- **Attendance Marking**: Easy-to-use interface for marking present/absent
- **Real-time Statistics**: View attendance overview for all subjects
- **Date-wise Records**: Access historical attendance data

### 👨‍🎓 Student Module
- **Student Portal**: Login with student credentials
- **Attendance Dashboard**: View attendance percentage per subject
- **Visual Analytics**: Progress bars and charts for better understanding
- **Low Attendance Alerts**: Automatic warnings when attendance drops below 75%
- **Recent History**: Track recent attendance records

### 🎨 Design Features
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Modern UI**: Clean, professional interface with Bootstrap 5
- **Interactive Elements**: Smooth animations and hover effects
- **Role-based Navigation**: Different interfaces for faculty and students
- **Real-time Updates**: Dynamic content loading without page refresh

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL 5.7+ |
| **Icons** | Font Awesome 6 |

## 📊 Database Schema

### Tables Structure

1. **students**
   - `id` (Primary Key)
   - `name`, `roll_no`, `email`, `password`
   - `department`, `created_at`

2. **faculty**
   - `id` (Primary Key)
   - `name`, `email`, `password`
   - `department`, `created_at`

3. **subjects**
   - `id` (Primary Key)
   - `subject_name`, `subject_code`
   - `faculty_id` (Foreign Key)
   - `department`, `created_at`

4. **attendance**
   - `id` (Primary Key)
   - `student_id`, `subject_id` (Foreign Keys)
   - `date`, `status` (P/A)
   - `marked_by`, `created_at`

## 🚀 Installation & Setup

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Installation Steps

1. **Clone/Download the project files**
   ```bash
   # Place all files in your web server directory
   # e.g., /var/www/html/ or htdocs/
   ```

2. **Database Setup**
   ```sql
   # Import the database schema
   mysql -u username -p < database.sql
   ```

3. **Configure Database Connection**
   ```php
   // Edit config.php with your database credentials
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "attendance_system";
   ```

4. **Access the Application**
   ```
   http://localhost/your-project-folder/
   ```

## 👥 Demo Credentials

### Faculty Login
- **Email**: sarah.johnson@college.edu
- **Password**: password

### Student Login
- **Email**: john.smith@student.edu
- **Password**: password

## 📱 File Structure

```
Digital-Attendance-System/
├── index.php              # Homepage
├── config.php             # Database configuration
├── database.sql           # Database schema
├── login_faculty.php      # Faculty login page
├── login_student.php      # Student login page
├── faculty_dashboard.php  # Faculty main dashboard
├── student_dashboard.php  # Student main dashboard
├── attendance_submit.php  # Attendance processing
├── logout.php            # Session management
└── README.md             # Project documentation
```

## 🧮 Attendance Calculation

The system automatically calculates attendance percentages using:

```php
$present = COUNT(*) FROM attendance WHERE student_id=X AND subject_id=Y AND status='P';
$total = COUNT(*) FROM attendance WHERE student_id=X AND subject_id=Y;
$percentage = ($present / $total) * 100;
```

## 🔔 Alert System

Automatic notifications when attendance falls below 75%:
- Visual warnings on student dashboard
- Color-coded progress indicators
- Summary statistics highlighting low attendance subjects

## 🎨 UI/UX Features

- **Gradient Backgrounds**: Modern visual appeal
- **Card-based Layout**: Clean, organized content
- **Interactive Buttons**: Hover effects and smooth transitions
- **Responsive Grid**: Optimal viewing on all devices
- **Progress Indicators**: Visual representation of attendance data
- **Real-time Feedback**: Instant confirmation of actions

## 🔒 Security Features

- **Session Management**: Secure login/logout functionality
- **Role-based Access**: Different permissions for faculty and students
- **SQL Injection Prevention**: Prepared statements for database queries
- **Input Validation**: Server-side validation for all forms
- **Password Security**: Hashed password storage (ready for implementation)

## 📈 Future Enhancements

- Email notifications for low attendance
- SMS integration for alerts
- Advanced reporting with export features
- Mobile app development
- Integration with existing college management systems
- Biometric attendance integration
- Parent portal for attendance monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is created for educational purposes and is free to use and modify.

## 📞 Support

For technical support or questions:
- Check the documentation
- Review the demo credentials
- Test with sample data provided

---

**Developed for Diploma Colleges - Bridging the gap between traditional and digital attendance management**