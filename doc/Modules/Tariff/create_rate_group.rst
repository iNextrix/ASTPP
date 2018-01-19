==================
Create Rate Group
==================


.. image:: /Images/rate_group_add.png
	
  
 
 
  
  
**Rate Group Add/Edit Field description**  
 
 
=====================   ==========================================================================================
**Name**		Name of Rate Group

**Routing Type**	LCR - call will go with maximum prefix/code match.
 		        
			COST - call will go with least rate cost.
			
**Initial Increment**	Initial rate increment to calculate call cost in seconds.
			
			Example: Initial increment: 30 Default increment: 6
			
			This will charge the first 30 seconds in advance. After the first 30 seconds of the call, 
			increments of 6 seconds will be applied.
  
**Default Increment**	Rate of increment to calculate call cost in seconds after initial rate increment. 

                	Example : 60 to charge every minute.
                
                	This increment will be useful when increment is not defined in origination rate. 

**Markup(%)**		Additional charges will be applicable on call cost.   

                	Example : If 10% markup defined in rate group and customer made call of $1 then system 
                
                	will charge customer 10% extra on $1 and that will be $1.1. 
            
**Trunks**		Select the trunks for LCR and routing.

                	If no trunks selected then customers who are having same rate group wouldn't be 
                	able to make outbound calls.

**Status**		Select status of rate group.
=====================   ==========================================================================================


|image| `How to create rate group 
<https://youtu.be/2KfiHjEY30c>`_ 

.. |image| image:: /Images/favicon.png










