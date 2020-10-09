<?php 
namespace App\Reserve;

use Seriti\Tools\Table;
use Seriti\Tools\TABLE_USER;

class UserExtend extends Table 
{
        
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Setting','col_label'=>'parameter'];
        parent::setup($param);        

        $this->addTableCol(array('id'=>'extend_id','type'=>'INTEGER','title'=>'Extend ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'user_id','type'=>'INTEGER','title'=>'User ID - name: email','edit_title'=>'User','join'=>'CONCAT(user_id," - ",name,": ",email) FROM '.TABLE_USER.' WHERE user_id'));
        $this->addTableCol(array('id'=>'agent_id','type'=>'INTEGER','title'=>'Agent','join'=>'name FROM '.TABLE_PREFIX.'agent WHERE agent_id'));
        $this->addTableCol(array('id'=>'cell','type'=>'STRING','title'=>'Cellphone','required'=>false));
        $this->addTableCol(array('id'=>'tel','type'=>'STRING','title'=>'Telephone','required'=>false));
        $this->addTableCol(array('id'=>'email_alt','type'=>'EMAIL','title'=>'Email alternative','required'=>false));
        $this->addTableCol(array('id'=>'bill_address','type'=>'TEXT','title'=>'Billing address','required'=>false));
        
        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'view','text'=>'view'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));

        $this->addSearch(array('user_id','agent_id','cell','tel','email_alt','bill_address'),array('rows'=>2));

        $this->addSelect('user_id','SELECT user_id,CONCAT(zone,": ",name,", ",email) FROM '.TABLE_USER.' WHERE zone = "PUBLIC" OR zone = "AGENT" ORDER BY zone, name ');
        $this->addSelect('agent_id','SELECT agent_id,name FROM '.TABLE_PREFIX.'agent ORDER BY sort');
    }    

}
?>
