==============
Get Balance
==============

This API is used to fetch account balance.

**Request:**

http://<ASTPP_URL>/getbalance/<SIP_DEVICE_USERNAME>/

E.G:  http://127.0.0.1:8081/getbalance/2457848300/


**Parameters:**

======================= ====================================
<ASTPP_URL>	            Enter url of your astpp portal
getbalance	            Method name of API
<SIP_DEVICE_USERNAME>	  Enter SIP Device username
======================= ====================================

**Response**

If Success then :

  Balance : 24.25 USD


If error OR Invalid Username :

  Please enter proper username of SIP Account
