===================
MySQLTuner
===================

**Navigate to the /opt directory on your server and download the latest version of MySQLTuner**
::  cd /opt/
wget http://mysqltuner.pl/ -O mysqltuner.pl
**Run the script using below command.**
::  perl mysqltuner.pl
**As a result, you should get some something like this:**
::  General recommendations:
Run OPTIMIZE TABLE to defragment tables for better performance
Reduce or eliminate unclosed connections and network issues
When making adjustments, make tmp_table_size/max_heap_table_size equal
Reduce your SELECT DISTINCT queries which have no LIMIT clause
Variables to adjust:
query_cache_type (=1)
tmp_table_size (> 16M)
max_heap_table_size (> 16M)

Once you get the final report and the MySQLTuner recommendations, you can make the changes to your MySQL database server settings. Of course, be careful while making the changes since you may lose some important data from your databases if something is set in a wrong way. If you are not sure about an issue, it is best to contact a MySQL expert in order to get the right answer.
