<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class AccountOrderPayment extends Table 
{
    protected $table_prefix = MODULE_RESERVE['table_prefix'];

    //configure
    public function setup($param = []) 
    {
        if(isset($param['table_prefix'])) $this->table_prefix = $param['table_prefix'];
        
        $table_param = ['row_name'=>'Payment','col_label'=>'amount','pop_up'=>true];
        parent::setup($table_param);        
                       
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>$this->table_prefix.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.$this->table_prefix.'reserve WHERE reserve_id = "{KEY_VAL}" '));

        
        $access['read_only'] = true;                         
        $this->modifyAccess($access);

        $this->addTableCol(array('id'=>'payment_id','type'=>'INTEGER','title'=>'Payment ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'date_create','type'=>'DATETIME','title'=>'Date paid'));
        $this->addTableCol(array('id'=>'amount','type'=>'DECIMAL','title'=>'Amount'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        //$this->addSearch(array('notes','date'),array('rows'=>1));
    }    
}

?>
