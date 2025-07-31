CREATE DATABASE moderntech_hr;
USE moderntech_hr;

-- Employees Table
CREATE TABLE employees (
    employee_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    employment_history TEXT,
    contact_email VARCHAR(100) NOT NULL
);

-- Payroll Data Table
CREATE TABLE payroll_data (
    payroll_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    hours_worked DECIMAL(6,2),
    leave_deductions INT,
    final_salary DECIMAL(10,2),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)ON DELETE CASCADE 
)ENGINE=InnoDB;

-- Attendance Table
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)ON DELETE CASCADE
) ENGINE=InnoDB;


-- Leave Requests Table
CREATE TABLE leave_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    date DATE NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('Approved', 'Denied', 'Pending') NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert Employee Data
INSERT INTO employees (employee_id, name, position, department, salary, employment_history, contact_email) VALUES
(1, 'Sibongile Nkosi', 'Software Engineer', 'Development', 70000, 'Joined in 2015, promoted to Senior in 2018', 'sibongile.nkosi@moderntech.com'),
(2, 'Lungile Moyo', 'HR Manager', 'HR', 80000, 'Joined in 2013, promoted to Manager in 2017', 'lungile.moyo@moderntech.com'),
(3, 'Thabo Molefe', 'Quality Analyst', 'QA', 55000, 'Joined in 2018', 'thabo.molefe@moderntech.com'),
(4, 'Keshav Naidoo', 'Sales Representative', 'Sales', 60000, 'Joined in 2020', 'keshav.naidoo@moderntech.com'),
(5, 'Zanele Khumalo', 'Marketing Specialist', 'Marketing', 58000, 'Joined in 2019', 'zanele.khumalo@moderntech.com'),
(6, 'Sipho Zulu', 'UI/UX Designer', 'Design', 65000, 'Joined in 2016', 'sipho.zulu@moderntech.com'),
(7, 'Naledi Moeketsi', 'DevOps Engineer', 'IT', 72000, 'Joined in 2017', 'naledi.moeketsi@moderntech.com'),
(8, 'Farai Gumbo', 'Content Strategist', 'Marketing', 56000, 'Joined in 2021', 'farai.gumbo@moderntech.com'),
(9, 'Karabo Dlamini', 'Accountant', 'Finance', 62000, 'Joined in 2018', 'karabo.dlamini@moderntech.com'),
(10, 'Fatima Patel', 'Customer Support Lead', 'Support', 58000, 'Joined in 2016', 'fatima.patel@moderntech.com');

-- Insert Payroll Data
INSERT INTO payroll_data (employee_id, hours_worked, leave_deductions, final_salary) VALUES
(1, 160, 8, 69500),
(2, 150, 10, 79000),
(3, 170, 4, 54800),
(4, 165, 6, 59700),
(5, 158, 5, 57850),
(6, 168, 2, 64800),
(7, 175, 3, 71800),
(8, 160, 0, 56000),
(9, 155, 5, 61500),
(10, 162, 4, 57750);

-- Insert Attendance Data
INSERT INTO attendance (employee_id, date, status) VALUES
(1, '2025-07-25', 'Present'), (1, '2025-07-26', 'Absent'), (1, '2025-07-27', 'Present'), (1, '2025-07-28', 'Present'), (1, '2025-07-29', 'Present'),
(2, '2025-07-25', 'Present'), (2, '2025-07-26', 'Present'), (2, '2025-07-27', 'Absent'), (2, '2025-07-28', 'Present'), (2, '2025-07-29', 'Present'),
(3, '2025-07-25', 'Present'), (3, '2025-07-26', 'Present'), (3, '2025-07-27', 'Present'), (3, '2025-07-28', 'Absent'), (3, '2025-07-29', 'Present'),
(4, '2025-07-25', 'Absent'), (4, '2025-07-26', 'Present'), (4, '2025-07-27', 'Present'), (4, '2025-07-28', 'Present'), (4, '2025-07-29', 'Present'),
(5, '2025-07-25', 'Present'), (5, '2025-07-26', 'Present'), (5, '2025-07-27', 'Absent'), (5, '2025-07-28', 'Present'), (5, '2025-07-29', 'Present'),
(6, '2025-07-25', 'Present'), (6, '2025-07-26', 'Present'), (6, '2025-07-27', 'Absent'), (6, '2025-07-28', 'Present'), (6, '2025-07-29', 'Present'),
(7, '2025-07-25', 'Present'), (7, '2025-07-26', 'Present'), (7, '2025-07-27', 'Present'), (7, '2025-07-28', 'Absent'), (7, '2025-07-29', 'Present'),
(8, '2025-07-25', 'Present'), (8, '2025-07-26', 'Absent'), (8, '2025-07-27', 'Present'), (8, '2025-07-28', 'Present'), (8, '2025-07-29', 'Present'),
(9, '2025-07-25', 'Present'), (9, '2025-07-26', 'Present'), (9, '2025-07-27', 'Present'), (9, '2025-07-28', 'Absent'), (9, '2025-07-29', 'Present'),
(10, '2025-07-25', 'Present'), (10, '2025-07-26', 'Present'), (10, '2025-07-27', 'Absent'), (10, '2025-07-28', 'Present'), (10, '2025-07-29', 'Present');

-- Insert Leave Requests
INSERT INTO leave_requests (employee_id, date, reason, status) VALUES
(1, '2025-07-22', 'Sick Leave', 'Approved'),
(1, '2024-12-01', 'Personal', 'Pending'),
(2, '2025-07-15', 'Family Responsibility', 'Denied'),
(2, '2024-12-02', 'Vacation', 'Approved'),
(3, '2025-07-10', 'Medical Appointment', 'Approved'),
(3, '2024-12-05', 'Personal', 'Pending'),
(4, '2025-07-20', 'Bereavement', 'Approved'),
(5, '2024-12-01', 'Childcare', 'Pending'),
(6, '2025-07-18', 'Sick Leave', 'Approved'),
(7, '2025-07-22', 'Vacation', 'Pending'),
(8, '2024-12-02', 'Medical Appointment', 'Approved'),
(9, '2025-07-19', 'Childcare', 'Denied'),
(10, '2024-12-03', 'Vacation', 'Pending');

-- Performance Reviews Table
CREATE TABLE `performance_reviews` (
  `review_id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` INT NOT NULL,
  `employee_name` VARCHAR(100) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `reviewer` VARCHAR(100) NOT NULL,
  `punctuality` ENUM('Poor', 'Average', 'Good', 'Excellent') NOT NULL,
  `dependability` ENUM('Poor', 'Average', 'Good', 'Excellent') NOT NULL,
  PRIMARY KEY (`review_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert sample performance review data
INSERT INTO `performance_reviews` (
  `employee_id`,
  `employee_name`, 
  `department`, 
  `reviewer`, 
  `punctuality`, 
  `dependability`
) VALUES
(1, 'Sibongile Nkosi', 'Development', 'Khanyiso Haman', 'Good', 'Excellent'),
(2, 'Lungile Moyo', 'HR', 'Khanyiso Haman', 'Average', 'Good'),
(3, 'Thabo Molefe', 'QA', 'Matthew Brown', 'Good', 'Good'),
(4, 'Keshav Naidoo', 'Sales', 'Ruth N''zola', 'Good', 'Good'),
(5, 'Zanele Khumalo', 'Marketing', 'Khanyiso Haman', 'Poor', 'Poor'),
(6, 'Sipho Zulu', 'Marketing', 'Ruth N''zola', 'Good', 'Good'),
(7, 'Naledi Moeketsi', 'IT', 'Ruth N''zola', 'Good', 'Excellent');