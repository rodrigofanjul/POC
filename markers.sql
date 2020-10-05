/*
 Navicat Premium Data Transfer

 Source Server         : rmdigital
 Source Server Type    : MySQL
 Source Server Version : 100323
 Source Host           : rmdigital.com.ar:3306
 Source Schema         : geolocationapi

 Target Server Type    : MySQL
 Target Server Version : 100323
 File Encoding         : 65001

 Date: 04/10/2020 22:50:53
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for markers
-- ----------------------------
DROP TABLE IF EXISTS `markers`;
CREATE TABLE `markers`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lat` float(10, 6) NOT NULL,
  `lng` float(10, 6) NOT NULL,
  `accuracy` float(10, 6) NOT NULL,
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time` datetime(0) NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of markers
-- ----------------------------
INSERT INTO `markers` VALUES (1, 'Rodrigo Fanjul', -38.066875, -57.554096, 20.000000, 'home', '2020-10-04 22:32:53');
INSERT INTO `markers` VALUES (2, 'Julian', -37.999523, -57.550373, 119.000000, 'home', '2020-10-04 22:32:53');
INSERT INTO `markers` VALUES (3, 'Flor', -37.998035, -57.541965, 9431.000000, 'home', '2020-10-04 22:32:53');
INSERT INTO `markers` VALUES (4, 'Manu', -38.004745, -57.543949, 30.000000, 'home', '2020-10-04 22:32:53');
INSERT INTO `markers` VALUES (5, 'Rodrigo Fanjul', -38.061985, -57.564610, 8754.000000, 'home', '2020-10-04 22:32:53');

SET FOREIGN_KEY_CHECKS = 1;
