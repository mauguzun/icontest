/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50719
Source Host           : localhost:3306
Source Database       : sslv

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2018-02-19 18:28:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of groups
-- ----------------------------
INSERT INTO `groups` VALUES ('1', 'admin', 'Administrator');
INSERT INTO `groups` VALUES ('2', 'members', 'General User');

-- ----------------------------
-- Table structure for login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of login_attempts
-- ----------------------------
INSERT INTO `login_attempts` VALUES ('5', '127.0.0.1', 'maguzun@gmail.com', '1519055687');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `about` text,
  `img` text,
  `birthday` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('7', '127.0.0.1', '$2y$08$fZp.UP5HxUN6Pq3r3wMq7OdIkC/7rALXZhF8ygwgPCE6NQdgUUM3y', null, 'matiss@iconcept.lv', 'c6dfd1d68d2a8741261ff10891873d483335aa1d', null, null, null, '1519057557', '1519057573', '1', 'Vards', 'Uzvards', 'its a test acc', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAACk0lEQVRoQ+2XjZFBMRSF8zrQASpABagAFaACVIAK0AEVoAJ0QAWoABXY+TJz30T2/Zi3G4zJncnMIje555z7kw3u9/tdfYEFHsiHqegV+TBBlFfEK+KIAZ9ajojNfKxXJDN1jhy9Io6IzXysVyQzdY4cvSJ/ITYIgtB9s9moWq32cFy5XFaHw+Hhu7T//96iSBKQ6XSqBoNBCCKfz6tcLqf2+30idx8HpNlsqvV6rYOuVqtqu90+Jf7HASHNdrudDn44HKrRaPR/QGBlNpup6/WqJS4UClruTqejGo2G/ts29o7HY73/dDop8r7dbisYj0qt+XyuFouF3o8vxj0sjFpKslRFCJYL4owAucQEQzCtVksDsG0ymTzUgBQ7zAM8zv5U7LDU7Xb12RQdl8EQjPGb5DJM81nMTA++6/V62g9lxUf2ChB+Y3HO+XwOa0Q6WlqKJSrS7/d1SsXlK5egBKrIRahQLBZDUCjAOWJmMUvKmO3XSY2YQCRYaiLJ7BS5XC6/0q5SqYRH2HPECRCkrtfrv+LmMhagAGiaCaRUKkX2/6Q54gQIAZKzKHO73SKF4GLSRwA9MwfeAoToKe7VaqUXHUmKUZBRyMfjUX80FYkbaG8DYktBQaOU2S7j2qjdNu1m8LIakccbLdQ2M58lILNls3+5XOohKGY3g5cAIWUkjXjMMS9k8NEIGHoyhc3uZPqxH3CkGYPVbMUva7/UBMGmmT0rbFVMf95PUSkZNUz/9a0F86hhT2QuhmWeMCzb7G7Hy4AU46ykFuus/ZoBUqgsmeZpSvG7vLfk8feMT5Y9qY/GLIe+w8cDeQfrSXd6RbwijhjwqeWI2MzHekUyU+fI0SviiNjMx3pFMlPnyPFrFPkBxt1lifaA9gQAAAAASUVORK5CYII=', '1990-01-01');

-- ----------------------------
-- Table structure for users_groups
-- ----------------------------
DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users_groups
-- ----------------------------
INSERT INTO `users_groups` VALUES ('8', '7', '2');
SET FOREIGN_KEY_CHECKS=1;
