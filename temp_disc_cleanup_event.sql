USE `cloud_disc`;

SET GLOBAL event_scheduler=ON;

CREATE EVENT clean_temp_discs
ON SCHEDULE 
	EVERY 1 DAY
	STARTS CURRENT_TIMESTAMP
DO
   DELETE fd, d, f FROM files_discs fd LEFT JOIN discs d ON fd.disc_id=d.id LEFT JOIN files f ON fd.file_id=f.id WHERE d.temporary=TRUE AND d.date_created < CURRENT_TIMESTAMP - INTERVAL 30 MINUTE;