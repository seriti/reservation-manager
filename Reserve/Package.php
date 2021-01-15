<?php 
namespace App\Reserve;

use Seriti\Tools\Table;
use Seriti\Tools\Form;
use Seriti\Tools\Secure;
use Seriti\Tools\Validate;
use Seriti\Tools\Html;
use Seriti\Tools\BASE_UPLOAD_WWW;

class Package extends Table 
{
    public function setup($param = []) 
    {
        $parent_param = ['row_name'=>'Package','col_label'=>'title'];
        parent::setup($parent_param);
        
        $config = $this->getContainer('config');
        //'NONE' for access is default and means no user needs to be logged in
        $access = $config->get('user','access');
        $access[] = 'NONE';

        $this->info['EDIT'] = 'You can use markdown and or raw html in page text field. '.
                              'The <a href="https://www.markdownguide.org/basic-syntax" target="_blank">markdown</a> interpreter is '.
                              '<a href="http://parsedown.org" target="_blank">Parsedown</a> and this allows you to simply create many '.
                              'standard html elements like headings,lists,bold,italic,underline and also more complex layouts like tables.'.
                              'After any changes you need to click [submit] button at bottom of form to save changes. ';
        
        //widens value column
        $this->classes['col_value'] = 'col-sm-9 col-lg-10 edit_value';

        $this->addForeignKey(array('table'=>TABLE_PREFIX.'reserve','col_id'=>'location_id','message'=>'Reservation'));
        
        $this->addTableCol(array('id'=>'package_id','type'=>'INTEGER','title'=>'Package ID','key'=>true,'key_auto'=>true,'list'=>true));
        $this->addTableCol(array('id'=>'package_code','type'=>'STRING','title'=>'Package code'));
        $this->addTableCol(array('id'=>'category_id','type'=>'INTEGER','title'=>'Category',
                                 'join'=>'name FROM '.TABLE_PREFIX.'package_category WHERE category_id'));
        $this->addTableCol(array('id'=>'title','type'=>'STRING','title'=>'Title','hint'=>'This appears as package header in large font'));
        $this->addTableCol(array('id'=>'body_markdown','type'=>'TEXT','secure'=>false,'title'=>'Page text','rows'=>20,
                                 'hint'=>'Uses <a href="http://parsedown.org/tests/" target="_blank">parsedown</a> extended <a href="https://www.markdownguide.org/basic-syntax" target="_blank">markdown</a> format, or raw html',
                                 'list'=>false,'required'=>false));
        $this->addTableCol(array('id'=>'info','type'=>'TEXT','title'=>'Plain text info','required'=>false));
        $this->addTableCol(array('id'=>'sort','type'=>'INTEGER','title'=>'Rank','hint'=>'Number to indicate dropdown display order'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addSortOrder('T.sort','Package rank','DEFAULT');

        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSelect('category_id','SELECT category_id,name FROM '.TABLE_PREFIX.'package_category WHERE status = "OK" ORDER BY sort');

        $this->addSearch(array('package_id','name','text_markdown','status','info'),array('rows'=>2));

        $status = ['OK','HIDE'];
        $this->addSelect('status',['list'=>$status,'list_assoc'=>false]);
        
        /*
        $this->setupImages(array('table'=>TABLE_PREFIX.'file','location'=>'PACIMG','max_no'=>100,
                                  'icon'=>'<span class="glyphicon glyphicon-picture" aria-hidden="true"></span>&nbsp;manage',
                                  'list'=>true,'list_no'=>1,'storage'=>STORAGE_WWW,'path'=>BASE_UPLOAD_WWW,
                                  'link_url'=>'package_image','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

        $this->setupFiles(array('table'=>TABLE_PREFIX.'file','location'=>'PACDOC','max_no'=>100,
                                'icon'=>'<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp;&nbsp;manage',
                                'list'=>true,'list_no'=>1,'storage'=>STORAGE_WWW,
                                'link_url'=>'package_file','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));
        */

        $this->setupImages(array('table'=>TABLE_PREFIX.'file','location'=>'PACIMG','max_no'=>10,
                                  'icon'=>'<span class="glyphicon glyphicon-picture" aria-hidden="true"></span>&nbsp;manage',
                                  'list'=>true,'list_no'=>1,'storage'=>STORAGE,'access'=>IMAGE_CONFIG['access'],
                                  'link_url'=>'package_image','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

                                  
        $this->setupFiles(array('table'=>TABLE_PREFIX.'file','location'=>'PACDOC','max_no'=>100,
                                'icon'=>'<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp;&nbsp;manage',
                                'list'=>true,'list_no'=>1,'storage'=>STORAGE,
                                'link_url'=>'package_file','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));
        

        
    }

     
    protected function afterUpdate($id,$context,$data) 
    {
        //converts page markdown into html and save 
        $text = $data['body_markdown'];
        if($text !== '') {
            //first need to convert  [link](?page=6) to  [link](public/link_url)
            /*
            $sql = 'SELECT page_id,link_url FROM '.TABLE_PREFIX.'page ';
            $links = $this->db->readSqlList($sql);
            foreach($links as $page_id=>$link_url) {
                $search = '?page='.$page_id;
                $replace = '/'.$this->route_root_page.$link_url;
                $text = str_replace($search,$replace,$text);
            }
            */

            //now convert any markdown to html
            $html = Html::markdownToHtml($text);      
            $sql='UPDATE '.TABLE_PREFIX.'page SET body_html = "'.$this->db->escapeSql($html).'" '.
                 'WHERE package_id = "'.$this->db->escapeSql($id).'"';
            $this->db->executeSql($sql,$error_tmp);
        }

        
    }  
    
}
?>
