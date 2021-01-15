<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class AccountReservePeople extends Table 
{
    protected $table_prefix = MODULE_RESERVE['table_prefix'];

    //configure
    public function setup($param = []) 
    {
        if(isset($param['table_prefix'])) $this->table_prefix = $param['table_prefix'];

        $table_param = ['row_name'=>'Person','row_name_plural'=>'People','col_label'=>'item','pop_up'=>true,'add_repeat'=>false];
        parent::setup($table_param);        
                       
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>$this->table_prefix.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.$this->table_prefix.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        

        $this->addTableCol(array('id'=>'people_id','type'=>'INTEGER','title'=>'People ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'title','type'=>'STRING','title'=>'Title','required'=>false));
        $this->addTableCol(array('id'=>'date_birth','type'=>'DATE','title'=>'Date of birth','required'=>false));
        $this->addTableCol(array('id'=>'sharing','type'=>'BOOLEAN','title'=>'Sharing'));
        
        $this->addAction(array('type'=>'edit','text'=>'Edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'Delete','icon_text'=>'delete','pos'=>'R'));
        
        $this->addSortOrder('T.name','Person name');
    } 
    
}

?>
