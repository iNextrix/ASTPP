-- -----------Configuration - Purge - "Invoices" spelling is wrong displayed query. Date of change : 160920 change_by : Bhargav 

UPDATE `system` SET `display_name` = 'Invoices Older Than Days' WHERE `system`.`name` = "purge_invoices";