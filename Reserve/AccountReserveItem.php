<?php 
namespace App\Reserve;

use Seriti\Tools\Table;
use Seriti\Tools\Date;

class AccountReserveItem extends Table 
{
    protected $labels = MODULE_RESERVE['labels'];
    protected $table_prefix = MODULE_RESERVE['table_prefix'];

    //configure
    public function setup($param = []) 
    {
        $table_param = ['row_name'=>$this->labels['item'],'col_label'=>'item','pop_up'=>true];
        parent::setup($table_param);   

        if(isset($param['table_prefix'])) $this->table_prefix = $param['table_prefix'];     
                       
        $access['read_only'] = true;                         
        $this->modifyAccess($access);

        //NB: specify master table relationship
        $this->setupMaster(array('table'=>$this->table_prefix.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.$this->table_prefix.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        

        $this->addTableCol(array('id'=>'data_id','type'=>'INTEGER','title'=>'Data ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'item_id','type'=>'INTEGER','title'=>$this->labels['item'],'join'=>'name FROM '.$this->table_prefix.'item WHERE item_id'));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTEGER','title'=>'No. People'));
        $this->addTableCol(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','new'=>date('Y-m-d')));
        $this->addTableCol(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','new'=>date('Y-m-d')));

        $this->addSortOrder('T.date_arrive','Arrival date');
    }  
}

?>
