<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ReservePeople extends Table 
{
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Person','row_name_plural'=>'People','col_label'=>'item','pop_up'=>true,'add_repeat'=>true];
        parent::setup($param);        
                       
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>TABLE_PREFIX.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        

        $this->addTableCol(array('id'=>'people_id','type'=>'INTEGER','title'=>'People ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'title','type'=>'STRING','title'=>'Title','required'=>false));
        $this->addTableCol(array('id'=>'date_birth','type'=>'DATE','title'=>'Date of birth','required'=>false));
        $this->addTableCol(array('id'=>'sharing','type'=>'BOOLEAN','title'=>'Sharing'));
        
        $this->addSortOrder('T.name','Person name');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));
                
    }  

      
}

?>
