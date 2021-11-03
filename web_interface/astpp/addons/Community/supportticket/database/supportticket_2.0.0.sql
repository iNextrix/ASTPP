-- -------22-April-2021
UPDATE `default_templates` SET `template` = '<p>Email Ticket ID: #TICKET_ID# had a new status <strong>#REPLY_TYPE#</strong> posted by #NAME#</p>\r\n\r\n<p>#MESSAGE#</p>\r\n\r\n<p>Feel free to re write us in case if you have any concern regarding this ticket.</p>' WHERE `name`="email_sent_support_ticket";
