<?php
// sql to create table
$table_channel = "
CREATE TABLE IF NOT EXISTS `service_livetv_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL COMMENT 'UUID identifier',
  `source_id` int(11) NOT NULL COMMENT 'Metadata provider id',
  `short_name` varchar(30) NOT NULL COMMENT 'Short name for the channel',
  `full_name` varchar(128) NOT NULL COMMENT 'Full name for the channel',
  `time_zone` varchar(30) NOT NULL,
  `primary_language` varchar(2) DEFAULT NULL COMMENT 'Two character description for the channel',
  `weight` int(4) DEFAULT '0' COMMENT 'Listing weight for the channel',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_name` (`short_name`),
  UNIQUE KEY `uuid-unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

// `duration` type changed to time
// [original] 
// `duration` int(11) unsigned DEFAULT NULL COMMENT 'Program duration',

$table_program = "
CREATE TABLE IF NOT EXISTS `service_livetv_program` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_program_id` bigint(20) unsigned NOT NULL COMMENT 'Metadata provider program id',
  `show_type` enum('movie','series','other') NOT NULL COMMENT 'Program show type',
  `long_title` varchar(255) NOT NULL COMMENT 'Program long title',
  `grid_title` varchar(15) DEFAULT NULL COMMENT 'Program grid title',
  `original_title` varchar(255) DEFAULT NULL COMMENT 'Program original title',  
  `duration` time NULL DEFAULT NULL COMMENT 'Program duration',
  `iso_2_lang` varchar(2) DEFAULT NULL COMMENT 'Program language',
  `eidr_id` varchar(50) DEFAULT NULL COMMENT 'Program Entertainment Identifier Registry',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indx_ext_program_id` (`ext_program_id`),
  FULLTEXT KEY `indx_long_title` (`long_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

// `run_time` type changed to time
// `start_time` and `end_time` type changed to timestamp
// [original]
// `run_time` int(11) unsigned DEFAULT NULL COMMENT 'Schedule duration/run time',
// `start_time` int(11) unsigned NOT NULL COMMENT 'Schedule start time',
// `end_time` int(11) unsigned NOT NULL COMMENT 'Schedule end time',

$table_schedule = "
CREATE TABLE IF NOT EXISTS `service_livetv_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_schedule_id` bigint(20) unsigned NOT NULL COMMENT 'Metadata provider schedule id',
  `channel_id` int(11) NOT NULL COMMENT 'Channel source/channel id',
  `start_time` timestamp NOT NULL COMMENT 'Schedule start time',
  `end_time` timestamp NOT NULL COMMENT 'Schedule end time',
  `run_time` time NULL DEFAULT NULL COMMENT 'Schedule duration/run time',
  `program_id` int(11) NOT NULL COMMENT 'Schedule program id',
  `is_live` tinyint(1) DEFAULT NULL COMMENT 'Is schedule a live broadcast',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_ext_schedule_id` (`ext_schedule_id`),
  UNIQUE KEY `index_channel_schedule` (`channel_id`,`start_time`,`end_time`),
  KEY `channel_id` (`channel_id`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `fk_service_livetv_schedule_channel_id` FOREIGN KEY (`channel_id`) REFERENCES `service_livetv_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_livetv_schedule_program_id` FOREIGN KEY (`program_id`) REFERENCES `service_livetv_program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$tables = [$table_channel, $table_program, $table_schedule];

foreach($tables as $item => $sql){
    $query = $mysqli->query($sql);
    
    if($query) {
        //echo "<br>Table " . $item . " created successfully";
    } else {
        echo "<br>Error creating table " . $item . ": " . $mysqli->error;
    }

}

?>