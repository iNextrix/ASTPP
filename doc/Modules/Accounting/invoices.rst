================
Invoices  
================

This page will display list of Invoice & Receipt generated for customers and resellers. Also admin & reseller can generate manual invoice for their customer.

You can download invoice in PDF format. 


.. image:: /Images/accounting_invoices.png

**Manual Invoice** : Generate invoice button will create blank invoice (That means it will not consider any assigned services and CDR billing of account). Then admin will be able to add invoice items as per their need and submit it to customer. We consider this operation as manual invoice generation process. 

**Automatic Invoice** : System will generate automatic invoices and receipts for account from cron scripts. For Prepaid customer, system will generate receipt upon service assignment date and for Postpaid customer, it will generate invoice on defined billing date in account profile setting.

You can change invoice configuration from : http://astpp.readthedocs.io/en/v3.5/Modules/Configuration/invoice_configuration.html 

 

**Action Column In Grid**

==========================================  ========  =================================
.. image:: /Images/download_invoice.png     Download  Use to download Details.

.. image:: /Images/edit.png                 Edit 	    Use to edit invoice information

.. image:: /Images/delete.png               Delete	  Delete invoice from list
==========================================  ========  =================================
