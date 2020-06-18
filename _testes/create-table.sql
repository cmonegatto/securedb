CREATE TABLE `adm_categories` (
  `idcat` int(11) NOT NULL AUTO_INCREMENT,
  `idcia` int(11) NOT NULL,
  `category` varchar(30) NOT NULL,
  `descat` varchar(50) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcat`),
  KEY `FK_cat_cias_idx` (`idcia`),
  CONSTRAINT uc_categories UNIQUE (idcia, category),
  CONSTRAINT `fk_cat_cias` FOREIGN KEY (`idcia`) REFERENCES `adm_cias` (`idcia`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `adm_login_activity` (   
`dtregister` timestamp NOT NULL DEFAULT current_timestamp(),   
`user` varchar(50) DEFAULT NULL,  
`username` varchar(50) DEFAULT NULL,  
`ip` varchar(50) DEFAULT NULL,  
`status` varchar(200) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `db_ecommerce`.`tb_users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `deslogin` varchar(64) NOT NULL,
  `despassword` varchar(256) NOT NULL,
  `inadmin` tinyint(4) NOT NULL DEFAULT '0',
  `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iduser`),
  KEY `FK_users_persons_idx` (`idperson`),
  CONSTRAINT `fk_users_persons` FOREIGN KEY (`idperson`) 
  REFERENCES `tb_persons` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;



PRIMARY KEY (`iduser`),
KEY `FK_users_persons_idx` (`idperson`),
CONSTRAINT `fk_users_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION



PRIMARY KEY(`iduser`), 
KEY `FK_users_cias_idx` (`idcia`),
CONSTRAINT `fk_users_cias` FOREIGN KEY (`idcia`) 
REFERENCES `adm_cias` (`idcias`) ON DELETE NO ACTION ON UPDATE NO ACTION  
) 