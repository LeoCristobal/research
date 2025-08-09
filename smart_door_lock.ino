/*
 * Smart Door Lock System
 * Hardware: NodeMCU ESP8266, RFID-RC522, Servo Motor, I2C LCD
 * Features: WiFi connectivity, RFID authentication, door control, LCD display
 */

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Servo.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_NAME";      // Change this to your WiFi name
const char* password = "YOUR_WIFI_PASSWORD"; // Change this to your WiFi password

// Hardware Pins
#define RST_PIN         5     // D1
#define SS_PIN          4     // D2
#define SERVO_PIN       2     // D4
#define LCD_SDA         0     // D3
#define LCD_SCL         14    // D5

// Web Server Configuration
const char* host = "192.168.1.100"; // Change this to your computer's IP address
const int httpPort = 80;
const char* baseUrl = "/v2"; // Change this to match your project folder

// Objects
MFRC522 mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2); // LCD address 0x27, 16 chars, 2 lines
Servo doorServo;

// Variables
String lastUID = "";
bool doorLocked = true;
int doorAngle = 0; // 0 = locked, 90 = unlocked

void setup() {
  Serial.begin(115200);
  
  // Initialize SPI for RFID
  SPI.begin();
  mfrc522.PCD_Init();
  
  // Initialize I2C LCD
  Wire.begin(LCD_SDA, LCD_SCL);
  lcd.init();
  lcd.backlight();
  
  // Initialize Servo
  doorServo.attach(SERVO_PIN);
  doorServo.write(0); // Lock door initially
  
  // Initialize LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Smart Door Lock");
  lcd.setCursor(0, 1);
  lcd.print("Initializing...");
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi...");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    lcd.setCursor(0, 1);
    lcd.print("WiFi: Connecting");
  }
  
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  
  // Display ready message
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("System Ready!");
  lcd.setCursor(0, 1);
  lcd.print("Tap RFID Card");
  
  delay(2000);
  
  // Show WiFi info
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi: " + WiFi.SSID());
  lcd.setCursor(0, 1);
  lcd.print("IP: " + WiFi.localIP().toString());
  
  delay(3000);
  
  // Show ready message again
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("System Ready!");
  lcd.setCursor(0, 1);
  lcd.print("Tap RFID Card");
}

void loop() {
  // Check WiFi connection
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi Disconnected");
    lcd.setCursor(0, 1);
    lcd.print("Reconnecting...");
    
    WiFi.reconnect();
    delay(5000);
    return;
  }
  
  // Check if new RFID card is present
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    String uid = "";
    
    // Read UID
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    
    // Check if it's a new card
    if (uid != lastUID) {
      lastUID = uid;
      Serial.println("Card detected: " + uid);
      
      // Display card detected message
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Card Detected:");
      lcd.setCursor(0, 1);
      lcd.print(uid);
      
      delay(1000);
      
      // Authenticate card with web server
      if (authenticateCard(uid)) {
        // Access granted
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Access Granted!");
        lcd.setCursor(0, 1);
        lcd.print("Door Opening...");
        
        unlockDoor();
        logAccess(uid, "Granted", "Opened");
        
        delay(3000);
        
        // Lock door after delay
        lockDoor();
        logAccess(uid, "Granted", "Closed");
        
      } else {
        // Access denied
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Invalid Card!");
        lcd.setCursor(0, 1);
        lcd.print("Access Denied");
        
        logAccess(uid, "Denied", "Closed");
        
        delay(3000);
      }
      
      // Show ready message
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("System Ready!");
      lcd.setCursor(0, 1);
      lcd.print("Tap RFID Card");
    }
    
    // Halt PICC
    mfrc522.PICC_HaltA();
    // Stop encryption on PCD
    mfrc522.PCD_StopCrypto1();
  }
  
  delay(100);
}

bool authenticateCard(String uid) {
  WiFiClient client;
  HTTPClient http;
  
  String url = String("http://") + host + baseUrl + "/check_access.php?uid=" + uid;
  
  Serial.println("Checking access for UID: " + uid);
  Serial.println("URL: " + url);
  
  if (http.begin(client, url)) {
    int httpCode = http.GET();
    
    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println("HTTP Response: " + payload);
      
      // Check if access is granted
      if (payload.indexOf("GRANTED") != -1) {
        return true;
      }
    } else {
      Serial.println("HTTP request failed");
    }
    
    http.end();
  }
  
  return false;
}

void unlockDoor() {
  Serial.println("Unlocking door...");
  doorLocked = false;
  
  // Move servo to unlock position
  for (int angle = 0; angle <= 90; angle += 5) {
    doorServo.write(angle);
    delay(50);
  }
  
  Serial.println("Door unlocked");
}

void lockDoor() {
  Serial.println("Locking door...");
  doorLocked = true;
  
  // Move servo to lock position
  for (int angle = 90; angle >= 0; angle -= 5) {
    doorServo.write(angle);
    delay(50);
  }
  
  Serial.println("Door locked");
}

void logAccess(String uid, String accessType, String doorStatus) {
  WiFiClient client;
  HTTPClient http;
  
  String url = String("http://") + host + baseUrl + "/log_access.php";
  String postData = "uid=" + uid + "&access_type=" + accessType + "&door_status=" + doorStatus;
  
  Serial.println("Logging access: " + postData);
  
  if (http.begin(client, url)) {
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    int httpCode = http.POST(postData);
    
    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println("Log response: " + payload);
    } else {
      Serial.println("Log request failed");
    }
    
    http.end();
  }
}

// Function to update WiFi credentials from web interface
void updateWiFiCredentials(String newSSID, String newPassword) {
  // This function can be called via web interface to update WiFi settings
  // For security, implement proper authentication before allowing updates
  
  WiFi.disconnect();
  WiFi.begin(newSSID.c_str(), newPassword.c_str());
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi Updated");
  lcd.setCursor(0, 1);
  lcd.print("Reconnecting...");
  
  delay(5000);
}
