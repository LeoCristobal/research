#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Servo.h>

// ===== RFID Setup =====
#define SS_PIN D8   // GPIO15
#define RST_PIN D0  // GPIO16
MFRC522 mfrc522(SS_PIN, RST_PIN);

// ===== I2C LCD Setup =====
LiquidCrystal_I2C lcd(0x27, 16, 2);

// ===== Servo Setup =====
Servo myServo;
#define SERVO_PIN D3

// ===== WiFi & Server =====
const char* ssid = "T-Attack";
const char* password = "likeaboss08";
String serverUrl = "http://192.168.254.177:8000/getUID.php";

WiFiClient client;

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();

  lcd.init();
  lcd.backlight();
  lcd.setCursor(0,0);
  lcd.print("TAP YOUR CARD");

  myServo.attach(SERVO_PIN);
  myServo.write(0);

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  lcd.setCursor(0,1);
  lcd.print("Connecting...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi!");
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("TAP YOUR CARD");
}

void loop() {
  // Check for new RFID card
  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;

  // Read UID
  String uid = "";
  for (byte i=0; i<mfrc522.uid.size; i++) {
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  Serial.println("Scanned UID: " + uid);

  // Decide action: "check" if just reading, "open" if real access
  String action = "open"; // <-- default, you can change to "check" when using Read Tag page

  // Send UID via POST
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    if (http.begin(client, serverUrl)) {
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      String postData = "uid=" + uid + "&action=" + action;
      Serial.println("Sending POST data: " + postData);

      int httpResponseCode = http.POST(postData);
      Serial.println("HTTP Response Code: " + String(httpResponseCode));

      if (httpResponseCode > 0) {
        String response = http.getString();
        response.trim();
        Serial.println("Response: " + response);

        if (response == "AUTHORIZED" && action == "open") {
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print("DOOR OPENED");
          myServo.write(180);
          delay(5000);
          myServo.write(0);
        } else if (action == "open") {
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print("NOT AUTHORIZED");
          lcd.setCursor(0, 1);
          lcd.print("CARD");
          delay(2000);
        }
      } else {
        Serial.println("Error sending UID. HTTP code: " + String(httpResponseCode));
      }
      http.end();
    }
  }

  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("TAP YOUR CARD");

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}
