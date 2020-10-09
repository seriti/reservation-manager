<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ReservePayment extends Table 
{
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Payment','col_label'=>'amount','pop_up'=>true];
        parent::setup($param);        
               
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>TABLE_PREFIX.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        


        $this->addTableCol(array('id'=>'payment_id','type'=>'INTEGER','title'=>'Payment ID','key'=>true,'key_auto'=>true));
        $this->addTableCol(array('id'=>'date','type'=>'DATE','title'=>'Date paid'));
        $this->addTableCol(array('id'=>'type_id','type'=>'INTEGER','title'=>'Payment type','join'=>'name FROM '.TABLE_PREFIX.'payment_type WHERE type_id'));
        $this->addTableCol(array('id'=>'amount','type'=>'DECIMAL','title'=>'Amount'));
        $this->addTableCol(array('id'=>'comment','type'=>'TEXT','title'=>'Comment','required'=>false));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addSortOrder('T.payment_id DESC','Most recent first','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $status_list = ['NEW','CONFIRMED'];
        $this->addSelect('status',['list'=>$status_list,'list_assoc'=>false]);
        $this->addSelect('type_id','SELECT type_id,name FROM '.TABLE_PREFIX.'payment_type WHERE status = "OK" ORDER BY sort');

        //$this->addSearch(array('payment_id','date','amount','status'),array('rows'=>2));
    }    
}

?>
