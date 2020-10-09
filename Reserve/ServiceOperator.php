<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ServiceOperator extends Table 
{
    
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Operator','col_label'=>'name'];
        parent::setup($param);
                
        $this->addTableCol(array('id'=>'operator_id','type'=>'INTEGER','title'=>'Operator ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'location_id','type'=>'INTEGER','title'=>'Location','join'=>'name FROM '.TABLE_PREFIX.'location WHERE location_id'));
        $this->addTableCol(array('id'=>'sort','type'=>'INTEGER','title'=>'Rank','hint'=>'Number to indicate dropdown display order'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addSortOrder('T.sort','Rank order','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $status = ['OK','HIDE'];
        $this->addSelect('status',['list'=>$status,'list_assoc'=>false]);
        $this->addSelect('location_id','SELECT location_id,name FROM '.TABLE_PREFIX.'location WHERE status = "OK" ORDER BY sort');
   }
}
?>
