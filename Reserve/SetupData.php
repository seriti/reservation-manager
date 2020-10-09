<?php
namespace App\Reserve;

use Seriti\Tools\SetupModuleData;

class SetupData extends SetupModuledata
{

    public function setupSql()
    {
        $this->tables = ['reserve','reserve_status','location','source','agent','service_operator','cash_type','reserve_cash','item','item_category','reserve_item','reserve_people',
                         'reserve_payment','reserve_transfer','transfer_type','package','payment_type','payment_option','file','user_extend'];

        $this->addCreateSql('reserve',
                            'CREATE TABLE `TABLE_NAME` (
                              `reserve_id` INT NOT NULL AUTO_INCREMENT,
                              `source_id` INT NOT NULL,
                              `location_id` INT NOT NULL,
                              `package_id` INT NOT NULL,
                              `code` VARCHAR(64) NOT NULL,
                              `no_people` INT NOT NULL,
                              `date_arrive` DATE NOT NULL,
                              `date_depart` DATE NOT NULL,
                              `itinerary_notes` TEXT NOT NULL,
                              `admin_notes` TEXT NOT NULL,
                              `group_leader` VARCHAR(250) NOT NULL,
                              `emergency_notes` TEXT NOT NULL,
                              `user_id_create` INT NOT NULL,
                              `user_id_responsible` INT NOT NULL,
                              `user_id_modify` INT NOT NULL,
                              `date_create` DATE NOT NULL,
                              `date_modify` DATE NOT NULL,
                              `status_id` INT NOT NULL,
                              PRIMARY KEY (`reserve_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('reserve_status',
                            'CREATE TABLE `TABLE_NAME` (
                              `status_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(64) NOT NULL,
                              `sort` INT NOT NULL,
                              `info` TEXT NOT NULL,
                              `config` TEXT NOT NULL,
                              PRIMARY KEY (`status_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('location',
                            'CREATE TABLE `TABLE_NAME` (
                              `location_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`location_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('source',
                            'CREATE TABLE `TABLE_NAME` (
                              `source_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`source_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('agent',
                            'CREATE TABLE `TABLE_NAME` (
                              `agent_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`agent_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');


        $this->addCreateSql('service_operator',
                            'CREATE TABLE `TABLE_NAME` (
                              `operator_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `location_id` INT NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`operator_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('cash_type',
                            'CREATE TABLE `TABLE_NAME` (
                              `type_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `tax_code` VARCHAR(250) NOT NULL,
                              `tax_value` DECIMAL(12,2) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`type_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('reserve_cash',
                            'CREATE TABLE `TABLE_NAME` (
                              `cash_id` INT NOT NULL AUTO_INCREMENT,
                              `reserve_id` INT NOT NULL,
                              `type_id` INT NOT NULL,
                              `amount` DECIMAL(12,2) NOT NULL,
                              `date_modify` DATE NOT NULL,
                              `user_id_modify` INT NOT NULL,
                              `notes` TEXT NOT NULL,
                              PRIMARY KEY (`cash_id`),
                              UNIQUE KEY `idx_reserve_cash1` (`reserve_id`,`type_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');
        
        $this->addCreateSql('item',
                            'CREATE TABLE `TABLE_NAME` (
                              `item_id` INT NOT NULL AUTO_INCREMENT,
                              `location_id` INT NOT NULL,
                              `name` VARCHAR(250) NOT NULL,
                              `description` TEXT NOT NULL,
                              `no_people` INT NOT NULL,
                              `category_id` INT NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`item_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('item_category',
                            'CREATE TABLE `TABLE_NAME` (
                              `category_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`category_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('reserve_item',
                            'CREATE TABLE `TABLE_NAME` (
                              `data_id` INT NOT NULL AUTO_INCREMENT,
                              `reserve_id` INT NOT NULL,
                              `item_id` INT NOT NULL,
                              `date_arrive` DATE NOT NULL,
                              `date_depart` DATE NOT NULL,
                              `no_people` INT NOT NULL,
                              PRIMARY KEY (`data_id`),
                              UNIQUE KEY `idx_reserve_item1` (`reserve_id`,`item_id`,`date_arrive`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');
       

        $this->addCreateSql('reserve_people',
                            'CREATE TABLE `TABLE_NAME` (
                              `people_id` INT NOT NULL AUTO_INCREMENT,
                              `reserve_id` INT NOT NULL,
                              `name` VARCHAR(250) NOT NULL,
                              `title` VARCHAR(64) NOT NULL,
                              `date_birth` DATE NOT NULL,
                              `sharing` TINYINT(1) NOT NULL,
                              PRIMARY KEY (`people_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('reserve_transfer',
                            'CREATE TABLE `TABLE_NAME` (
                              `transfer_id` INT NOT NULL AUTO_INCREMENT,
                              `reserve_id` INT NOT NULL,
                              `operator_id` INT NOT NULL,
                              `operator_fee` DECIMAL(12,2) NOT NULL,
                              `total_cost` DECIMAL(12,2) NOT NULL,
                              `type_id` INT NOT NULL,
                              `date` DATE NOT NULL,
                              `start_time` TIME NOT NULL,
                              `start_place` VARCHAR(250) NOT NULL,
                              `end_time` TIME NOT NULL,
                              `end_place` VARCHAR(250) NOT NULL,
                              `no_people` INT NOT NULL,
                              `notes` TEXT NOT NULL,
                              PRIMARY KEY (`transfer_id`),
                              UNIQUE KEY `idx_reserve_item1` (`reserve_id`,`operator_id`,`date`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('transfer_type',
                            'CREATE TABLE `TABLE_NAME` (
                              `type_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`type_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('package',
                            'CREATE TABLE `TABLE_NAME` (
                              `package_id` INT NOT NULL AUTO_INCREMENT,
                              `location_id` INT NOT NULL,
                              `package_code` VARCHAR(64) NOT NULL,
                              `title` VARCHAR(250) NOT NULL,
                              `body_markdown` TEXT NOT NULL,
                              `body_html` TEXT NOT NULL,
                              `info` TEXT NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`package_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('reserve_payment',
                            'CREATE TABLE `TABLE_NAME` (
                              `payment_id` INT NOT NULL AUTO_INCREMENT,
                              `reserve_id` INT NOT NULL,
                              `type_id` INT NOT NULL,
                              `date` DATETIME NOT NULL,
                              `amount` DECIMAL(12,2) NOT NULL,
                              `comment` TEXT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`payment_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('payment_type',
                            'CREATE TABLE `TABLE_NAME` (
                              `type_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`type_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('payment_option',
                            'CREATE TABLE `TABLE_NAME` (
                              `option_id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(250) NOT NULL,
                              `provider_code` varchar(64) NOT NULL,
                              `sort` INT NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`option_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('file',
                            'CREATE TABLE `TABLE_NAME` (
                              `file_id` int(10) unsigned NOT NULL,
                              `link_id` varchar(255) NOT NULL,
                              `file_name` varchar(255) NOT NULL,
                              `file_name_tn` varchar(255) NOT NULL,
                              `file_name_orig` varchar(255) NOT NULL,
                              `file_text` longtext NOT NULL,
                              `file_date` date NOT NULL DEFAULT \'0000-00-00\',
                              `file_size` int(11) NOT NULL,
                              `location_id` varchar(64) NOT NULL,
                              `location_rank` int(11) NOT NULL,
                              `encrypted` tinyint(1) NOT NULL,
                              `file_ext` varchar(16) NOT NULL,
                              `file_type` varchar(16) NOT NULL,
                              PRIMARY KEY (`file_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('user_extend',
                            'CREATE TABLE `TABLE_NAME` (
                              `extend_id` INT NOT NULL AUTO_INCREMENT,
                              `user_id` INT NOT NULL,
                              `agent_id` INT NOT NULL,
                              `cell` varchar(64) NOT NULL,
                              `tel` varchar(64) NOT NULL,
                              `email_alt` varchar(255) NOT NULL,
                              `bill_address` TEXT NOT NULL,
                              PRIMARY KEY (`extend_id`),
                              UNIQUE KEY `idx_reserve_user1` (`user_id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET=utf8');
       

        //initialisation
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXagent` (name,sort,status) VALUES("Internal","10","OK")','Created default agent');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXsource` (name,sort,status) VALUES("Default source","10","OK")','Created default reservation source');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXlocation` (name,sort,status) VALUES("Default location","10","OK")','Created default location');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXpackage` (title,package_code,sort,status) VALUES("Default package","PACK_01",10","OK")','Created default package');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXtransfer_type` (name,sort,status) VALUES("IN","10","OK"), ("OUT","20","OK")','Created default transfer types');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXitem_category` (name,sort,status) VALUES("Chalet","10","OK"), ("Tent","20","OK")','Created default item categories');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXitem` (name,category_id,location_id,no_people,sort,status) VALUES("Chalet-1",1,1,2,10,"OK"),("Chalet-2",1,1,2,20,"OK")','Created default item');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXcash_type` (name,sort,status) VALUES("Balance due","10","OK"),("Deposit due","20","OK"),("Flights","30","OK"),("Transfers","10","OK")','Created default reservation cash types');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXservice_operator` (name,location_id,sort,status) VALUES("Minibus Inc.",1,"10","OK")','Created default transport operator');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXpayment_type` (name,sort,status) VALUES("Credit Card","10","OK"),("EFT","10","OK"),("CASH","10","OK")','Created default package');
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXreserve_status` (name,sort) VALUES("Query only",0),("Wait listed",10),("Unit available, provisional reserved",20),(" All availability confirmed",30),'.
                             '("Reservation confirmed",40),("All payments complete",50),("Arrival expected",60)','Created default reservation status settings');

        $this->addInitialSql('INSERT INTO `TABLE_PREFIXpayment_option` (name,provider_code,sort,status) '.
                             'VALUES("Manual EFT with token","BANK_XXX","1","OK"),("DPO Paygate gateway","DPO_PAYGATE","2","OK")','Created default payment options');

        /*
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXprovider` (type_id,name,code,config,sort,status) '.
                             'VALUES("EFT_TOKEN","Manual payment with token reference","BANK_XXX","Your bank account details","10","OK"),("GATEWAY_FORM","DPO paygate","DPO_PAYGATE","{\"merchant_id\":\"10011072130\",\"key\":\"secret\",\"currency\":\"ZAR\",\"return_url\":\"https://yourdomain.com/public/payment/confirm/shop/dpo\",\"notify_url\":\"https://yourdomain.com/payment/notify/shop/dpo\"}","20","OK")',
                             'Created default EFT Token and DPO Paygate Payweb 3 testing options');
        */

          
        //updates use time stamp in ['YYYY-MM-DD HH:MM'] format, must be unique and sequential
        //$this->addUpdateSql('YYYY-MM-DD HH:MM','Update TABLE_PREFIX--- SET --- "X"');
    }
}


  
?>
