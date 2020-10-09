<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ReserveCash extends Table 
{
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Cash flow','col_label'=>'item','pop_up'=>true,'add_repeat'=>true];
        parent::setup($param);        
                       
        //NB: specify master table relationship
        $this->setupMaster(array('table'=>TABLE_PREFIX.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        

        //$access['read_only'] = true;                         
        //$this->modifyAccess($access);

        $this->addTableCol(array('id'=>'cash_id','type'=>'INTEGER','title'=>'Cash ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'type_id','type'=>'INTEGER','title'=>'Cash type','join'=>'name FROM '.TABLE_PREFIX.'cash_type WHERE type_id'));
        $this->addTableCol(array('id'=>'amount','type'=>'DECIMAL','title'=>'Quantity'));
        $this->addTableCol(array('id'=>'notes','type'=>'TEXT','title'=>'Notes','required'=>false));
        
        $this->addSql('JOIN','LEFT JOIN '.TABLE_PREFIX.'cash_type AS C ON(T.type_id = C.type_id)');

        $this->addSortOrder('C.sort','Cash flow type sort order','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSelect('type_id','SELECT type_id,name FROM '.TABLE_PREFIX.'cash_type WHERE status = "OK" ORDER BY sort');
    }  

    protected function afterUpdate($id,$edit_type,$form) {
        $error = '';
        $sql = 'UPDATE '.$this->table.' SET date_modify = NOW(), user_id_modify =  "'.$this->db->escapeSql($this->user_id).'" '.
               'WHERE reserve_id = "'.$this->db->escapeSql($id).'" ';
        $this->db->executeSql($sql,$error);
        if($error !== '') throw new Exception('RESERVE_UPDATE_ERROR: INVALID cash flow update');
    }     
}

?>
