=====================================
FreeSWITCH's core.db I/O Bottleneck
=====================================

On a normal configuration, core.db is written to disk almost every second, generating hundreds of block-writes per second. To avoid this problem, turn /usr/local/freeswitch/db into an in-memory filesystem.

On current FreeSWITCH versions you should use the documented **"core-db-name"** parameter in **switch.conf.xml**
:: <param name="core-db-name" value="/dev/shm/core.db" />
**Save the file and restart freeswitch.**
    
