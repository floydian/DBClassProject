<?php


			$stmt =  $conn->stmt_init();
			if ( !$stmt->prepare('insert into user (login_name, password) values (?, ?)') ) {
				$create_errors |= self::CANT_CREATE_USER;
			} else if ( !$stmt->bind_param('ss', $config['login_name'], self::passHash($config['password'])) ) {
				$create_errors |= self::CANT_CREATE_USER;
			} else if (!$stmt->execute()) {
				$create_errors |= self::CANT_CREATE_USER;
			} else if ( !($result = $stmt->get_result()) ) {
				$create_errors |= self::CANT_CREATE_USER;
			} else if ( !($data = $result->fetch_array(MYSQLI_ASSOC)) ) {
				$create_errors |= self::CANT_CREATE_USER;
			} else {
				$stmt->close();
			}
