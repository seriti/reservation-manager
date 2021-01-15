<?php 
namespace App\Reserve;

use Seriti\Tools\Table;

class PackageCategory extends Table 
{
    
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Category','row_name_plural'=>'Categories','col_label'=>'name'];
        parent::setup($param);

        $this->addForeignKey(array('table'=>TABLE_PREFIX.'package','col_id'=>'category_id','message'=>'Package'));
                
        $this->addTableCol(array('id'=>'category_id','type'=>'INTEGER','title'=>'Category ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'sort','type'=>'INTEGER','title'=>'Rank','hint'=>'Number to indicate dropdown display order'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addSortOrder('T.sort','Sort order','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $status = ['OK','HIDE'];
        $this->addSelect('status',['list'=>$status,'list_assoc'=>false]);
   }
}
?>
