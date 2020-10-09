<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class ReserveStatus extends Table 
{
    
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Status','col_label'=>'name'];
        parent::setup($param);
                
        $this->addTableCol(array('id'=>'status_id','type'=>'INTEGER','title'=>'Location ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'sort','type'=>'INTEGER','title'=>'Rank','hint'=>'Number to indicate PROCESS FLOW & dropdown display order'));
        $this->addTableCol(array('id'=>'info','type'=>'TEXT','title'=>'Info','required'=>false));
        //$this->addTableCol(array('id'=>'config','type'=>'TEXT','title'=>'Config'));

        $this->addSortOrder('T.sort','Process flow order','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));
    }
}
?>
