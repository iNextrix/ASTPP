================
Create Trunk
================



.. image:: /Images/create_trunk.png
  
  
  
  
**Trunk Add/Edit Form Fields Description:**
  
 ============   =========================================================================================================
 Trunk name	   Trunk name
  
 Provider	     Select provider to whom this trunk belongs to
  
 Gateway	       Select gateway on which call will be terminated
  
 Failover       Select failover gateway on which call will be terminated. If primary gateway failed in 
 Gateway 
                 establishing call then system will try call using failover gateway.
                    
 Max Channels   Number of Maximum concurrent call for this trunk  
  
 Number         If you wish to translate number with some defined number for trunk then use this feature.
 Translation    
                 Ex: “011/2222” (You can define multiple translations like "011/2222","02/33")

                 That means from called number 011 is replaced by 2222.   

 Codec          Enter codecs if you want call to use specific codecs only
                    
 Priority       Priority of trunk
 
                Ex : In rate group you have selected multiple trunks and more than one selected trunks are having same prefix rates. In such case you can use priority in trunk.
                
 ============   =========================================================================================================
  
  
|image| `How to create trunk 
<https://youtu.be/xZ52dP3oEnM>`_ 

.. |image| image:: /Images/favicon.png






