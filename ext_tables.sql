#
# Table structure for table 'tx_devlog_domain_model_logrun'
#
CREATE TABLE tx_devlog_domain_model_logrun (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	requestId varchar(25) DEFAULT NULL,
	start int(11) unsigned DEFAULT NULL,
	entries int(11) unsigned DEFAULT NULL,

	PRIMARY KEY (uid),
	KEY idx_parent (pid),
	KEY requestId (pid)
);

#
# Table structure for table 'tx_devlog_domain_model_logrecord'
#
CREATE TABLE tx_devlog_domain_model_logrecord (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	run int(11) unsigned DEFAULT NULL,
	pageId int(11) unsigned DEFAULT NULL,
	time int(11) unsigned DEFAULT NULL,
	message varchar(255) DEFAULT NULL,
	component varchar(50) DEFAULT NULL,
	severity smallint(2) DEFAULT NULL,
	debugData longblob,

	PRIMARY KEY (uid),
	KEY idx_parent (pid),
	KEY idx_run (run)
) ENGINE = InnoDB;
