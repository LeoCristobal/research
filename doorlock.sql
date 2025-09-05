-- Database: nodemcu_rfid_iot_projects

-- Drop tables if they exist (optional, for clean import)
DROP TABLE IF EXISTS access_log;
DROP TABLE IF EXISTS user_info;

-- Table structure for table `user_info`
CREATE TABLE user_info (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    id VARCHAR(100) UNIQUE NOT NULL,
    gender VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Table structure for table `access_log`
CREATE TABLE access_log (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    rfid_id VARCHAR(100),
    action VARCHAR(50),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_info(user_id)
) ENGINE=InnoDB;

-- Dumping data for table `user_info`
INSERT INTO user_info (name, id, gender, email, mobile) VALUES
('Leo', '39EAB06D', 'Male', 'leo@gmail.com', '991252104'),
('Azumi', '769174F8', 'Female', 'azumi@email.com', '23456789');

-- Sample log entry
INSERT INTO access_log (user_id, rfid_id, action) VALUES (1, '39EAB06D', 'login');