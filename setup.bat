@echo off
echo === Lifestyle Risk Portal Setup ===
echo.

echo Setting up database...
"C:\wamp64\bin\php\php8.2.0\php.exe" setup_database.php
echo.

echo Training ML model...
cd ml
python train_model.py
cd ..
echo.

echo Testing system...
"C:\wamp64\bin\php\php8.2.0\php.exe" test_ml_integration.php
echo.

echo === Setup Complete ===
echo.
echo You can now access the portal at:
echo http://localhost/lifestyle-risk-portal-full/
echo.
echo Default test account:
echo Email: test@example.com
echo Password: password123
echo.

pause
