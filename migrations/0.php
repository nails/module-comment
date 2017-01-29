<?php

/**
 * Migration:   0
 * Started:     09/01/2015
 * Finalised:   09/01/2015
 */

namespace Nails\Database\Migration\Nailsapp\ModuleComment;

use Nails\Common\Console\Migrate\Base;

class Migration0 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}comment` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `item_id` int(11) unsigned NOT NULL,
                `item_type_id` int(11) unsigned NOT NULL,
                `parent_id` int(11) unsigned NOT NULL,
                `body` text NOT NULL,
                `is_deleted` tinyint(1) unsigned NOT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `item_type_id` (`item_type_id`),
                KEY `parent_id` (`parent_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_ibfk_1` FOREIGN KEY (`item_type_id`) REFERENCES `{{NAILS_DB_PREFIX}}comment_type` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `{{NAILS_DB_PREFIX}}comment` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}comment_flag` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `comment_id` int(11) unsigned NOT NULL,
                `reason` varchar(300) NOT NULL DEFAULT '',
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `comment_id` (`comment_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_flag_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `{{NAILS_DB_PREFIX}}comment` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_flag_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_flag_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}comment_type` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `slug` varchar(25) DEFAULT NULL,
                `label` varchar(100) NOT NULL DEFAULT '',
                `table` varchar(100) NOT NULL DEFAULT '',
                `id_column` varchar(25) NOT NULL DEFAULT 'id',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}comment_vote` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `comment_id` int(11) unsigned NOT NULL,
                `vote_type` enum('UP','DOWN') NOT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `comment_id` (`comment_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_vote_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `{{NAILS_DB_PREFIX}}comment` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_vote_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}comment_vote_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
