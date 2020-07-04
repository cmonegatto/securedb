create table adm_users(
  `iduser` 	 int(11) NOT NULL AUTO_INCREMENT,
  `idcia` 	 int(11) NOT NULL ,  
  `name`  	 varchar(100) NOT NULL,
  `login` 	 varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` 	 varchar(50) NOT NULL,  
  `celphone` tinyint(20)  NULL DEFAULT '0',
  `status`	 tinyint(1) NOT NULL DEFAULT '0',  
  `admin`	 tinyint(1) NOT NULL DEFAULT '0',
  `superuser` tinyint(1) NOT NULL DEFAULT '0',   
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iduser`),
  CONSTRAINT uc_users UNIQUE (idcia, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table adm_cias(
  `idcia` 	 int(11) NOT NULL AUTO_INCREMENT,
  `cianame`  varchar(50) NOT NULL,
  `respname` varchar(50) DEFAULT NULL,
  `email` 	 varchar(50) DEFAULT NULL,
  `celphone` varchar(20) DEFAULT NULL,  
  `status`	 tinyint(1) NOT NULL DEFAULT '0',
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcia`),
  CONSTRAINT uc_cia UNIQUE (cianame)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `adm_databases` (
  `iddb` int(11) NOT NULL AUTO_INCREMENT,
  `idcat` int(11) NOT NULL,
  `dbname` varchar(50) NOT NULL,
  `hostname` varchar(50) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL, 
  `port`     number(10) default null,
  `player`   varchar(20) deafult null,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iddb`),
  KEY `FK_db_cat_idx` (`idcat`),
  CONSTRAINT uc_database UNIQUE (idcat, dbname),
  CONSTRAINT `fk_db_cat` FOREIGN KEY (`idcat`) REFERENCES `adm_categories` (`idcat`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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