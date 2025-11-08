@echo off
echo === Lifestyle Risk Portal System Test ===
echo.

echo 1. Testing PHP...
"C:\wamp64\bin\php\php8.2.0\php.exe" --version
if %ERRORLEVEL% EQU 0 (
    echo ✓ PHP is working
) else (
    echo ✗ PHP not found
    goto :end
)
echo.

echo 2. Testing database connection...
"C:\wamp64\bin\php\php8.2.0\php.exe" -r "
try {
    $mysqli = new mysqli('localhost', 'root', '', 'lifestyle_portal');
    if ($mysqli->connect_errno) {
        echo '✗ Database connection failed: ' . $mysqli->connect_error;
    } else {
        echo '✓ Database connection successful';
        $result = $mysqli->query('SELECT COUNT(*) as count FROM users');
        if ($result) {
            $row = $result->fetch_assoc();
            echo '✓ Users table accessible (found ' . $row['count'] . ' users)';
        }
        $mysqli->close();
    }
} catch (Exception $e) {
    echo '✗ Database error: ' . $e->getMessage();
}
"
echo.

echo 3. Testing Python...
python --version
if %ERRORLEVEL% EQU 0 (
    echo ✓ Python is working
) else (
    echo ✗ Python not found
    goto :end
)
echo.

echo 4. Testing ML prediction...
cd ml
python predict_risk.py 25.5 120 0 0 2
if %ERRORLEVEL% EQU 0 (
    echo ✓ ML prediction working
) else (
    echo ✗ ML prediction failed
)
cd ..
echo.

echo 5. Testing web server...
echo You can test the web interface at: http://localhost/lifestyle-risk-portal-full/
echo.

echo === Test Complete ===
echo If all tests pass, the system should be working!
echo.

:end
pause
