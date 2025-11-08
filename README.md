# Lifestyle Disease Risk Assessment Portal

A comprehensive web application that uses machine learning to assess lifestyle disease risks and provide personalized health recommendations.

## Features

- **Machine Learning Integration**: Uses Random Forest model for risk prediction
- **User Management**: Registration, login, and user sessions
- **Health Assessment**: Comprehensive health data collection and analysis
- **Risk Analysis**: ML-powered risk scoring and disease prediction
- **Visual Reports**: Interactive charts and PDF export functionality
- **Dashboard**: Personal health dashboard with history tracking
- **Responsive Design**: Mobile-friendly Bootstrap interface

## System Requirements

- **Web Server**: Apache/Nginx with PHP 7.4+
- **Database**: MySQL 5.7+
- **Python**: Python 3.8+ with scikit-learn
- **PHP Extensions**: mysqli, session

## Installation Steps

### 1. Database Setup

Run the database setup script:

```bash
php setup_database.php
```

Or manually create the database:

1. Open phpMyAdmin or MySQL command line
2. Create database: `lifestyle_portal`
3. Import the `database.sql` file

### 2. Python ML Setup

1. Navigate to the `ml/` directory:
   ```bash
   cd ml
   ```

2. Install Python dependencies:
   ```bash
   pip install -r requirements.txt
   ```

3. Train the ML model:
   ```bash
   python train_model.py
   ```

### 3. Web Server Configuration

1. Place the project in your web server's root directory
2. Ensure PHP can execute Python scripts (adjust permissions if needed)
3. Make sure the `ml/model/` directory is writable

### 4. Access the Application

Open your browser and go to:
```
http://localhost/lifestyle-risk-portal-full/
```

## Usage

### For New Users:
1. Click "Register" to create an account
2. Fill in your details and create a password
3. Login with your credentials

### For Existing Users:
1. Click "Login" and enter your credentials
2. Navigate to "New Assessment" to start
3. Fill in your health information
4. Submit to get your risk assessment

### Understanding Results:
- **Risk Score**: ML-predicted risk level (0-100)
- **Risk Level**: Low, Moderate, or High
- **Diseases**: Potential health conditions based on your data
- **Recommendations**: Personalized health advice

## File Structure

```
lifestyle-risk-portal-full/
├── index.html              # Landing page
├── index.php               # PHP entry point
├── database.sql            # Database schema
├── setup_database.php      # Database setup script
├── config/
│   └── db.php             # Database configuration
├── includes/
│   ├── auth.php           # Authentication functions
│   ├── header.php         # Page header template
│   └── footer.php         # Page footer template
├── modules/
│   ├── login.php          # User login
│   ├── register.php       # User registration
│   ├── dashboard.php      # User dashboard
│   ├── health_form.php    # Health assessment form
│   ├── save_health.php    # Process assessment data
│   ├── risk_result.php    # Display results
│   ├── history.php        # Assessment history
│   ├── charts.php         # Visual charts
│   ├── pdf_export.php     # PDF report generation
│   ├── logout.php         # User logout
│   └── calc_risk.php      # Risk calculation functions
├── ml/
│   ├── train_model.py     # ML model training
│   ├── predict_risk.py    # Risk prediction script
│   ├── data/
│   │   └── health_data.csv # Training data
│   ├── model/
│   │   ├── disease_risk_model.pkl # Trained model
│   │   └── scaler.pkl     # Data scaler
│   └── requirements.txt   # Python dependencies
├── assets/
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── app.js         # JavaScript functions
└── dompdf/                # PDF generation library
```

## Machine Learning Details

### Model Training
- **Algorithm**: Random Forest Classifier
- **Features**: BMI, Blood Pressure, Smoking, Alcohol, Activity Level
- **Target**: Disease risk prediction (0=Low, 1=High)
- **Data**: Sample health dataset with lifestyle factors

### Prediction Process
1. User submits health assessment form
2. PHP calculates BMI and prepares features
3. PHP calls Python prediction script
4. Python loads trained model and returns prediction
5. PHP maps prediction to risk level and recommendations

### Model Retraining
To improve model accuracy:
1. Add more sample data to `ml/data/health_data.csv`
2. Run `python train_model.py` to retrain
3. The new model will be used automatically

## Troubleshooting

### Common Issues:

**Database Connection Error:**
- Ensure MySQL is running
- Check database credentials in `config/db.php`
- Run `php setup_database.php` to create database

**ML Prediction Not Working:**
- Ensure Python and scikit-learn are installed
- Check file permissions on `ml/` directory
- Verify model files exist in `ml/model/`

**File Paths Incorrect:**
- All paths have been configured for the current directory structure
- If moving the project, update paths in PHP files accordingly

**PDF Export Not Working:**
- Ensure Dompdf library is properly installed
- Check write permissions on the server

### Support

For issues or questions:
1. Check the browser's developer console for errors
2. Verify all system requirements are met
3. Ensure proper file permissions (755 for directories, 644 for files)

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization
- CSRF protection recommendations

## Future Enhancements

- More sophisticated ML models
- Additional health metrics
- Mobile app integration
- Advanced reporting features
- Multi-language support
- Integration with wearable devices

## License

This project is for educational and research purposes.
