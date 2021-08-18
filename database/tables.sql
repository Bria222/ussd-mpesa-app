/* main users table*/
CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_name varchar(100) NOT NULL,
  phone_number varchar(20) NOT NULL,
  amount int(11) NOT NULL,
  savings int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY phone_number (phone_number)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/* Airtime table*/
CREATE TABLE IF NOT EXISTS airtime (
  id int(11) NOT NULL AUTO_INCREMENT,
  airtime_tocken varchar(100) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY airtime_tocken (airtime_tocken)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;