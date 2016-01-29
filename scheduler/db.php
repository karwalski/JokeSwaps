<?php
$servername = "localhost";
$username = "root";
$password = "YmQCl60qMwe2YpUn34k7";
// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`parent` (
  `id` INT NOT NULL,
  `username` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `address` VARCHAR(45) NULL,
  `suburb` VARCHAR(45) NULL,
  `postcode` INT NULL,
  `state` VARCHAR(45) NULL,
  `phone1` INT NULL,
  `phone2` INT NULL,
  `email` VARCHAR(45) NULL,
  `alternatefirstname` VARCHAR(45) NULL,
  `alternatelastname` VARCHAR(45) NULL,
  `alternatephone` VARCHAR(45) NULL,
  `datejoined` DATE NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table parent created successfully";
} else {
    echo "Error creating table parent: " . $conn->error;
}
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`child` (
  `id` INT NOT NULL,
  `parentid` INT NULL,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `dob` DATE NULL,
  `diet` VARCHAR(45) NULL,
  `medical` VARCHAR(45) NULL,
  `sleepend` DECIMAL(4,2) NULL,
  `sleepstart` DECIMAL(4,2) NULL,
  `other` VARCHAR(45) NULL,
  `rate` DECIMAL(4,2) NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table child created successfully";
} else {
    echo "Error creating table child: " . $conn->error;
}
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`booking` (
  `id` INT NOT NULL,
  `childid` INT NULL,
  `starttime` DECIMAL(4,2) NULL,
  `endtime` DECIMAL(4,2) NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table booking created successfully";
} else {
    echo "Error creating table booking: " . $conn->error;
}
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`admin` (
  `id` INT NOT NULL,
  `starttime` DECIMAL(4,2) NULL,
  `endtime` DECIMAL(4,2) NULL,
  `buffertime` DECIMAL(4,2) NULL,
  `timezone` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table admin created successfully";
} else {
    echo "Error creating table admin: " . $conn->error;
}
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`staff` (
  `id` INT NOT NULL,
  `username` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `adminlevel` INT NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table staff created successfully";
} else {
    echo "Error creating table staff: " . $conn->error;
}
$sql = "CREATE TABLE IF NOT EXISTS `scheduler`.`available` (
  `id` INT NOT NULL,
  `staffid` INT NULL,
  `starttime` DECIMAL(4,2) NULL,
  `endtime` DECIMAL(4,2) NULL,
  PRIMARY KEY (`id`))
  ";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table available created successfully";
} else {
    echo "Error creating table available: " . $conn->error;
}
?>
