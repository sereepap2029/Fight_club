/*
Navicat MySQL Data Transfer

Source Server         : root
Source Server Version : 50713
Source Host           : localhost:3306
Source Database       : fight_club

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2017-07-28 16:01:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `business`
-- ----------------------------
DROP TABLE IF EXISTS `business`;
CREATE TABLE `business` (
  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of business
-- ----------------------------
INSERT INTO `business` VALUES ('2a9a300a3e', 'VIRT');
INSERT INTO `business` VALUES ('3640c5f389', 'NeuMerlin Group');
INSERT INTO `business` VALUES ('474dbb5fe5', 'SALAD');
INSERT INTO `business` VALUES ('8350845b18', 'BRAND-One');
INSERT INTO `business` VALUES ('92e737e38c', 'NeuMerlin');

-- ----------------------------
-- Table structure for `department`
-- ----------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `business_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of department
-- ----------------------------
INSERT INTO `department` VALUES ('0ed3fce2bc', 'Financial and Administration', '3640c5f389');
INSERT INTO `department` VALUES ('3a153505b6', 'Interactive Design', '2a9a300a3e');
INSERT INTO `department` VALUES ('6793d26352', 'Design', '474dbb5fe5');
INSERT INTO `department` VALUES ('7da18ca3a4', 'Client Service', '474dbb5fe5');
INSERT INTO `department` VALUES ('7f31aa7350', 'Client Service', '2a9a300a3e');
INSERT INTO `department` VALUES ('9e51c17d18', 'Strategic Planning', '92e737e38c');
INSERT INTO `department` VALUES ('c9d51953a7', 'Content Creative', '2a9a300a3e');
INSERT INTO `department` VALUES ('d18ecad5b7', 'Creative', '92e737e38c');
INSERT INTO `department` VALUES ('d797c82a89', 'Executive Office', '3640c5f389');
INSERT INTO `department` VALUES ('f9f8c3c15a', 'Client Service', '92e737e38c');

-- ----------------------------
-- Table structure for `group_has_prem`
-- ----------------------------
DROP TABLE IF EXISTS `group_has_prem`;
CREATE TABLE `group_has_prem` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `g_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prem` enum('resource','hod','csd','cs','fc','hr','account','admin') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of group_has_prem
-- ----------------------------
INSERT INTO `group_has_prem` VALUES ('55', '90ce2f6d18', 'resource');
INSERT INTO `group_has_prem` VALUES ('63', 'b766a60fdf', 'admin');
INSERT INTO `group_has_prem` VALUES ('84', 'e46b263a8b', 'cs');
INSERT INTO `group_has_prem` VALUES ('85', '4492c86c1f', 'hod');
INSERT INTO `group_has_prem` VALUES ('86', '887f612752', 'hod');
INSERT INTO `group_has_prem` VALUES ('87', '887f612752', 'resource');
INSERT INTO `group_has_prem` VALUES ('98', '8214458c2f', 'fc');
INSERT INTO `group_has_prem` VALUES ('99', '1234567890', 'account');
INSERT INTO `group_has_prem` VALUES ('100', '1234567890', 'admin');
INSERT INTO `group_has_prem` VALUES ('101', '1234567890', 'cs');
INSERT INTO `group_has_prem` VALUES ('102', '1234567890', 'csd');
INSERT INTO `group_has_prem` VALUES ('103', '1234567890', 'fc');
INSERT INTO `group_has_prem` VALUES ('104', '1234567890', 'hod');
INSERT INTO `group_has_prem` VALUES ('105', '1234567890', 'hr');
INSERT INTO `group_has_prem` VALUES ('106', '1234567890', 'resource');
INSERT INTO `group_has_prem` VALUES ('109', '9b360ceb6a', 'hr');
INSERT INTO `group_has_prem` VALUES ('111', '19cd8f13c2', 'account');
INSERT INTO `group_has_prem` VALUES ('118', 'd01f1bb419', 'cs');
INSERT INTO `group_has_prem` VALUES ('119', 'd01f1bb419', 'csd');
INSERT INTO `group_has_prem` VALUES ('120', 'd01f1bb419', 'fc');
INSERT INTO `group_has_prem` VALUES ('121', 'c574df262c', 'hod');
INSERT INTO `group_has_prem` VALUES ('122', 'c574df262c', 'resource');

-- ----------------------------
-- Table structure for `group_prem`
-- ----------------------------
DROP TABLE IF EXISTS `group_prem`;
CREATE TABLE `group_prem` (
  `g_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `g_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of group_prem
-- ----------------------------
INSERT INTO `group_prem` VALUES ('1234567890', 'super admin');
INSERT INTO `group_prem` VALUES ('19cd8f13c2', 'Accounting');
INSERT INTO `group_prem` VALUES ('4492c86c1f', 'HOD');
INSERT INTO `group_prem` VALUES ('8214458c2f', 'FC');
INSERT INTO `group_prem` VALUES ('887f612752', 'HOD and res');
INSERT INTO `group_prem` VALUES ('90ce2f6d18', 'Resource');
INSERT INTO `group_prem` VALUES ('9b360ceb6a', 'HR');
INSERT INTO `group_prem` VALUES ('b766a60fdf', 'admin');
INSERT INTO `group_prem` VALUES ('c574df262c', 'HOD and Resource');
INSERT INTO `group_prem` VALUES ('d01f1bb419', 'CSD');
INSERT INTO `group_prem` VALUES ('e46b263a8b', 'CS');

-- ----------------------------
-- Table structure for `position`
-- ----------------------------
DROP TABLE IF EXISTS `position`;
CREATE TABLE `position` (
  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `department_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `non_productive` enum('n','y') COLLATE utf8_unicode_ci DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of position
-- ----------------------------
INSERT INTO `position` VALUES ('04fd3f07b9', 'Strategic Planner', '9e51c17d18', '', 'n');
INSERT INTO `position` VALUES ('0b1ef6b97e', 'Accounting Supervisor', '0ed3fce2bc', '', 'y');
INSERT INTO `position` VALUES ('19128f5ec2', 'Interactive Design Group Head', '3a153505b6', '', 'n');
INSERT INTO `position` VALUES ('1a6d060d22', 'Designer - Art Production', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('1d4c5f0659', 'Senior Back-End Developer - IT2', '3a153505b6', null, 'n');
INSERT INTO `position` VALUES ('1f95c05128', 'Account Executive', '7da18ca3a4', '', 'y');
INSERT INTO `position` VALUES ('1fd7b52b24', 'Account Executive', 'f9f8c3c15a', '', 'y');
INSERT INTO `position` VALUES ('2e4099b1e8', 'Art Production Manager', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('3740f98ad9', 'Creative - Copy Writer', 'd18ecad5b7', '', 'n');
INSERT INTO `position` VALUES ('38e4ba787f', 'Account Director', 'f9f8c3c15a', '', 'y');
INSERT INTO `position` VALUES ('3b0285d32c', 'Account Manager', '7f31aa7350', '', 'y');
INSERT INTO `position` VALUES ('3df6640c39', 'Business Development Manager', 'd797c82a89', '', 'y');
INSERT INTO `position` VALUES ('401fc4b0a8', 'Group Managing Director', 'd797c82a89', '', 'n');
INSERT INTO `position` VALUES ('43066bb2a1', 'Financial Director', 'd797c82a89', '', 'n');
INSERT INTO `position` VALUES ('564df0d8a6', 'Digital Content Designer', 'c9d51953a7', '', 'n');
INSERT INTO `position` VALUES ('5a853377e7', 'Senior Designer ', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('5cb6e3d8d0', 'Senior Designer - Art Production', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('6514ba55f0', 'Content Administrator ', 'c9d51953a7', '', 'n');
INSERT INTO `position` VALUES ('685aaabe2b', 'Design and Technology Director', 'd797c82a89', '', 'n');
INSERT INTO `position` VALUES ('8e100d246e', 'Maid', '0ed3fce2bc', '', 'y');
INSERT INTO `position` VALUES ('976dd91fe9', 'Dev Acc', 'd797c82a89', 'just Dev Acc', 'n');
INSERT INTO `position` VALUES ('aa625fbd16', 'Digital Content Creative', 'd18ecad5b7', '', 'n');
INSERT INTO `position` VALUES ('ac40e401ce', 'Digital Content / Interactive Designer (Special)', 'c9d51953a7', 'สร้างมาให้จอย เพราะทำ Interactive Design ได้ด้วย', 'n');
INSERT INTO `position` VALUES ('af143bde3b', 'Account Manager', '7da18ca3a4', '', 'y');
INSERT INTO `position` VALUES ('b447ca5e41', 'Senior Creative - Copy Writer', 'd18ecad5b7', '', 'n');
INSERT INTO `position` VALUES ('b55539c1c2', 'Business Director', 'd797c82a89', '', 'y');
INSERT INTO `position` VALUES ('bb44fb0241', 'Design Group Head ', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('bba9206142', 'Interactive Designer', '3a153505b6', '', 'n');
INSERT INTO `position` VALUES ('bbbbf283af', 'Account Manager', 'f9f8c3c15a', '', 'y');
INSERT INTO `position` VALUES ('bd2c4876e2', 'Back-end Developer - IT1', '3a153505b6', null, 'n');
INSERT INTO `position` VALUES ('bd70f42fe0', 'Art Director', 'd18ecad5b7', '', 'n');
INSERT INTO `position` VALUES ('c436f6a52f', 'Design Director', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('c65016c8d9', 'Executive Creative Director', 'd18ecad5b7', '', 'n');
INSERT INTO `position` VALUES ('cb81616301', 'Accounting Officer', '0ed3fce2bc', '', 'y');
INSERT INTO `position` VALUES ('ce66324d06', 'Digital Content Manager', 'c9d51953a7', '', 'n');
INSERT INTO `position` VALUES ('d08a2544cf', 'Senior Account Executive', 'f9f8c3c15a', '', 'y');
INSERT INTO `position` VALUES ('d0c8461f25', 'Account Director', '7f31aa7350', '', 'y');
INSERT INTO `position` VALUES ('d7b0c85026', 'Front-End Developer - IT1', '3a153505b6', null, 'n');
INSERT INTO `position` VALUES ('dd19b3891c', 'Visualiser', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('dda713cbb4', 'Senior Front-End Developer - IT2', '3a153505b6', null, 'n');
INSERT INTO `position` VALUES ('e2264b2805', 'Messenger', '0ed3fce2bc', '', 'y');
INSERT INTO `position` VALUES ('e2612e8354', 'Human Resource Supervisor', '0ed3fce2bc', '', 'y');
INSERT INTO `position` VALUES ('e4345cb2bd', 'Strategic Planning Manager', '9e51c17d18', '', 'n');
INSERT INTO `position` VALUES ('eb394572c6', 'Account Executive', '7f31aa7350', '', 'y');
INSERT INTO `position` VALUES ('f5ae86313c', 'Designer', '6793d26352', '', 'n');
INSERT INTO `position` VALUES ('f8bf0f7f56', 'Senior Interactive Designer', '3a153505b6', '', 'n');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nickname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_access` bigint(11) DEFAULT '0',
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `g_prem_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'no',
  `sign_filename` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `supervisor` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `join_date` bigint(11) DEFAULT NULL,
  `weight` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('sereepap2029', '12345', 'Atom', 'Atom', 'Atom', '0', '0804032819', '1234567890', 'sereepap2029_sign_1501231646.PNG', 'no', 'no', '1501174801', '100');
