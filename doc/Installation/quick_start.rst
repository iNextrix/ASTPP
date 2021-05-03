================================
Quick Start 
================================

**Here are the steps to configure basic system:**

+----------------------------------------------------------------------------------+
| **[Origination Configuration]**                                                  |
+----------------------------------------------------------------------------------+
| 1. Create Rate Group. Tariff -> Rate Group                                       |
| 2. Select Trunk in Rate Group                                                    |
| 3. Add Origination Rates. Tariff -> Origination rates (Pattern example : 1, 235) |
+----------------------------------------------------------------------------------+






+----------------------------------------------------------------------------------+
| **[Termination Configuration]**                                                  |
+----------------------------------------------------------------------------------+
| 1. Add Gateway under your sip profile. Switch -> Gateways                        |
| 2. Add Provider. Global Accounts -> Customers -> Create Provider                 |
| 3. Add your trunk. Carriers -> Trunks                                            |
| 4. Add termination rates. Carriers -> Termination Rates(Pattern example : 1, 235)|
+----------------------------------------------------------------------------------+




Create new Customer or Reseller and assign your created rate group. For customer add SIP Device from View Account or Freeswitch SIP Devices.

For reseller configuration, create new reseller. Login as reseller. Add Routes. Create customers and then make calls using that customer.

Register it and make outbound calls.


Outbound call flow
^^^^^^^^^^^^^^^^^^^
.. image:: /Images/outbound_call_flow.png

Inbound call flow
^^^^^^^^^^^^^^^^^^^
.. image:: /Images/inbound_call_flow.png
   
|image| `How to ASTPP Quick Start
<https://youtu.be/mQpAptAETp8>`_ 


.. |image| image:: /Images/favicon.png
   







