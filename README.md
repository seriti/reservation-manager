# Reservation manager module. 

## Designed for managing complex reservations for multiple items/units in multiple locations.

Use this module to setup locations, items/units at those locations and then reservations for those items/units. 
Each reservation can have multiple item reservations, guests, and transfers. You can split reservation costs into multiple cash flows depending on your requirements.
There will be a basic publicly accessibile reservation packages management added later. Payment processing via email link is also in the pipeline.  

## Requires Seriti Slim 3 MySQL Framework skeleton

This module integrates seamlessly into [Seriti skeleton framework](https://github.com/seriti/slim3-skeleton).  
You need to first install the skeleton framework and then download the source files for the module and follow these instructions.

It is possible to use this module independantly from the seriti skeleton but you will still need the [Seriti tools library](https://github.com/seriti/tools).  
It is strongly recommended that you first install the seriti skeleton to see a working example of code use before using it within another application framework.  
That said, if you are an experienced PHP programmer you will have no problem doing this and the required code footprint is very small.  

## Requires Seriti public-website module

You will be able to setup and manage reservations without this but for the public to view packages and make payments you will need to have the **git clone https://github.com/seriti/public-website**
module installed. 

## Requires Seriti public-payment module

You will be able to setup and manage reservations but for the public to be able to use checkout wizard and process payments you will need to have the **git clone https://github.com/seriti/public-payment**
module installed. 

## Install the module

1.) Install Seriti Skeleton framework(see the framework readme for detailed instructions):   
    **composer create-project seriti/slim3-skeleton [directory-for-app]**.   
    Make sure that you have thsi working before you proceed.

2.) Download a copy of reservation-manager module source code directly from github and unzip,  
or by using **git clone https://github.com/seriti/reservation-manager** from command line.  
Once you have a local copy of module code check that it has following structure:

/Reserve/(all module implementation classes are in this folder)  
/setup_add.php  
/routes.php 
/templates/reserve/(all templates for checkout wizard and payment wizard) 

3.) Copy the **Reserve** folder and all its contents into **[directory-for-app]/app** folder.

4.) Open the routes.php file and insert the **$this->group('/reserve', function (){}** route definition block
within the existing  **$app->group('/admin', function () {}** code block contained in existing skeleton **[directory-for-app]/src/routes.php** file.
In addition you will need to either replace the entire **$app->group('/public', function () {}** code block or insert reservation specific routes within any existing code.

5.) Open the setup_app.php file and  add the module config code snippet into bottom of skeleton **[directory-for-app]/src/setup_app.php** file.  
Please check the **table_prefix** value to ensure that there will not be a clash with any existing tables in your database.

6.) Copy the contents of "templates" folder to **[directory-for-app]/templates/** folder
 
7.) Now in your browser goto URL:  

"http://localhost:8000/admin/reserve/dashboard" if you are using php built in server  
OR  
"http://www.yourdomain.com/admin/reserve/dashboard" if you have configured a domain on your server  

Now click link at bottom of page **Setup Database**: This will create all necessary database tables with table_prefix as defined above.  
Thats it, you are good to go. Check the setup page for all options and then start capturing reservations.
