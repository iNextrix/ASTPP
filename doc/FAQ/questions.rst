=========
Questions
=========

**Two Types Of Questions:**

1.General Questions

2.Technical Questions



**General Questions**

 1. Is this solution completely open source?
     Yeah.
    
    
 2. What kind of license does it have?
     GNU AGPL3 More info : https://www.gnu.org/licenses/agpl-3.0.en.html


 3. Is there any limitation of using ASTPP?
     Nope. There is no limitation of using ASTPP. You can use it anywhere.


 4. Where ASTPP can be used?
     It can be used in small scale as well as large scale carrier setup.


 5. What do i need to setup ASTPP on my system?
     You just need to have system with above defined OS and then you will be able to setup ASTPP on that.


 6. Which OS are preferable for ASTPP?
     It is strongly recommended that ASTPP be deployed on the Linux distribution CentOS version 7.x or Debian version 8.x


 7. What is the minimum hardware requirement?
     CentOS 7.X OR Debian 8.x,
     4GB RAM (8 or 16gb is highly recommended for better performance), 
     40gb Hard Drive,
     We recommend to use high configuration hardware to get better performance.


 8. Does ASTPP work on Virtual servers?
     Yeah, It can work on Virtual servers.


 9. How many concurrent calls ASTPP can handle?
     That is purely depends on hardware which you will use. 
     More Hardware resource can give more concurrent calls.


 10. How can I contribute code or donate money to support project?
      You can simply send your code to us for review and we will include it in open source version.
      You can donate us at paypal account billing@inextrix.com 


 11. Do you offer support?
      Yeah we do offer installation, configuration, on demand support, recurring support & custom development. 
      You can check our pricing from http://astpp.inextrix.com/cart.php. For custom development, you can drop an email to 
      us at sales@inextrix.com OR use http://astpp.inextrix.com/contact.php



**Technical Questions**

  1. What databases are supported in ASTPP ?
      It supports only MySQL (PostgreSQL support will be added in future release).
   
   
  2. What payment gateways are supported in ASTPP ?
      Paypal only for now. In future we have plan to add authorize.net payment gateway.


  3. Does ASTPP support Multi-language ?
      No


  4. Does ASTPP current version support callshop?
      No. It will be included in future releases. To get more information you can contact us at sales@inextrix.com.


  5. How can I update my source from to keep it update to date?
      ASTPP provides update.sh script with its source just run that script to update your source.


  6. When I go to login page, rather WEBUI I get long list of php contents.
      Enable short_open_tag in php.ini and then restart apache.


  7. Why I am not able to register extensions after installation?
      Make sure you have configured your IP in Sip Profile and your registration request is reaching to server.


  8. How to verify if ASTPP and FreeSWITCH communicating properly?
      Default sip profile must be loaded in FreeSWITCH


  9. While login I am getting “Unable to connect to your database server using the provided settings” error.
      Please check your database credentials. ASTPP uses /var/lib/astpp/astpp-config.conf file for database connection.


  10. How can I do IP Authentication for my customers?
       Configure your customer IP under Customers -> IP Settings


  11. Does CDRs report will show all data ?
       No, CDRs report will only record of current day. If you want to see record of previous days record then you need 
       to search.














