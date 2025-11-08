import pandas as pd
import joblib
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier

# Load dataset
data = pd.read_csv("C:/wamp64/www/lifestyle-risk-portal-full/ml/data/health_data.csv")

# Features (inputs)
X = data[["age","bmi","bp","smoking","alcohol","activity","family_history"]]

# Target variables (multiple outputs)
y_risk = data["risk_level"]   # overall Low/Medium/High
y_diseases = data[["hypertension","diabetes","heart_disease","osteoporosis","mental_health"]]

# Train/Test split
X_train, X_test, y_risk_train, y_risk_test = train_test_split(X, y_risk, test_size=0.2, random_state=42)
_, _, y_diseases_train, y_diseases_test = train_test_split(X, y_diseases, test_size=0.2, random_state=42)

# Model for overall risk
risk_model = RandomForestClassifier(n_estimators=100, random_state=42)
risk_model.fit(X_train, y_risk_train)

# Model for individual diseases
disease_model = RandomForestClassifier(n_estimators=100, random_state=42)
disease_model.fit(X_train, y_diseases_train)

# Save models
joblib.dump(risk_model, "C:/wamp64/www/lifestyle-risk-portal-full/ml/risk_model.pkl")
joblib.dump(disease_model, "C:/wamp64/www/lifestyle-risk-portal-full/ml/disease_model.pkl")

print("✅ Models trained and saved successfully!")
