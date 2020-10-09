<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ReserveTransfer extends Table 
{
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Transfer','col_label'=>'date','pop_up'=>true];
        parent::setup($param);        
               
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>TABLE_PREFIX.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        


        $this->addTableCol(array('id'=>'transfer_id','type'=>'INTEGER','title'=>'Transfer ID','key'=>true,'key_auto'=>true));
        $this->addTableCol(array('id'=>'date','type'=>'DATE','title'=>'Date'));
        $this->addTableCol(array('id'=>'type_id','type'=>'INTEGER','title'=>'Type','join'=>'name FROM '.TABLE_PREFIX.'transfer_type WHERE type_id'));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTEGER','title'=>'No. People'));
        $this->addTableCol(array('id'=>'operator_id','type'=>'INTEGER','title'=>'Operator','join'=>'name FROM '.TABLE_PREFIX.'service_operator WHERE operator_id'));
        $this->addTableCol(array('id'=>'operator_fee','type'=>'DECIMAL','title'=>'Operator fee'));
        $this->addTableCol(array('id'=>'total_cost','type'=>'DECIMAL','title'=>'Total cost'));
        $this->addTableCol(array('id'=>'start_place','type'=>'STRING','title'=>'Pick up place','new'=>'Place / Previous'));
        $this->addTableCol(array('id'=>'start_time','type'=>'TIME','title'=>'Pick up time','new'=>'12:00'));
        $this->addTableCol(array('id'=>'end_place','type'=>'STRING','title'=>'Drop off place','new'=>'Place / Next'));
        $this->addTableCol(array('id'=>'end_time','type'=>'TIME','title'=>'Drop off time','new'=>'12:00'));
        $this->addTableCol(array('id'=>'notes','type'=>'TEXT','title'=>'Comment','required'=>false));
        
        $this->addSortOrder('T.date ','Transfer Date','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSelect('operator_id','SELECT operator_id,name FROM '.TABLE_PREFIX.'service_operator WHERE status = "OK" ORDER BY sort');
        $this->addSelect('type_id','SELECT type_id,name FROM '.TABLE_PREFIX.'transfer_type WHERE status = "OK" ORDER BY sort');

    }    
}

?>
