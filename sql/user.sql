CREATE TABLE `db_class_project`.`user` (
  `userid` INT UNSIGNED NOT NULL,
  `display_name` VARCHAR(30) NULL,
  `login_name` VARCHAR(30) NOT NULL,
  `password` VARCHAR(97) NOT NULL,
  `join_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` DATETIME NULL,
  `premium_status` DATETIME NULL,
  PRIMARY KEY (`userid`),
  UNIQUE INDEX `display_name_UNIQUE` (`display_name` ASC),
  UNIQUE INDEX `login_name_UNIQUE` (`login_name` ASC));
  
