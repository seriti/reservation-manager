<?php 
namespace App\Reserve;

use Seriti\Tools\Upload;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_UPLOAD_WWW;

class PackageFile extends Upload 
{
  //configure
    public function setup($param = []) 
    {
        $file_prefix = 'WWW'; 
        $id_prefix = 'PACDOC'; //WebsitePageFile

        $param = ['row_name'=>'Package document',
                  'pop_up'=>true,
                  'col_label'=>'file_name_orig',
                  'update_calling_page'=>true,
                  'storage'=>STORAGE_WWW,
                  'upload_path_base'=>BASE_PATH,
                  'upload_path'=>BASE_UPLOAD_WWW,
                  'prefix'=>$file_prefix,//will prefix file_name if used, but file_id.ext is unique 
                  'upload_location'=>$id_prefix]; 
        parent::setup($param);

        $param = [];
        $param['table']     = TABLE_PREFIX.'package';
        $param['key']       = 'package_id';
        $param['label']     = 'title';
        $param['child_col'] = 'location_id';
        $param['child_prefix'] = $id_prefix ;
        $param['show_sql'] = 'SELECT CONCAT("Package: ",title) FROM '.TABLE_PREFIX.'package WHERE package_id = "{KEY_VAL}"';
        $this->setupMaster($param);

        $this->addFileCol(['id'=>$this->file_cols['location_rank'],'title'=>'Location rank','type'=>'INTEGER','list'=>true,'update'=>true,'upload'=>true]);

        $this->addSortOrder($this->file_cols['location_rank'],'Location Rank','DEFAULT');

        $this->addAction('edit');
        $this->addAction('delete');

        //$access['read_only'] = true;                         
        //$this->modifyAccess($access); p
    }
}
?>
