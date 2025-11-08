
/* Placeholder JS */
function calcBMI() {
  const h = parseFloat(document.querySelector('[name=height_cm]').value) || 0;
  const w = parseFloat(document.querySelector('[name=weight_kg]').value) || 0;
  if (h>0 && w>0) {
    const bmi = (w / ((h/100)*(h/100))).toFixed(2);
    const el = document.getElementById('bmi_preview');
    if (el) el.textContent = bmi;
  }
}
