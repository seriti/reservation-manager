<?php 
namespace App\Reserve;

use Seriti\Tools\Record;
use Seriti\Tools\TABLE_USER;

class AccountProfile extends Record 
{
    protected $table_prefix = MODULE_RESERVE['table_prefix'];
    protected $user_id = 0;

    //configure
    public function setup($param = []) 
    {
        if(isset($param['table_prefix'])) $this->table_prefix = $param['table_prefix'];
        if(isset($param['user_id'])) $this->user_id = $param['user_id'];

        $sql = 'SELECT extend_id FROM '.$this->table_prefix.'user_extend '.
               'WHERE user_id = "'.$this->db->escapeSql($this->user_id).'" ';
        $extend_id = $this->db->readSqlValue($sql);  
        if($extend_id === 0) {
            $data = [];
            $data['user_id'] = $this->user_id;
            $extend_id = $this->db->insertRecord($this->table_prefix.'user_extend',$data,$error);
            if($error !== '') throw new Exception('ACCOUNT_PROFILE_ERROR: Could not extend user profile.');
        }       

        $param = ['record_name'=>'Profile','col_label'=>'name','record_id'=>$extend_id];
        parent::setup($param); 

        $access['delete'] = false;
        $access['add'] = true;
        $access['edit'] = true;                         
        $this->modifyAccess($access);       

        $this->addRecordCol(array('id'=>'extend_id','type'=>'INTEGER','title'=>'Extend ID','key'=>true,'key_auto'=>true,'view'=>false));
        $this->addRecordCol(array('id'=>'user_id','type'=>'INTEGER','title'=>'User','join'=>'CONCAT(name,": ",email) FROM '.TABLE_USER.' WHERE user_id','edit'=>false));
        $this->addRecordCol(array('id'=>'cell','type'=>'STRING','title'=>'Cellphone','required'=>true));
        $this->addRecordCol(array('id'=>'tel','type'=>'STRING','title'=>'Telephone','required'=>false));
        $this->addRecordCol(array('id'=>'email_alt','type'=>'EMAIL','title'=>'Email alternative','required'=>false));
        $this->addRecordCol(array('id'=>'bill_address','type'=>'TEXT','title'=>'Billing address','required'=>false));
        
        $this->addAction(array('type'=>'edit','text'=>'Edit profile'));
       
    }    

}
?>
