<?php 
namespace App\Reserve;

use Seriti\Tools\Listing;

//use Seriti\Tools\Form;
//use Seriti\Tools\Secure;
//use Seriti\Tools\Template;
//use Seriti\Tools\Image;
//use Seriti\Tools\Calc;
//use Seriti\Tools\Menu;

//use Seriti\Tools\DbInterface;
//use Seriti\Tools\IconsClassesLinks;
//use Seriti\Tools\MessageHelpers;
//use Seriti\Tools\ContainerHelpers;
//use Seriti\Tools\STORAGE;
//use Seriti\Tools\UPLOAD_DOCS;
//use Seriti\Tools\BASE_PATH;
//use Seriti\Tools\BASE_TEMPLATE;
use Seriti\Tools\BASE_URL;

use Seriti\Tools\STORAGE;
//use Seriti\Tools\BASE_UPLOAD_WWW;

use Psr\Container\ContainerInterface;

class PackageList extends Listing
{
    
    protected $table_prefix = MODULE_RESERVE['table_prefix'];

    //configure
    public function setup($param = []) 
    {
        //Class accessed outside /App/Shop so cannot use TABLE_PREFIX constant
        $labels = MODULE_RESERVE['labels'];
        $image_access = MODULE_RESERVE['images']['access'];
        
        $currency = 'R';

        $image_popup = ['show'=>true,'width'=>600,'height'=>600];
        
        $param = ['row_name'=>$labels['package'],'col_label'=>'title','show_header'=>false,'order_by'=>'sort',
                  'image_pos'=>'LEFT','image_width'=>200,'no_image_src'=>BASE_URL.'images/no_image.png',
                  'col_options'=>'options','image_popup'=>$image_popup,'format'=>'MERGE_COLS']; 
        parent::setup($param);

        $this->addListCol(array('id'=>'package_id','type'=>'INTEGER','title'=>$labels['package'].' ID','key'=>true,'key_auto'=>true,'list'=>true));
        $this->addListCol(array('id'=>'title','type'=>'STRING','title'=>'Title','class'=>'list_item_title'));
        $this->addListCol(array('id'=>'category_id','type'=>'INTEGER','title'=>$labels['package_category'],
                                'join'=>'name FROM '.$this->table_prefix.'package_category WHERE category_id'));
        //$this->addListCol(array('id'=>'body_html','type'=>'HTML','title'=>'Description','class'=>'list_item_text'));
        $this->addListCol(array('id'=>'info','type'=>'TEXT','title'=>'Description','class'=>'list_item_text'));
         
        //NB: must have to be able to search on products below category_id in tree
        $this->addSql('JOIN','JOIN '.$this->table_prefix.'package_category AS C ON(T.category_id = C.category_id)');
        //only list products with status = OK 
        $this->addSql('WHERE','T.status = "OK"');
        
        //sort by primary category sort and then package sort
        $this->addSortOrder('C.sort,T.sort,T.title ',$labels['package_category'].' then importance','DEFAULT');

        //NB: need to add a dummty action so customListAction() is called
        $this->addListAction('empty',array('type'=>'NA','text'=>'NA','icon_text'=>'NA','pos'=>'R'));
                
        //$sql = 'SELECT id,CONCAT(IF(level > 1,REPEAT("--",level - 1),""),title) FROM '.$this->table_prefix.'category  ORDER BY rank';
        $sql = 'SELECT category_id,name FROM '.$this->table_prefix.'package_category ORDER BY sort';
        $this->addSelect('category_id',$sql);
        
        $this->addSearch(array('title','category_id','info'),array('rows'=>2));

        $this->setupListImages(array('table'=>$this->table_prefix.'file','location'=>'PACIMG','max_no'=>100,'manage'=>false,
                                     'list'=>true,'list_no'=>1,'storage'=>STORAGE,'title'=>$labels['package'],'access'=>$image_access,
                                     'link_url'=>'not_used','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));
                                
        $this->setupListFiles(array('table'=>$this->table_prefix.'file','location'=>'PACDOC','max_no'=>10,'manage'=>false,
                                    'list'=>true,'list_no'=>5,'storage'=>STORAGE,
                                    'link_url'=>'package_download','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

    }

    protected function modifyRowFormatted($row_no,&$actions_left,&$actions_right,&$images,&$files,&$items)
    {
       $package_id = $items[$this->key['id']]['value'];

       $gallery_link = '<a href="javascript:open_popup(\'image_popup?id='.$package_id.'\','.$this->image_popup['width'].','.$this->image_popup['height'].')">'.
                        $this->icons['gallery'].'</a>';

       $items['title']['formatted'] .= '&nbsp;'.$gallery_link;
        
    }

    protected function customListAction($data,$row_no,$pos = 'R') 
    {
        $html = '<a class="btn btn-primary" href="/public/checkout?package='.$data['package_id'].'">Make an enquiry</a>';

        return $html;
    } 
}  

?>
