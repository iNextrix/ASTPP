================
Create Account
================

Once you click on Create Customer button, it will show you page to create new account like below screenshot.
Enter appropriate information in page and click on save button to create account successfully. 

For creating resellers, admins and sub-admins, we have similar process.


.. image:: /Images/cutomer_add.png

**Create Customer Account Form Fields Details:**

====================  ================================================================================================
 Account              Also referred as User Name,Card Number or ID is typically a 10 unique digits that identify an
                      account into the system.   
                      
                      Length of account number is configurable, admin can change it from global configuration. 
 Password             The password that needs to be provided to the customer so he/she can log into portal. 
             
 Pin                  Calling Card Pin
                      Important if customer is using calling card feature. Length of pin is configurable, admin can 
                      change it from calling card configuration. 
             
FirstName             Customer First name

LastName              Customer Last name
 
Company               Customer Company name
 
Telephone             Customer Telephone number

Country               Customer Country

Timezone              Customer Timezone
 
Status                Customer account status

Max Channels          Maximum allowed concurrent channels for outbound calls. 0=Unlimited
 
Number Translation    If you wish to translate number with some defined number for specific customer then use 
                      this feature.
 
First Used            Customer account's first used date and time. It will be updated when customer will do first 
                      call from system.

Expiry Date           Customer account's expiry date. After that date, customer wouldn't be able to make new calls.

Valid Days            Valid days for customer account.                   
                     
Create SIP Device     By selecting check-box sip device is automatically created for that new user account.

Rate Group            Rate group is an essential field for billing. Without rate group customer wouldn't be able 
                      to make any calls.
                      You can create rate group by navigating to Tariff -> Rate group. 
                     
Billing Schedule      Billing schedule for invoice generation.

                   
Billing Day           If billing schedule is monthly then you will be able to define the day on which you want 
                      customer invoice should be generated.  
                       
Currency              Customer account's currency.
                      If customer currency is INR then all amounts will appear in INR in customer portal. 
                     
Account Type          Select customer account type. Prepaid OR Postpaid. 
                      For prepaid customers, system will generate receipts as soon as any charges will be applied 
                      to them. 
                      For Postpaid, system will generate invoice on defined Billing Day.

Credit Limit          Customer account's credit limit. Credit limit is only used for the postpaid account. 


Tax                   Select applicable taxes
                      You can create taxes from Configuration -> Taxes.
                       
Low Balance Alert     Define low balance amount on which you want to send notification to customer.
 

Enable Email Alerts?  system will notify for Low credit if this option is set to Yes.


Email Address          E-mail address to get Low credit notification.


====================  ================================================================================================


 
|image| `How to create customer
<https://youtu.be/YgfcuybxlXg>`_ 


.. |image| image:: /Images/favicon.png  
                                      




