<?php
namespace App\Reserve;

use Seriti\Tools\Form;
use Seriti\Tools\Dashboard AS DashboardTool;

class SetupDashboard extends DashboardTool
{
    protected $labels = MODULE_RESERVE['labels'];

    //configure
    public function setup($param = []) 
    {
        $this->col_count = 2;  

        $login_user = $this->getContainer('user'); 
       
                
        $this->addBlock('RESERVE',1,1,'Reservation setup');
        $this->addItem('RESERVE','Manage physical locations',['link'=>"location?mode=list"]); 
        $this->addItem('RESERVE','Manage reservation sources',['link'=>"source?mode=list"]);    
        $this->addItem('RESERVE','Manage transfer types',['link'=>"transfer_type?mode=list"]); 
        $this->addItem('RESERVE','Manage '.$this->labels['item'].' categories',['link'=>"item_category?mode=list"]);
        $this->addItem('RESERVE','Manage '.$this->labels['item'].'s',['link'=>"item?mode=list"]); 
        $this->addItem('RESERVE','Manage cash flow types',['link'=>"cash_type?mode=list"]); 
        $this->addItem('RESERVE','Manage service operators',['link'=>"service_operator?mode=list"]); 

        $this->addBlock('USER',1,2,'User setup');
        $this->addItem('USER','Extend user settings',['link'=>"user_extend?mode=list"]);
        $this->addItem('USER','Agent setup',['link'=>"agent?mode=list"]);

        if($login_user->getAccessLevel() === 'GOD') {
            $this->addBlock('SYSTEM',2,1,'System setup');
            $this->addItem('SYSTEM','Reservation Status settings',['link'=>"reserve_status?mode=list"]);
            $this->addItem('SYSTEM','Payment options',['link'=>"payment_option?mode=list"]);
            $this->addItem('SYSTEM','Payment types',['link'=>"payment_type?mode=list"]);
            $this->addItem('SYSTEM','Package categories',['link'=>"package_category?mode=list"]);
        }    
        
    }
}

?>