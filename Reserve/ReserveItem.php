<?php 
namespace App\Reserve;

use Seriti\Tools\Table;
use Seriti\Tools\Date;

class ReserveItem extends Table 
{
    protected $labels = MODULE_RESERVE['labels'];

    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>$this->labels['item'],'col_label'=>'item','pop_up'=>true];
        parent::setup($param);        
                       
        //if(isset($_GET['id']))

        //NB: specify master table relationship
        $this->setupMaster(array('table'=>TABLE_PREFIX.'reserve','key'=>'reserve_id','child_col'=>'reserve_id', 
                                 'show_sql'=>'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}" '));                        

        $this->addTableCol(array('id'=>'data_id','type'=>'INTEGER','title'=>'Data ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'item_id','type'=>'INTEGER','title'=>$this->labels['item'],'join'=>'name FROM '.TABLE_PREFIX.'item WHERE item_id'));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTEGER','title'=>'No. People'));
        $this->addTableCol(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','new'=>date('Y-m-d')));
        $this->addTableCol(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','new'=>date('Y-m-d')));

        $this->addSortOrder('T.date_arrive','Arrival date');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

         $this->addSelect('item_id','SELECT item_id,name FROM '.TABLE_PREFIX.'item WHERE status = "OK" ORDER BY sort');       
    }  

    protected function beforeUpdate($id,$edit_type,&$form,&$error) 
    {
        $error = '';
        
        $date_arrive = Date::getDate($form['date_arrive']);
        $date_depart = Date::getDate($form['date_depart']);
        if($date_depart[0] <= $date_arrive[0]) {
            $error .= 'Departure date cannot be before or same as arrival date.';
        }
    }

      
}

?>
