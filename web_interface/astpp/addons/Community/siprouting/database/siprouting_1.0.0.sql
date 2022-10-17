CREATE TABLE `sip_device_routing` (
  `id` int(11) NOT NULL,
  `sip_device_id` int(11) NOT NULL DEFAULT '0',
  `call_forwarding_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:Enable,1:Disable',
  `call_forwarding_destination` varchar(25) DEFAULT NULL,
  `on_busy_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:Enable,1:Disable',
  `on_busy_destination` varchar(25) DEFAULT NULL,
  `no_answer_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:Enable,1:Disable',
  `no_answer_destination` varchar(25) DEFAULT NULL,
  `not_register_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:Enable,1:Disable',
  `not_register_destination` varchar(25) DEFAULT NULL,
  `is_recording` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `sip_device_routing`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sip_device_routing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO sip_device_routing (`sip_device_id`) SELECT `id` FROM sip_devices;

-- start-event
DROP TRIGGER IF EXISTS `add_sip_routing`;

-- break-event

CREATE DEFINER=`astppuser`@`localhost` TRIGGER `add_sip_routing` AFTER INSERT ON `sip_devices` FOR EACH ROW BEGIN
INSERT INTO sip_device_routing (`sip_device_id`) VALUES (NEW.id);
END;

-- end-event