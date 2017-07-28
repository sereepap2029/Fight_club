/*
Navicat MySQL Data Transfer

Source Server         : root
Source Server Version : 50713
Source Host           : localhost:3306
Source Database       : fight_club

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2017-07-28 17:11:19
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
INSERT INTO `business` VALUES ('526eefdf64', 'Fight Club');

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
INSERT INTO `department` VALUES ('42a4b6af67', 'บางนา', '526eefdf64');

-- ----------------------------
-- Table structure for `group_has_prem`
-- ----------------------------
DROP TABLE IF EXISTS `group_has_prem`;
CREATE TABLE `group_has_prem` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `g_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prem` enum('resource','hod','csd','cs','fc','hr','account','admin') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of group_has_prem
-- ----------------------------
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
INSERT INTO `group_has_prem` VALUES ('123', '90ce2f6d18', 'resource');

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
INSERT INTO `group_prem` VALUES ('90ce2f6d18', 'Fighter');
INSERT INTO `group_prem` VALUES ('9b360ceb6a', 'HR');
INSERT INTO `group_prem` VALUES ('b766a60fdf', 'admin');
INSERT INTO `group_prem` VALUES ('c574df262c', 'HOD and Resource');
INSERT INTO `group_prem` VALUES ('d01f1bb419', 'CSD');
INSERT INTO `group_prem` VALUES ('e46b263a8b', 'CS');

-- ----------------------------
-- Table structure for `holiday`
-- ----------------------------
DROP TABLE IF EXISTS `holiday`;
CREATE TABLE `holiday` (
  `time` bigint(11) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci,
  `is_holiday` enum('n','y') COLLATE utf8_unicode_ci DEFAULT 'n',
  PRIMARY KEY (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of holiday
-- ----------------------------
INSERT INTO `holiday` VALUES ('1451581201', 'วันหยุดปีใหม่', 'y');
INSERT INTO `holiday` VALUES ('1456074001', 'วันมาฆบูชา', 'y');
INSERT INTO `holiday` VALUES ('1459875601', 'วันจักกรี ', 'y');
INSERT INTO `holiday` VALUES ('1460480401', 'วันสงกรานต์ ', 'y');
INSERT INTO `holiday` VALUES ('1460566801', 'วันสงกรานต์ ', 'y');
INSERT INTO `holiday` VALUES ('1460653201', 'วันสงกรานต์ ', 'y');
INSERT INTO `holiday` VALUES ('1462122001', 'วันเเรงงาน', 'y');
INSERT INTO `holiday` VALUES ('1462381201', 'วันฉัตรมงคล', 'y');
INSERT INTO `holiday` VALUES ('1463677201', 'วันวิสาขบูชา', 'y');
INSERT INTO `holiday` VALUES ('1468861201', 'วันอาสาฬหบูชา', 'y');
INSERT INTO `holiday` VALUES ('1470330001', 'no comment', 'n');
INSERT INTO `holiday` VALUES ('1470934801', 'วันเเม่เเห่งชาติ', 'y');
INSERT INTO `holiday` VALUES ('1477242001', 'วันหยุดชดเชยวันปิยมหาราช', 'y');
INSERT INTO `holiday` VALUES ('1480870801', 'วันพ่อเเห่งชาติ', 'y');
INSERT INTO `holiday` VALUES ('1481475601', 'วันหยุดชดเชยวันรัฐธรรมนูญ', 'y');
INSERT INTO `holiday` VALUES ('1483290001', 'วันหยุดชดเชยปีใหม่ ', 'y');
INSERT INTO `holiday` VALUES ('1483376401', 'วันหยุดชดเชยตามประกาศ ครม ', 'y');
INSERT INTO `holiday` VALUES ('1486918801', 'วันหยุดชดเชยมาฆบูชา ', 'y');
INSERT INTO `holiday` VALUES ('1487005201', 'no comment', 'n');
INSERT INTO `holiday` VALUES ('1491411601', 'วันจักกรี', 'y');
INSERT INTO `holiday` VALUES ('1492016401', 'วันสงกรานตร์ ', 'y');
INSERT INTO `holiday` VALUES ('1492102801', 'วันสงกรานตร์ ', 'y');
INSERT INTO `holiday` VALUES ('1492362001', 'ชดเชยวันสงกรานต์ ', 'y');
INSERT INTO `holiday` VALUES ('1493571601', 'วันเเรงงานเเห่งชาติ', 'y');
INSERT INTO `holiday` VALUES ('1493917201', 'วันฉัตรมงคล', 'n');
INSERT INTO `holiday` VALUES ('1494349201', 'วันวิสาฆบูชา', 'y');
INSERT INTO `holiday` VALUES ('1499619601', 'วันหยุดชดเชยวันอาสาฬหบูชา', 'y');
INSERT INTO `holiday` VALUES ('1501174801', 'no comment', 'y');
INSERT INTO `holiday` VALUES ('1502643601', 'วันหยุดชดเชยวันเเม่เเห่งชาติ', 'y');
INSERT INTO `holiday` VALUES ('1508691601', 'วันปิยมหาราช', 'y');
INSERT INTO `holiday` VALUES ('1512406801', 'วันคล้ายวันเฉลิมพระชนมพรรษาของพระบาทสมเด็จพระปรมินมหาภูมิพล (วันพ่อเเห่งชาติ )', 'y');
INSERT INTO `holiday` VALUES ('1512925201', 'วันหยุดชดเชยวันรัฐธรรมนูญ', 'y');

-- ----------------------------
-- Table structure for `hour_rate`
-- ----------------------------
DROP TABLE IF EXISTS `hour_rate`;
CREATE TABLE `hour_rate` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hour_rate` int(11) DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `is_special` enum('n','y') COLLATE utf8_unicode_ci DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of hour_rate
-- ----------------------------
INSERT INTO `hour_rate` VALUES ('1', 'มวยไทย', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('2', 'ยูโด', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('3', 'มวยสากล', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('4', 'มวยปล้ำ', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('5', 'เทควันโด', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('6', 'คาราเต้', '0', '', 'n');
INSERT INTO `hour_rate` VALUES ('7', 'คาโปเอร่า', '0', '', 'n');

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
INSERT INTO `position` VALUES ('308c239d4a', 'Swifter (เข้าใวออกใว)', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('58f6e59dba', 'นักมวย', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('624fe44364', 'คาราเต้', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('7a764f647d', 'มวยสากล', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('ab201de2f3', 'Crusher (สายนอนสู้)', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('acf866cd5c', 'คาโปเอร่า', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('cba9c8b2b4', 'มวยปล้ำ', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('f1f87c8616', 'เทควันโด', '42a4b6af67', '', 'n');
INSERT INTO `position` VALUES ('f6fe90dcc2', 'มวยไทย', '42a4b6af67', '', 'n');

-- ----------------------------
-- Table structure for `position_has_hour_rate`
-- ----------------------------
DROP TABLE IF EXISTS `position_has_hour_rate`;
CREATE TABLE `position_has_hour_rate` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `position_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hour_rate_id` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of position_has_hour_rate
-- ----------------------------
INSERT INTO `position_has_hour_rate` VALUES ('1', '58f6e59dba', '1');
INSERT INTO `position_has_hour_rate` VALUES ('2', '58f6e59dba', '3');
INSERT INTO `position_has_hour_rate` VALUES ('3', 'ab201de2f3', '2');
INSERT INTO `position_has_hour_rate` VALUES ('4', 'ab201de2f3', '4');
INSERT INTO `position_has_hour_rate` VALUES ('5', '308c239d4a', '5');
INSERT INTO `position_has_hour_rate` VALUES ('6', '308c239d4a', '6');
INSERT INTO `position_has_hour_rate` VALUES ('7', '308c239d4a', '7');
INSERT INTO `position_has_hour_rate` VALUES ('8', 'f1f87c8616', '5');
INSERT INTO `position_has_hour_rate` VALUES ('9', '624fe44364', '6');
INSERT INTO `position_has_hour_rate` VALUES ('10', 'f6fe90dcc2', '1');
INSERT INTO `position_has_hour_rate` VALUES ('11', '7a764f647d', '3');
INSERT INTO `position_has_hour_rate` VALUES ('12', 'acf866cd5c', '7');
INSERT INTO `position_has_hour_rate` VALUES ('13', 'cba9c8b2b4', '4');

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
INSERT INTO `user` VALUES ('Atom', '14499', 'อะตอม', 'เสรีภาพ', 'คำสี', '1501235820', '0804032819', '90ce2f6d18', 'Atom_sign.jpg', null, '308c239d4a', '1501174801', '100');
INSERT INTO `user` VALUES ('sereepap2029', '12345', 'Atom', 'Atom', 'Atom', '1501236487', '0804032819', '1234567890', 'sereepap2029_sign_1501231646.PNG', 'no', '38e4ba787f', '1501174801', '100');

-- ----------------------------
-- Table structure for `user_has_hour_rate`
-- ----------------------------
DROP TABLE IF EXISTS `user_has_hour_rate`;
CREATE TABLE `user_has_hour_rate` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `usn` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hour_rate_id` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user_has_hour_rate
-- ----------------------------

-- ----------------------------
-- Table structure for `user_leave`
-- ----------------------------
DROP TABLE IF EXISTS `user_leave`;
CREATE TABLE `user_leave` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `time_day` bigint(11) DEFAULT NULL,
  `time_start` bigint(11) DEFAULT NULL,
  `time_end` bigint(11) DEFAULT NULL,
  `usn` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user_leave
-- ----------------------------
INSERT INTO `user_leave` VALUES ('1', '1483549201', '1483581601', '1483632001', 'Atom');
