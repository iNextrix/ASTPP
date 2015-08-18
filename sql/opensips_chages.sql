ALTER table subscriber Add accountcode int(11) after `password`;
ALTER table subscriber Add pricelist_id int(11) after `accountcode`;