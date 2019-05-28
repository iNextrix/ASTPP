#!/bin/bash
if [ $4 -gt 0 ]
then
        /usr/bin/mysql -u root -pinextrix astpp -e "update routes set call_count =call_count+1 where id= $4 ";
        /usr/bin/mysql -u root -pinextrix astpp -e "update routing set call_count =call_count+1 where routes_id= $4 and trunk_id=$1";
else
        /usr/bin/mysql -u root -pinextrix astpp -e "update pricelists set call_count =call_count+1 where id= $2";
        /usr/bin/mysql -u root -pinextrix astpp -e "update routing set call_count =call_count+1 where pricelist_id= $2 and trunk_id=$1";
fi
