-- Database: nodemcu_rfid_iot_projects

-- Drop table if it exists (optional, for clean import)
DROP TABLE IF EXISTS user_info;

-- Table structure for table `user_info`
CREATE TABLE user_info (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    id VARCHAR(100) UNIQUE NOT NULL,
    gender VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(100) NOT NULL
);

-- Dumping data for table `user_info`
INSERT INTO user_info (name, id, gender, email, mobile) VALUES
('Leo', '39EAB06D', 'Male', 'leo@gmail.com', '991252104'),
('Azumi', '769174F8', 'Female', 'azumi@email.com', '23456789');
