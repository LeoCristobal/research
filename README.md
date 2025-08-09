# Smart Door Lock System

A comprehensive IoT-based smart door lock system using NodeMCU ESP8266, RFID authentication, and a PHP web interface.

## Features

- **RFID Authentication**: Secure access control using RFID cards
- **WiFi Connectivity**: Remote monitoring and control via web interface
- **Real-time Status**: Live door status and user activity monitoring
- **User Management**: Add, edit, and delete authorized users
- **Access History**: Complete log of all door access attempts
- **LCD Display**: Real-time status messages on I2C LCD
- **Servo Control**: Automated door lock/unlock mechanism

## System Components

### Hardware
- **NodeMCU ESP8266** - WiFi-enabled microcontroller
- **RFID-RC522 Module** - RFID card reader
- **SG90 Servo Motor** - Door lock mechanism
- **I2C LCD Display (16x2)** - Status display
- **Breadboard** - For prototyping
- **Power Supply** - 5V/2A for stable operation

### Software
- **Arduino IDE** with ESP8266 board support
- **PHP Web Interface** with MySQL database
- **Required Libraries** (see Arduino code for includes)

## Quick Start

### 1. Database Setup
1. Create a MySQL database named `door_lock_system`
2. Import the `database_setup.sql` file
3. Ensure your web server can connect to MySQL

### 2. Hardware Assembly
1. Follow the wiring diagram in `hardware_setup.md`
2. Connect all components to the breadboard
3. Power up the system

### 3. Software Configuration
1. Upload `smart_door_lock.ino` to your NodeMCU
2. Update WiFi credentials in the Arduino code
3. Update server IP address in the code

### 4. Web Interface Setup
1. Place all PHP files in your web server directory
2. Access the system via `index.php`
3. Enter WiFi credentials to configure the system

## File Structure

```
smart-door-lock/
├── index.php                 # Login page
├── dashboard.php            # Main dashboard
├── users.php               # User management
├── registration.php        # User registration
├── read_tag.php           # RFID tag reading
├── history.php            # Access history
├── login_process.php      # Login processing
├── logout.php             # Logout functionality
├── check_access.php       # RFID access verification
├── log_access.php         # Access logging
├── get_status.php         # System status API
├── smart_door_lock.ino    # Arduino code for ESP8266
├── database_setup.sql     # Database structure
├── hardware_setup.md      # Hardware assembly guide
└── README.md              # This file
```

## How It Works

### 1. RFID Authentication
1. User taps RFID card on reader
2. ESP8266 reads card UID
3. UID is sent to web server for verification
4. Server checks database for authorized users
5. Access granted/denied based on user status

### 2. Door Control
1. If access granted, servo motor unlocks door
2. LCD displays "Door Open" message
3. Door remains unlocked for 3 seconds
4. Servo automatically locks door
5. All events are logged to database

### 3. Web Interface
1. Admin logs in with WiFi credentials
2. Dashboard shows system status and statistics
3. User management for adding/editing authorized users
4. Real-time access history and monitoring
5. RFID tag reading and verification

## Configuration

### WiFi Settings
- Update `ssid` and `password` in Arduino code
- Ensure ESP8266 can connect to your network
- Check server IP address in code

### Database Settings
- Update database connection details in PHP files
- Default: localhost, root, no password
- Create database: `door_lock_system`

### Hardware Pins
- RFID: D2 (SS), D1 (SCK), D6 (MOSI), D7 (MISO), D5 (RST)
- LCD: D3 (SDA), D5 (SCL)
- Servo: D4 (PWM)

## Security Features

- **Session Management**: Secure login/logout system
- **SQL Injection Protection**: Prepared statements
- **Access Control**: WiFi credential verification
- **Audit Trail**: Complete access logging
- **User Authentication**: RFID card validation

## Troubleshooting

### Common Issues

1. **ESP8266 Not Connecting to WiFi**
   - Check WiFi credentials
   - Verify network signal strength
   - Check power supply stability

2. **RFID Not Reading Cards**
   - Verify SPI connections
   - Check RFID module power
   - Test with known working cards

3. **Web Interface Not Accessible**
   - Check database connection
   - Verify file permissions
   - Check web server configuration

4. **Servo Not Moving**
   - Verify power supply (5V required)
   - Check PWM pin connection
   - Test servo with basic Arduino code

### Testing Checklist
- [ ] Power supply stable at 5V
- [ ] NodeMCU boots and connects to WiFi
- [ ] RFID module reads cards
- [ ] LCD displays messages
- [ ] Servo moves smoothly
- [ ] Web interface accessible
- [ ] Database connection working

## API Endpoints

### For ESP8266 Communication
- `GET /check_access.php?uid={RFID_UID}` - Check access permission
- `POST /log_access.php` - Log access events

### For Web Interface
- `GET /get_status.php` - Get system status
- `POST /login_process.php` - Process login
- `POST /users.php` - User management

## Maintenance

### Regular Tasks
1. Monitor access logs for suspicious activity
2. Clean RFID reader surface
3. Check wire connections
4. Backup user database
5. Update system software

### Security Updates
1. Change default passwords
2. Monitor for security vulnerabilities
3. Keep libraries updated
4. Regular security audits

## Support

For technical support or questions:
1. Check the troubleshooting section
2. Review hardware setup guide
3. Verify all connections
4. Test individual components
5. Check system logs

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

---

**Note**: This system is designed for educational and personal use. For commercial or security-critical applications, additional security measures should be implemented.
