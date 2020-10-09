<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class PaymentType extends Table 
{
    
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Type','col_label'=>'name'];
        parent::setup($param);
                
        $this->addTableCol(array('id'=>'type_id','type'=>'INTEGER','title'=>'Type ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'sort','type'=>'INTEGER','title'=>'Rank','hint'=>'Number to indicate dropdown display order'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addSortOrder('T.sort','Rank order','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $status = ['OK','HIDE'];
        $this->addSelect('status',['list'=>$status,'list_assoc'=>false]);
   }
}
?>
