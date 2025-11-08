import sys
import joblib
import pandas as pd

# Load models
risk_model = joblib.load("C:/wamp64/www/lifestyle-risk-portal-full/ml/risk_model.pkl")
disease_model = joblib.load("C:/wamp64/www/lifestyle-risk-portal-full/ml/disease_model.pkl")

# Collect inputs
age = float(sys.argv[1])
bmi = float(sys.argv[2])
bp = float(sys.argv[3])
smoking = int(sys.argv[4])
alcohol = int(sys.argv[5])
activity = int(sys.argv[6])
family_history = int(sys.argv[7])

features = pd.DataFrame([[age,bmi,bp,smoking,alcohol,activity,family_history]],
    columns=["age","bmi","bp","smoking","alcohol","activity","family_history"])

# Predict overall risk
risk_pred = risk_model.predict(features)[0]

# Predict diseases
disease_pred = disease_model.predict(features)[0]
disease_labels = ["Hypertension","Diabetes","Heart Disease","Osteoporosis","Mental Health"]
disease_results = [disease_labels[i] for i, val in enumerate(disease_pred) if val == 1]

# Output results
print("Risk Level:", risk_pred)
print("Diseases:", ", ".join(disease_results) if disease_results else "None")
