<?php 
namespace App\Reserve;

use Seriti\Tools\Upload;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_UPLOAD_WWW;

class PackageImage extends Upload 
{
  //configure
    public function setup($param = []) 
    {
         
        $id_prefix = 'PACIMG'; 

        /*
        $file_prefix = 'WWW';
        
        $param = ['row_name'=>'Package document',
                  'pop_up'=>true,
                  'col_label'=>'file_name_orig',
                  'update_calling_page'=>true,
                  //'upload_access'=>IMAGE_CONFIG['access'],
                  'storage'=>STORAGE_WWW,
                  'upload_path_base'=>BASE_PATH,
                  'upload_path'=>BASE_UPLOAD_WWW,
                  'prefix'=>$file_prefix,//will prefix file_name if used, but file_id.ext is unique 
                  'upload_location'=>$id_prefix]; 
        parent::setup($param);
  */
        $param = ['row_name'=>'Package Image',
                  'pop_up'=>true,
                  'col_label'=>'file_name_orig',
                  'update_calling_page'=>true,
                  'upload_access'=>IMAGE_CONFIG['access'],
                  'prefix'=>$id_prefix,//will prefix file_name if used, but file_id.ext is unique 
                  'upload_location'=>$id_prefix]; 
        parent::setup($param);

        //resize parameters
        $resize = ['original'=>true,'thumb_nail'=>true,'crop'=>false,
                   'width'=>IMAGE_CONFIG['width'],'height'=>IMAGE_CONFIG['height'], 
                   'width_thumb'=>IMAGE_CONFIG['width_tn'],'height_thumb'=>IMAGE_CONFIG['height_tn']];

        //thumbnail display parameters           
        $thumbnail = ['list_view'=>true,'edit_view'=>true,
                      'list_width'=>IMAGE_CONFIG['width_tn'],'list_height'=>0,'edit_width'=>0,'edit_height'=>0];

        parent::setupImages(['resize'=>$resize,'thumbnail'=>$thumbnail]);

        //limit to web viewable images
        $this->allow_ext = array('Images'=>array('jpg','jpeg','gif','png')); 

        $param = [];
        $param['table']     = TABLE_PREFIX.'package';
        $param['key']       = 'package_id';
        $param['label']     = 'title';
        $param['child_col'] = 'location_id';
        $param['child_prefix'] = $id_prefix ;
        $param['show_sql'] = 'SELECT CONCAT("Package: ",title) FROM '.TABLE_PREFIX.'package WHERE package_id = "{KEY_VAL}"';
        $this->setupMaster($param);

        //NB: only need to add non-standard file cols here, or if you need to modify standard file col setup
        $this->addFileCol(array('id'=>'caption','type'=>'STRING','title'=>'Caption','upload'=>true,'required'=>false));
        $this->addFileCol(['id'=>$this->file_cols['location_rank'],'title'=>'Location rank','type'=>'INTEGER','list'=>true,'update'=>true,'upload'=>true]);

        $this->addSortOrder($this->file_cols['location_rank'],'Location Rank','DEFAULT');

        $this->addAction('edit');
        $this->addAction('delete');

        //$access['read_only'] = true;                         
        //$this->modifyAccess($access); p
    }
}
?>
