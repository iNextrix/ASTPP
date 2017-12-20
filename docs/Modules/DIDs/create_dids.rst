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
**Call Type**                LOCAL : Wish to route call to Local extension

                             PSTN : Wish to route call to PSTN Number
                             
                             OTHER : If you wish to route call to custom destination

**Destination**              Set appropriate destination based on call Type.

                             Example : 
                             
                             LOCAL : 1001 (Local Extension number)

                             PSTN : 1800214018 (PSTN number)

                             OTHER : sofia/default/1234567890@192.168.1.3

                             OR

                              sofia/gateway/gwname/121423232
                              
**Max Channels**              Maximum allowed concurrent channels for DID calls. 0=Unlimited
    
===========================  =================================================================



|image| `How to create DID 
<https://youtu.be/60kP7QmH2A8>`_ 

.. |image| image:: /Images/favicon.png






























