create database mohammed4;

use mohammed4;

CREATE TABLE student(
    regno varchar(30) primary key,
    name varchar(20) not null,
    dept varchar(20) not null,
    dob date not null,
    email varchar(50) unique
);
insert into student (regno, name, dept, dob) values('ai01', 'Aasif', 'AI', '2025-10-22');
insert into student (regno, name, dept, dob) values('ai02', 'Bharath', 'AI', '2005-10-22');
insert into student (regno, name, dept, dob) values('ai03', 'Chandru', 'AI', '2005-10-23');
insert into student (regno, name, dept, dob) values('ai04', 'Dinesh', 'AI', '2005-10-24');
insert into student (regno, name, dept, dob) values('ai05', 'Eshwaran ', 'AI', '2005-10-25');
insert into student (regno, name, dept, dob) values('ai06', 'Fahim', 'AI', '2005-10-26');
insert into student (regno, name, dept, dob) values('ai07', 'Gokul', 'AI', '2005-10-27');
insert into student (regno, name, dept, dob) values('ai08', 'kishore', 'AI', '2005-10-28');
INSERT INTO student (regno, name, dept, dob) VALUES('ai09', 'Lokesh', 'AI', '2005-10-29');
insert into student (regno, name, dept, dob) values('ai10', 'Mohammed', 'AI', '2005-10-30');
insert into student (regno, name, dept, dob) values('ai11', 'Nishar', 'AI', '2005-10-31');
insert into student (regno, name, dept, dob) values('ai12', 'vinoth', 'AI', '2005-10-02');
insert into student (regno, name, dept, dob) values('ds01', 'anbu', 'DS', '2006-11-21');
insert into student (regno, name, dept, dob) values('ds02', 'bala', 'DS', '2006-11-22');
insert into student (regno, name, dept, dob) values('ds03', 'dhamo', 'DS', '2006-11-23');
insert into student (regno, name, dept, dob) values('ds04', 'ismail', 'DS', '2006-11-24');
insert into student (regno, name, dept, dob) values('ds05', 'sachin', 'DS', '2006-11-25');
insert into student (regno, name, dept, dob) values('ds06', 'samuyil', 'DS', '2006-11-26');
insert into student (regno, name, dept, dob) values('ds07', 'suresh', 'DS', '2006-11-27');
insert into student (regno, name, dept, dob) values('ds08', 'vijay', 'DS', '2006-11-28');
insert into student (regno, name, dept, dob) values('ds09', 'yakesh', 'DS', '2006-11-29');
insert into student (regno, name, dept, dob) values('ds10', 'zab', 'DS', '2006-11-30');



CREATE TABLE staff(
    regno int primary key,
    password varchar(20) not null,
    name varchar(20)
);
insert into staff values(101,'ai@01','Aasif');
insert into staff values(102,'ai@02','Bharath');

CREATE TABLE stat (
    regno VARCHAR(30) NOT NULL,
    name VARCHAR(20) NOT NULL,
    dept VARCHAR(20) NOT NULL,
    dt DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    percentage FLOAT,
    FOREIGN KEY (regno) REFERENCES student(regno) 
        ON DELETE CASCADE,
    UNIQUE (regno,dt)
);



CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin (username: admin, password: 1234)
INSERT INTO admin (username, password) VALUES ('aasif', '786');

