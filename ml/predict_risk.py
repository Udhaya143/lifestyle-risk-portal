import sys
import joblib
import numpy as np
import json

def validate_inputs(args):
    """Validate and convert command-line inputs"""
    if len(args) != 6:
        return None, "Error: Expected 5 inputs (bmi, bp, smoking, alcohol, activity)."
    try:
        bmi = float(args[1])
        bp = float(args[2])
        smoking = int(args[3])  # 0 or 1
        alcohol = int(args[4])  # 0-3
        activity = int(args[5]) # minutes/day category

        # Basic validation
        if bmi <= 0 or bp <= 0:
            return None, "Error: Invalid inputs (BMI/BP must be positive)."

        features = np.array([[bmi, bp, smoking, alcohol, activity]])
        return features, None
    except Exception as e:
        return None, f"Error: Invalid inputs. Details: {str(e)}"

def map_risk_level(prediction):
    return {0: "Low", 1: "Moderate", 2: "High"}.get(prediction, "Unknown")

def get_recommendations(risk_level):
    if risk_level == "Low":
        return "Maintain a healthy lifestyle. Keep up regular activity and balanced diet."
    elif risk_level == "Moderate":
        return ("Consider weight management: increase activity, reduce caloric intake. "
                "Aim for at least 150 mins of moderate exercise per week. "
                "Increase fruits & vegetables to 5 servings/day.")
    elif risk_level == "High":
        return ("Consult a healthcare professional. High risk detected. "
                "Focus on medical check-up, strict diet control, and quitting harmful habits.")
    return "No recommendation available."

def main():
    # Validate inputs
    features, error = validate_inputs(sys.argv)
    if error:
        print(error)
        return

    try:
        # Load model + scaler
        model = joblib.load("ml/model/disease_risk_model.pkl")
        scaler = joblib.load("ml/model/scaler.pkl")

        # Scale inputs
        features_scaled = scaler.transform(features)

        # Prediction
        prediction = model.predict(features_scaled)[0]
        prob = model.predict_proba(features_scaled).max() * 100

        # Prepare output
        risk_level = map_risk_level(prediction)
        possible_risks = []
        if risk_level == "Moderate":
            possible_risks = ["Obesity risk", "Pre-diabetes"]
        elif risk_level == "High":
            possible_risks = ["Diabetes", "Heart disease", "Hypertension"]

        result = {
            "risk_level": risk_level,
            "possible_risks": possible_risks,
            "confidence": round(prob, 2),
            "recommendations": get_recommendations(risk_level)
        }

        # Output JSON (easy for PHP to parse)
        print(json.dumps(result))

    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    main()
