CREATE TABLE `adm_users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `idcia` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(50) NOT NULL,
  `celphone` tinyint(20) DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `superuser` tinyint(1) NOT NULL DEFAULT 0,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `uc_users` (`idcia`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;



CREATE TABLE `adm_cias` (
  `idcia` int(11) NOT NULL AUTO_INCREMENT,
  `cianame` varchar(50) NOT NULL,
  `respname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `celphone` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcia`),
  UNIQUE KEY `uc_cia` (`cianame`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;



CREATE TABLE `adm_categories` (
  `idcat` int(11) NOT NULL AUTO_INCREMENT,
  `idcia` int(11) NOT NULL,
  `category` varchar(30) NOT NULL,
  `descat` varchar(50) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcat`),
  UNIQUE KEY `uc_categories` (`idcia`,`category`),
  KEY `FK_cat_cias_idx` (`idcia`),
  CONSTRAINT `fk_cat_cias` FOREIGN KEY (`idcia`) REFERENCES `adm_cias` (`idcia`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;



CREATE TABLE `adm_databases` (
  `iddb` int(11) NOT NULL AUTO_INCREMENT,
  `idcat` int(11) NOT NULL,
  `dbname` varchar(50) NOT NULL,
  `hostname` varchar(50) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `port` int(10) DEFAULT NULL,
  `player` varchar(20) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iddb`),
  UNIQUE KEY `uc_database` (`idcat`,`dbname`),
  KEY `FK_db_cat_idx` (`idcat`),
  CONSTRAINT `fk_db_cat` FOREIGN KEY (`idcat`) REFERENCES `adm_categories` (`idcat`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;



CREATE TABLE `adm_login_activity` (
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  `user` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `adm_errors` (
  `iduser` int(11) DEFAULT NULL,
  `frommodule` varchar(256) DEFAULT NULL,
  `sqltext` varchar(2000) DEFAULT NULL,
  `message` varchar(256) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-----------------------------------------------------------------------------
Tabelas de backup 
-----------------------------------------------------------------------------

CREATE TABLE ADM_LOGINS
(
  iddb              int(11),
  idcat             int(11),
  USERNAME          VARCHAR(30),
  OSUSER            VARCHAR(30),
  MACHINE           VARCHAR(64),
  BEGIN_DATE        datetime,
  ENDDATE           datetime,
  FREETOOLS         VARCHAR(200),
  SESSIONS_PER_USER int(2),
  LOG_LOGON         VARCHAR(1),
  TRACE             VARCHAR(1),
  CURSOR_SHARING    VARCHAR(1),
  INIT_PLSQL        VARCHAR(4000),
  COMMENTS          VARCHAR(4000),
  GRANTOR_US        VARCHAR(30),
  GRANTOR_OS        VARCHAR(30),
  BACKUP_DATE       timestamp DEFAULT current_timestamp(),
KEY adm_logins_idx1 (iddb, idcat)
);


CREATE TABLE ADM_LOGINS_TO_KILL_BKP
( 
  iddb        int(11),
  idcat       int(11),
  USERNAME    VARCHAR(30),
  BACKUP_DATE timestamp DEFAULT current_timestamp(),
  KEY adm_logins_idx1 (iddb, idcat)   
);


CREATE TABLE ADM_LOGINS_TOOLS_BKP
(
  iddb     int(11),
  idcat    int(11),
  PROGRAM  VARCHAR(100),
  BACKUP_DATE timestamp DEFAULT current_timestamp(),
  KEY adm_logins_idx1 (iddb, idcat)     
);


CREATE TABLE ADM_LOGINS_LOCKED_BKP
(
  iddb     int(11),
  idcat    int(11),
  DATETIME     DATE,
  USERNAME     VARCHAR(30),
  OSUSER       VARCHAR(30),
  MACHINE      VARCHAR(64),
  BACKUP_DATE timestamp DEFAULT current_timestamp(),
  KEY adm_logins_idx1 (iddb, idcat)
);

