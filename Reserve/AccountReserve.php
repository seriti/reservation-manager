<?php 
namespace App\Reserve;

use Seriti\Tools\Table;
use Seriti\Tools\Form;
use Seriti\Tools\STORAGE;

use App\Reserve\Helpers;

class AccountReserve extends Table 
{
    protected $labels = MODULE_RESERVE['labels'];
    protected $table_prefix = MODULE_RESERVE['table_prefix'];
    protected $user_id = 0;

    //configure
    public function setup($param = []) 
    {
        $table_param = ['row_name'=>'Reservation','col_label'=>'date_create'];
        parent::setup($table_param);
       
        if(isset($param['table_prefix'])) $this->table_prefix = $param['table_prefix'];
        if(isset($param['user_id'])) $this->user_id = $param['user_id'];

        $access['delete'] = false;                         
        $this->modifyAccess($access);

        $this->addTableCol(array('id'=>'reserve_id','type'=>'INTEGER','title'=>'Reserve ID','key'=>true,'key_auto'=>true));
        $this->addTableCol(array('id'=>'code','type'=>'STRING','title'=>'Reservation Code','edit'=>false));
        $this->addTableCol(array('id'=>'source_id','type'=>'INTEGER','title'=>'Source',
                                 'join'=>'name FROM '.$this->table_prefix.'source WHERE source_id','edit'=>false));
        $this->addTableCol(array('id'=>'location_id','type'=>'INTEGER','title'=>'Location',
                                 'join'=>'name FROM '.$this->table_prefix.'location WHERE location_id','edit'=>false));
        $this->addTableCol(array('id'=>'package_id','type'=>'INTEGER','title'=>'Package',
                                 'join'=>'title FROM '.$this->table_prefix.'package WHERE package_id','edit'=>false));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTETGER','title'=>'No. People','edit'=>false));
        $this->addTableCol(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','edit'=>false));
        $this->addTableCol(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','edit'=>false));
        
        $this->addTableCol(array('id'=>'people_notes','type'=>'TEXT','title'=>'Your custom notes','required'=>false));
        $this->addTableCol(array('id'=>'terms_accepted','type'=>'BOOLEAN','title'=>'Terms accepted','hint'=>'Please check if you accept our terms and conditions.'));
        //$this->addTableCol(array('id'=>'itinerary_notes','type'=>'TEXT','title'=>'Itinerary notes','required'=>false));
        $this->addTableCol(array('id'=>'group_leader','type'=>'STRING','title'=>'Group leader','required'=>false));
        $this->addTableCol(array('id'=>'emergency_notes','type'=>'TEXT','title'=>'Emergency contact details','required'=>false));
        $this->addTableCol(array('id'=>'status_id','type'=>'INTEGER','title'=>'Status','new'=>0,
                                 'join'=>'name FROM '.$this->table_prefix.'reserve_status WHERE status_id','edit'=>false));

        $this->addSql('WHERE','T.user_id_responsible = "'.$this->db->escapeSql($this->user_id).'" ');
        $this->addSortOrder('T.reserve_id DESC','Most recent first','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'Edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'popup','text'=>$this->labels['item'].'s','url'=>'reserve_item','mode'=>'view','width'=>600,'height'=>600)); 
        //$this->addAction(array('type'=>'popup','text'=>'Transfers','url'=>'reserve_transfer','mode'=>'view','width'=>600,'height'=>600)); 
        //NB: spacer_edit is used in edit view and set to '' as LAST action
        $this->addAction(array('type'=>'popup','text'=>'People','url'=>'reserve_people','mode'=>'view','width'=>600,'height'=>600,'spacer_edit'=>'')); 

        $this->addSearch(array('reserve_id','source_id','location_id','no_people','date_arrive','date_depart','itinerary_notes','emergency_notes','status_id'),array('rows'=>3));
        
        $this->addSelect('source_id','SELECT source_id,name FROM '.$this->table_prefix.'source WHERE status = "OK" ORDER BY sort');
        $this->addSelect('location_id','SELECT location_id,name FROM '.$this->table_prefix.'location WHERE status = "OK" ORDER BY sort');
        $this->addSelect('package_id','SELECT package_id,title FROM '.$this->table_prefix.'package WHERE status = "OK" ORDER BY sort');
        $this->addSelect('status_id','SELECT status_id,CONCAT(sort,"-",name) FROM '.$this->table_prefix.'reserve_status ORDER BY sort');

        


    }

    

    //protected function beforeUpdate($id,$context,&$data,&$error) {}
    //protected function beforeDelete($id,&$error) {}
    //protected function afterDelete($id) {} 
}
?>
