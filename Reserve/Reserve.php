<?php 
namespace App\Reserve;

use Exception;
use Seriti\Tools\Date;
use Seriti\Tools\Table;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\STORAGE;

class Reserve extends Table 
{

    protected $labels = MODULE_RESERVE['labels'];

    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Reservation','col_label'=>'reserve_id'];
        parent::setup($param);

                        
        $this->addTableCol(array('id'=>'reserve_id','type'=>'INTEGER','title'=>'ID','key'=>true,'key_auto'=>true));
        $this->addTableCol(array('id'=>'code','type'=>'STRING','title'=>'Code','required'=>false));
        $this->addTableCol(array('id'=>'source_id','type'=>'INTEGER','title'=>'Source','join'=>'name FROM '.TABLE_PREFIX.'source WHERE source_id'));
        $this->addTableCol(array('id'=>'location_id','type'=>'INTEGER','title'=>'Location','join'=>'name FROM '.TABLE_PREFIX.'location WHERE location_id'));
        $this->addTableCol(array('id'=>'package_id','type'=>'INTEGER','title'=>'Package','join'=>'title FROM '.TABLE_PREFIX.'package WHERE package_id'));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTETGER','title'=>'No. People','new'=>2));
        $this->addTableCol(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','new'=>date('Y-m-d')));
        $this->addTableCol(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','new'=>date('Y-m-d')));
        $this->addTableCol(array('id'=>'itinerary_notes','type'=>'TEXT','title'=>'Itinerary notes','required'=>false));
        $this->addTableCol(array('id'=>'admin_notes','type'=>'TEXT','title'=>'Admin notes','required'=>false));
        $this->addTableCol(array('id'=>'group_leader','type'=>'STRING','title'=>'Group leader','required'=>false));
        $this->addTableCol(array('id'=>'emergency_notes','type'=>'TEXT','title'=>'Emergency contact details','required'=>false));
        $this->addTableCol(array('id'=>'status_id','type'=>'INTEGER','title'=>'Status','new'=>0,'join'=>'CONCAT(sort,"-",name) FROM '.TABLE_PREFIX.'reserve_status WHERE status_id'));

        $this->addSortOrder('T.reserve_id DESC','Most recent first','DEFAULT');
        $this->addSql('JOIN','LEFT JOIN '.TABLE_PREFIX.'reserve_status AS S ON(T.status_id = S.status_id)');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>$this->labels['item'].'s','url'=>'reserve_item','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Cash','url'=>'reserve_cash','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Transfers','url'=>'reserve_transfer','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Guests','url'=>'reserve_people','mode'=>'view','width'=>600,'height'=>600)); 

        $this->addSearch(array('reserve_id','source_id','location_id','no_people','date_arrive','date_depart','itinerary_notes','admin_notes','emergency_notes','status_id'),array('rows'=>3));
        $this->addSearchXtra('S.sort','Status sequence');

        $this->addSelect('source_id','SELECT source_id,name FROM '.TABLE_PREFIX.'source WHERE status = "OK" ORDER BY sort');
        $this->addSelect('location_id','SELECT location_id,name FROM '.TABLE_PREFIX.'location WHERE status = "OK" ORDER BY sort');
        $this->addSelect('package_id','SELECT package_id,title FROM '.TABLE_PREFIX.'package WHERE status = "OK" ORDER BY sort');
        $this->addSelect('status_id','SELECT status_id,CONCAT(sort,"-",name) FROM '.TABLE_PREFIX.'reserve_status ORDER BY sort');

        $this->setupFiles(array('table'=>TABLE_PREFIX.'file','location'=>'RESDOC','max_no'=>100,
                                'icon'=>'<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp;&nbsp;manage',
                                'list'=>true,'list_no'=>1,'storage'=>STORAGE,
                                'link_url'=>'reserve_file','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

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

    protected function afterUpdate($id,$edit_type,$form) {
        $error = '';
        $sql = 'UPDATE '.$this->table.' SET ';
        if($edit_type === 'INSERT') {
            $sql .= 'date_create = NOW(), user_id_create =  "'.$this->db->escapeSql($this->user_id).'" ';
            if($form['code'] === '') $sql .= ', code = "R'.$id.'" ';
        } else {
            $sql .= 'date_modify = NOW(), user_id_modify =  "'.$this->db->escapeSql($this->user_id).'" ';
        }

        $sql .= 'WHERE reserve_id = "'.$this->db->escapeSql($id).'" ';

        $this->db->executeSql($sql,$error);
        if($error !== '') throw new Exception('RESERVE_UPDATE_ERROR: INVALID user update details');
    }    
}

?>