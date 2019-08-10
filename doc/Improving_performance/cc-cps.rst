==============================
Increase CC/CPS in freeswitch
==============================

**Navigated to /usr/local/freeswitch/conf/autoload_configs/ to increase Max handles**
::  cd /usr/local/freeswitch/conf/autoload_configs/
vim switch.conf.xml
**You can increase below variable as per your need and need to restart freeswitch for applied changes.**
::  <!-- Maximum number of simultaneous DB handles open -->
<param name="max-db-handles" value="200"/>
<!-- Maximum number of seconds to wait for a new DB handle before failing -->
<param name="db-handle-timeout" value="10"/>
<!-- Max number of sessions to allow at any given time -->
<param name="max-sessions" value="10000"/>
<!--Most channels to create per second -->
<param name="sessions-per-second" value="100"/>

**Recommended ULIMIT settings.**

The following are recommended ulimit settings for FreeSWITCH when you want maximum performance. Ulimit settings you can add to initd script before do_start().
::  ulimit -c unlimited # The maximum size of core files created.
ulimit -d unlimited # The maximum size of a process's data segment.
ulimit -f unlimited # The maximum size of files created by the shell (default option)
ulimit -i unlimited # The maximum number of pending signals
ulimit -n 999999    # The maximum number of open file descriptors.
ulimit -q unlimited # The maximum POSIX message queue size
ulimit -u unlimited # The maximum number of processes available to a single user.
ulimit -v unlimited # The maximum amount of virtual memory available to the process.
ulimit -s 240       # The maximum stack size
ulimit -l unlimited # The maximum size that may be locked into memory.
ulimit -a           # All current limits are reported.
