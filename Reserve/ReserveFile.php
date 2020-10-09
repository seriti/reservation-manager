<?php 
namespace App\Reserve;

use Seriti\Tools\Upload;
use Seriti\Tools\STORAGE;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_UPLOAD;

class ReserveFile extends Upload 
{
  
    public function setup($param = []) 
    {
        $id_prefix = 'RESDOC'; 

        $param = ['row_name'=>'Reserve document',
                  'pop_up'=>true,
                  'col_label'=>'file_name_orig',
                  'update_calling_page'=>true,
                  'prefix'=>$id_prefix,//will prefix file_name if used, but file_id.ext is unique 
                  'upload_location'=>$id_prefix]; 
        parent::setup($param);

        $param = [];
        $param['table']     = TABLE_PREFIX.'reserve';
        $param['key']       = 'reserve_id';
        $param['label']     = 'name';
        $param['child_col'] = 'location_id';
        $param['child_prefix'] = $id_prefix;
        $param['show_sql'] = 'SELECT CONCAT("Reservation ID: ",reserve_id) FROM '.TABLE_PREFIX.'reserve WHERE reserve_id = "{KEY_VAL}"';
        $this->setupMaster($param);

        $this->addAction('delete');

        //$access['read_only'] = true;                         
        //$this->modifyAccess($access);
    }
}