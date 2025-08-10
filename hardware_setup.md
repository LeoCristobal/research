# Smart Door Lock System - Hardware Setup Guide

## Components Required

### Hardware Components
1. **NodeMCU ESP8266** - WiFi-enabled microcontroller
2. **RFID-RC522 Module** - RFID card reader
3. **SG90 Servo Motor** - Door lock mechanism
4. **I2C LCD Display (16x2)** - Status display
5. **Breadboard** - For prototyping
6. **Jumper Wires** - For connections
7. **Power Supply** - 5V/2A for stable operation
8. **RFID Cards/Tags** - For user authentication

### Software Requirements
- Arduino IDE with ESP8266 board support
- Required libraries (see Arduino code for includes)
-link:
-https://github.com/johnrickman/LiquidCrystal_I2C
-https://github.com/miguelbalboa/rfid
-put in preferences - http://arduino.esp8266.com/stable/package_esp8266com_index.json


## Wiring Diagram

```
NodeMCU ESP8266 Pin Connections:

Power:
- VIN/V5 → 5V Power Supply
- GND → Common Ground

RFID-RC522:
- 3.3V → 3.3V
- GND → GND
- D8 (GPIO15) → SDA/SS
- D5 (GPIO14) → SCK
- D7 (GPIO13) → MOSI
- D6 (GPIO12) → MISO
- D0 (GPIO16) → RST

I2C LCD Display:
- GND → GND
- VCC → 5V
- D2 (GPIO4) → SDA
- D1 (GPIO5) → SCL

Servo Motor:
- GND → GND
- 5V → 5V
- D3 (GPIO0) → Signal (yellow wire)

Breadboard Connections:
- Connect all GND pins to common ground
- Connect 5V power supply to breadboard power rail
- Connect 3.3V from NodeMCU to breadboard power rail
```

## Detailed Pin Configuration

### NodeMCU ESP8266 Pin Mapping
```
GPIO0  → D3  → Servo Signal (yellow wire)
GPIO1  → TX  → Not used
GPIO2  → D4  → Not used
GPIO3  → RX  → Not used
GPIO4  → D2  → LCD SDA
GPIO5  → D1  → LCD SCL
GPIO12 → D6  → RFID MISO
GPIO13 → D7  → RFID MOSI
GPIO14 → D5  → RFID SCK
GPIO15 → D8  → RFID SDA/SS
GPIO16 → D0  → RFID RST
```

### Power Distribution
```
5V Power Supply:
├── NodeMCU VIN
├── Servo Motor
└── Breadboard Power Rail

3.3V from NodeMCU:
├── RFID Module
└── LCD Display

Common Ground:
├── Power Supply GND
├── NodeMCU GND
├── RFID Module GND
├── LCD Display GND
└── Servo Motor GND
```

## Assembly Instructions

### Step 1: Power Setup
1. Connect 5V power supply to breadboard power rail
2. Connect NodeMCU VIN to 5V rail
3. Connect all GND pins to common ground

### Step 2: RFID Module
1. Connect RFID-RC522 to NodeMCU using SPI pins
2. Ensure proper voltage levels (3.3V for RFID)
3. Test RFID reading functionality

### Step 3: LCD Display
1. Connect I2C LCD to NodeMCU I2C pins
2. Verify I2C address (usually 0x27 or 0x3F)
3. Test display functionality

### Step 4: Servo Motor
1. Connect servo to NodeMCU PWM pin
2. Ensure adequate power supply for servo
3. Test servo movement range

### Step 5: Testing
1. Upload Arduino code to NodeMCU
2. Test WiFi connection
3. Test RFID reading
4. Test servo movement
5. Test LCD display

## Troubleshooting

### Common Issues
1. **WiFi Connection Failed**
   - Check WiFi credentials in code
   - Verify WiFi signal strength
   - Check power supply stability

2. **RFID Not Reading**
   - Verify SPI connections
   - Check RFID module power
   - Test with known working cards

3. **Servo Not Moving**
   - Check power supply (servo needs 5V)
   - Verify PWM pin connection
   - Check servo signal wire

4. **LCD Not Displaying**
   - Verify I2C connections
   - Check I2C address
   - Ensure proper power supply

### Testing Checklist
- [ ] Power supply stable at 5V
- [ ] NodeMCU boots and connects to WiFi
- [ ] RFID module reads cards
- [ ] LCD displays messages
- [ ] Servo moves smoothly
- [ ] Web interface accessible
- [ ] Database connection working

## Safety Considerations

1. **Electrical Safety**
   - Use appropriate power supply
   - Avoid short circuits
   - Secure all connections

2. **Mechanical Safety**
   - Ensure servo can't cause injury
   - Secure door lock mechanism
   - Test door operation safely

3. **Network Security**
   - Change default WiFi passwords
   - Use secure network
   - Monitor access logs

## Maintenance

1. **Regular Checks**
   - Clean RFID reader surface
   - Check wire connections
   - Monitor system logs

2. **Updates**
   - Keep Arduino libraries updated
   - Monitor for security updates
   - Backup user database regularly
