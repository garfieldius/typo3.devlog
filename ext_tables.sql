#
# Table structure for table 'tx_devlog'
#
CREATE TABLE tx_devlog (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT NULL,
	crmsec bigint(20) unsigned DEFAULT NULL,
	cruser_id int(11) unsigned DEFAULT NULL,
	severity tinyint(2) unsigned DEFAULT NULL,
	extkey varchar(40) DEFAULT NULL,
	msg text,
	location varchar(255) DEFAULT NULL,
	ip varchar(50) DEFAULT NULL,
	line int(11) unsigned DEFAULT NULL,
	data_var longblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY crdate (crdate),
	KEY crmsec (crmsec)
) ENGINE = InnoDB;
