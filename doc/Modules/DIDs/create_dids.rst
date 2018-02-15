================
Create DIDs
================

You can create new DID using below page,

.. image:: /Images/did_create.jpg


**DIDs Add/Edit Field description**


**DID Information**

===================  =============================================
**DID Enter**        unique numeric DID number

**Country**          Select country of DID

**City**             DID City

**Province/State**   DID State

**Provider**         Select provider to whom this DID belongs to
===================  =============================================  	

**DID Billing**

===========================  =================================================================
**Account**                  Select account number you wish to assign DID
**Increments**               Rate of increment to calculate call cost.

                             Example : 60 to charge every minute
                             
**Cost**                     Cost per minute
**Included Seconds**         Define seconds will be free from the call duration for each call
**Setup Fee**                One time Setup fee
**Monthly Fee**              Monthly recurring fee
**Connection Fee**           Connection fee to charge customer minimum when their call will be 
                             connected
===========================  =================================================================


**DID Setting**

===========================  =================================================================
**Call Type**                DID-LOCAL : Wish to route call to Local extension

                             PSTN : Wish to route call to PSTN Number
                             
                             OTHER : If you wish to route call to custom destination
                             
                             SIP-DID : If you wish to route call to asterisk/other pbx server and if you need to use caller id in          header content then use this call type by setting one of the sip device of astpp which is registered as trunk on asterisk/other pbx server.
                             
                             DIRECT-IP : If you want to route call on same DID/Extension number which registered on other pbx server.
                             
                             DID@IP/URL : If you want to route call on other pbx

                             Multiple Destination : DID-Local and SIP-DID call-type can send calls to multiple destination simultaneously as well as one after another.

                             Example, if Destination string is '12345,78904' than system will try to send call to both destination simultaneously, whichever receives first call will be established with it.

							 If Destination string is '12345|78904' (with pipe sign) than system will try to send the call first to 12345 and if not received there than send the call to 78904.

							 You may also use comma and pipe sign together in string as per your usage.
                             
                             

**Destination**              Set appropriate destination based on call Type.

                             Example : 
                             
                             LOCAL : 1001 (Local Extension number)

                             PSTN : 1800214018 (PSTN number)

                             OTHER : sofia/default/1234567890@192.168.1.3

                             OR sofia/gateway/gwname/1234567890
                             
                             SIP-DID : 1234567890
                             
                             DIRECT-IP : xx.xxx.xx.xxx (server ip)
                             
                             DID@IP/URL : DIDnumber@xx.xxx.xx.xx
                             
                              
**Max Channels**              Maximum allowed concurrent channels for DID calls. 0=Unlimited
    
===========================  =================================================================



|image| `How to create DID 
<https://youtu.be/60kP7QmH2A8>`_ 

.. |image| image:: /Images/favicon.png

|image| `How to use DID Global Translation 
<https://youtu.be/GnNMPYi-HRM>`_

.. |image| image:: /Images/favicon.png

























