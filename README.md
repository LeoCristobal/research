# 🔧 ESP8266 Door Lock System

This project uses an **ESP8266**, **RFID-RC522**, **I2C LCD Display**, and a **Servo Motor** to create a simple smart door lock system.

---

## 📦 Components Used

* ESP8266 (NodeMCU)
* RFID-RC522 module
* I2C LCD Display (16x2 or 20x4)
* Servo Motor (SG90 or compatible)
* Jumper wires
* Breadboard
* Power supply (5V for LCD & Servo, 3.3V for RFID)

---

## ⚡ Wiring Connections

### 🟦 RFID-RC522 → ESP8266

| RFID Pin | ESP8266 Pin |
| -------- | ----------- |
| 3.3V     | 3.3V        |
| GND      | GND         |
| SDA / SS | D8 (GPIO15) |
| SCK      | D5 (GPIO14) |
| MOSI     | D7 (GPIO13) |
| MISO     | D6 (GPIO12) |
| RST      | D0          |

---

### 🟩 I2C LCD Display → ESP8266

| LCD Pin | ESP8266 Pin |
| ------- | ----------- |
| GND     | GND         |
| VCC     | 5V          |
| SDA     | D2 (GPIO4)  |
| SCL     | D1 (GPIO5)  |

---

### 🟨 Servo Motor → ESP8266

| Servo Pin | ESP8266 Pin |
| --------- | ----------- |
| GND       | GND         |
| VCC       | 5V          |
| Signal    | D3 (GPIO0)  |

---

## ▶️ How to Run

1. **Upload the code** to your ESP8266 using Arduino IDE or PlatformIO.

   * Make sure you have the required libraries installed:

     * `MFRC522` (for RFID)
     * `Wire` & `LiquidCrystal_I2C` (for I2C LCD)
     * `Servo` (for servo motor)

2. **Connect the components** as shown in the wiring tables above.

3. **Power the ESP8266** via USB or external power supply.

4. **Run the local PHP server** (for backend communication if needed):

   ```bash
   "C:\xampp\php\php.exe" -S 0.0.0.0:8000 -t "C:\FOR RESEARCH\research"
   ```

5. **Open the project in your browser** at:

   ```
   http://localhost:8000
   ```

---

## 📝 Notes

* Ensure the **RFID module is powered at 3.3V only** to avoid damage.
* Use a stable **5V supply** for the servo and LCD.
* If the servo jitters, use an external 5V power supply instead of the ESP8266 5V pin.

---
