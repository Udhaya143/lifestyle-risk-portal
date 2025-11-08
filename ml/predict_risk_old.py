import sys
import joblib
import pandas as pd

# Load model
model = joblib.load("C:/wamp64/www/lifestyle-risk-portal-full/ml/risk_model.pkl")

# Collect CLI arguments
try:
    bmi = float(sys.argv[1])
    bp = float(sys.argv[2])
    smoking = int(sys.argv[3])
    exercise = int(sys.argv[4])
    diet = int(sys.argv[5])
except:
    print("Error: Invalid inputs.")
    sys.exit(1)

# Same feature names used in training
feature_names = ["bmi", "blood_pressure", "smoking", "exercise", "diet"]

# Wrap into DataFrame
input_data = pd.DataFrame([[bmi, bp, smoking, exercise, diet]], columns=feature_names)

# Predict
pred = model.predict(input_data)

# Output
if pred[0] == 0:
    print("Low Risk")
else:
    print("High Risk")
