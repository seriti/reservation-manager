<?php 
namespace App\Reserve;

use Exception;
use Seriti\Tools\Form;
use Seriti\Tools\Validate;
use Seriti\Tools\Date;
use Seriti\Tools\Secure;
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

        $system = $this->getContainer('system');
        $admin_notes = $system->getDefault('RESERVE_ADMIN','');
        $itinerary_notes = $system->getDefault('RESERVE_ITINERARY','');
        $people_notes = $system->getDefault('RESERVE_PREFERENCE','');
        $emergency_notes = $system->getDefault('RESERVE_EMERGENCY','');
                        
        $this->addTableCol(array('id'=>'reserve_id','type'=>'INTEGER','title'=>'ID','key'=>true,'key_auto'=>true));
        $this->addTableCol(array('id'=>'code','type'=>'STRING','title'=>'Reservation Code','required'=>false));
        $this->addTableCol(array('id'=>'source_id','type'=>'INTEGER','title'=>'Source','join'=>'name FROM '.TABLE_PREFIX.'source WHERE source_id'));
        $this->addTableCol(array('id'=>'location_id','type'=>'INTEGER','title'=>'Location','join'=>'name FROM '.TABLE_PREFIX.'location WHERE location_id'));
        $this->addTableCol(array('id'=>'package_id','type'=>'INTEGER','title'=>'Package','join'=>'title FROM '.TABLE_PREFIX.'package WHERE package_id'));
        $this->addTableCol(array('id'=>'no_people','type'=>'INTETGER','title'=>'No. People','new'=>2));
        $this->addTableCol(array('id'=>'user_id_responsible','type'=>'CUSTOM','title'=>'Responsible user','edit_title'=>'Responsible user ID'));
        $this->addTableCol(array('id'=>'terms_accepted','type'=>'BOOLEAN','title'=>'Terms accepted','edit'=>false));
        $this->addTableCol(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','new'=>date('Y-m-d')));
        $this->addTableCol(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','new'=>date('Y-m-d')));

        $this->addTableCol(array('id'=>'itinerary_notes','type'=>'TEXT','title'=>'Itinerary notes','required'=>false,'new'=>$itinerary_notes));
        $this->addTableCol(array('id'=>'admin_notes','type'=>'TEXT','title'=>'Admin notes','required'=>false,'new'=>$admin_notes));
        $this->addTableCol(array('id'=>'group_leader','type'=>'STRING','title'=>'Group leader','required'=>false));
        $this->addTableCol(array('id'=>'emergency_notes','type'=>'TEXT','title'=>'Emergency contact details','required'=>false,'new'=>$emergency_notes));
        $this->addTableCol(array('id'=>'people_notes','type'=>'TEXT','title'=>'Responsible user notes','required'=>false,'new'=>$people_notes));

        $this->addTableCol(array('id'=>'status_id','type'=>'INTEGER','title'=>'Status','new'=>0,'join'=>'CONCAT(sort,"-",name) FROM '.TABLE_PREFIX.'reserve_status WHERE status_id'));

        $this->addSortOrder('T.reserve_id DESC','Most recent first','DEFAULT');
        $this->addSql('JOIN','LEFT JOIN '.TABLE_PREFIX.'reserve_status AS S ON(T.status_id = S.status_id)');

        $this->addAction(['type'=>'check_box','text'=>'']);
        $this->addAction(array('type'=>'edit','text'=>'Edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'Delete','icon_text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>$this->labels['item'].'s','url'=>'reserve_item','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Cash','url'=>'reserve_cash','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Transfers','url'=>'reserve_transfer','mode'=>'view','width'=>600,'height'=>600)); 
        //NB: spacer_edit is used in edit view and set to '' as LAST action
        $this->addAction(array('type'=>'popup','text'=>'Guests','url'=>'reserve_people','mode'=>'view','width'=>600,'height'=>600,'spacer_edit'=>'')); 

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

    protected function customEditValue($col_id,$value,$edit_type,$form) 
    {
        $html = '';
        $param['class'] = $this->classes['edit'];

        if($col_id === 'user_id_responsible') {
            if($value == 0) {
                
                $html .= 'Either enter existing User ID(leave as 0 to ignore):<br/>'; 
                $html .= Form::textInput('user_id_responsible',$value,$param).'<br/>'; 
                $html .= 'Or enter existing/create User email:<br/>'; 
                $html .= Form::textInput('responsible_email',$form['responsible_email'],$param).'<br/>'; 
                $html .= 'Create User name(If required):<br/>'; 
                $html .= Form::textInput('responsible_name',$form['responsible_name'],$param).'<br/>'; 
            } else {
                $html .= 'Enter Valid User ID(0 to reset):<br/>'; 
                $html .= Form::textInput('user_id_responsible',$value,$param).'<br/>'; 
            }
        }

        return $html;
    }

    /*
    protected function viewXXEditXtra($id,$form,$edit_type) 
    {
        $html = '';

        if($edit_type === 'INSERT' or $form['user_id_responsible'] == 0) {
            $param['class'] = $this->classes['edit'];
            
            $html .= '<div class="row"><div class="'.$this->classes['col_label'].'">Responsible user name:</div>'.
                     '<div class="'.$this->classes['col_value'].'"><i>()</i>';
            $html .= Form::textInput('responsible_name',$responsible_name,$param); 
            $html .= '</div></div>';
            $html .= '<div class="row"><div class="'.$this->classes['col_label'].'">Responsible user email:</div>'.
                     '<div class="'.$this->classes['col_value'].'">';
            $html .= Form::textInput('responsible_email',$responsible_email,$param); 
            $html .= '</div></div>';
        }

        return $html;
    }
    */

    protected function modifyRowValue($col_id,$data,&$value) {
        if($col_id === 'user_id_responsible') {
            if($value == 0) {
                $value = 'No user assigned.';
            } else {
                $user = $this->container->user->getUser('ID',$data['user_id_responsible']);
                $value = $data['user_id_responsible'].':'.$user['name'];
            }
        }  
        
    } 

    protected function beforeUpdate($id,$edit_type,&$data,&$error) 
    {
        $error = '';
        $error_tmp = '';
        
        //check responsible user and create if necessarty
        if($data['user_id_responsible'] == 0) {
            if(isset($_POST['responsible_email'])) {
                Validate::email('Responsible user email',$_POST['responsible_email'],$error_tmp);
                if($error_tmp !== '') {
                    $this->addError($error_tmp);
                } else {
                    $user = $this->container->user->getUser('EMAIL',$_POST['responsible_email']);
                    if($user != 0) {
                        //assign correct user_id
                        $data['user_id_responsible'] = $user['user_id'];
                    } else {
                        $password = Form::createPassword();
                        $access = 'USER';
                        $zone = 'PUBLIC';
                        $status = 'NEW';
                        $email = $_POST['responsible_email'];
                        $name = Secure::clean('string',$_POST['responsible_name']);
                        if($name === '') $name = array_shift(explode('@',$email));

                        //create new user
                        $this->container->user->createUser($name,$email,$password,$access,$zone,$status,$error_tmp);
                        if($error_tmp !== '') {
                            $this->addError($error_tmp);
                        } else {
                            $user = $this->container->user->getUser('EMAIL',$email);
                            $data['user_id_responsible'] = $user['user_id'];
                        }
                    }
                }
            }
        } else {
            $user = $this->container->user->getUser('ID',$data['user_id_responsible']);
            if($user == 0) $this->adderror('Responsible User ID['.$data['user_id_responsible'].'] Invalid.');
        }

        //validate date sequence
        $date_arrive = Date::getDate($data['date_arrive']);
        $date_depart = Date::getDate($data['date_depart']);
        if($date_depart[0] <= $date_arrive[0]) {
            $error .= 'Departure date cannot be before or same as arrival date.';
        }
    }

    protected function afterUpdate($id,$edit_type,$data) {
        $error = '';
        $sql = 'UPDATE '.$this->table.' SET ';
        if($edit_type === 'INSERT') {
            $sql .= 'date_create = NOW(), user_id_create =  "'.$this->db->escapeSql($this->user_id).'" ';
            if($data['code'] === '') $sql .= ', code = "R'.$id.'" ';
        } else {
            $sql .= 'date_modify = NOW(), user_id_modify =  "'.$this->db->escapeSql($this->user_id).'" ';
        }

        $sql .= 'WHERE reserve_id = "'.$this->db->escapeSql($id).'" ';

        $this->db->executeSql($sql,$error);
        if($error !== '') throw new Exception('RESERVE_UPDATE_ERROR: INVALID user update details');
    }  

    protected function viewTableActions() {
        $html = '';
        $list = array();
            
        $status_set = 'NEW';
        $date_set = date('Y-m-d');
        
        if(!$this->access['read_only']) {
            $list['SELECT'] = 'Action for selected '.$this->row_name_plural;
            $list['STATUS_CHANGE'] = 'Change order Status.';
            $list['EMAIL_LOGIN'] = 'Email Responsible user login';
        }  
        
        if(count($list) != 0){
            $html .= '<span style="padding:8px;"><input type="checkbox" id="checkbox_all"></span> ';
            $param['class'] = 'form-control input-medium input-inline';
            $param['onchange'] = 'javascript:change_table_action()';
            $action_id = '';
            $status_change = 'NONE';
            $email_comment = '';
            
            $html .= Form::arrayList($list,'table_action',$action_id,true,$param);
            
            //javascript to show collection list depending on selecetion      
            $html .= '<script type="text/javascript">'.
                     '$("#checkbox_all").click(function () {$(".checkbox_action").prop(\'checked\', $(this).prop(\'checked\'));});'.
                     'function change_table_action() {'.
                     'var table_action = document.getElementById(\'table_action\');'.
                     'var action = table_action.options[table_action.selectedIndex].value; '.
                     'var status_select = document.getElementById(\'status_select\');'.
                     'var email_comment = document.getElementById(\'email_comment\');'.
                     'status_select.style.display = \'none\'; '.
                     'email_comment.style.display = \'none\'; '.
                     'if(action==\'STATUS_CHANGE\') status_select.style.display = \'inline\';'.
                     'if(action==\'EMAIL_LOGIN\') email_comment.style.display = \'inline\';'.
                     '}'.
                     '</script>';
            
            $param = array();
            $param['class'] = 'form-control input-small input-inline';
            $sql = 'SELECT status_id,CONCAT(sort,"-",name) FROM '.TABLE_PREFIX.'reserve_status ORDER BY sort';
            $html .= '<span id="status_select" style="display:none"> status&raquo;'.
                     Form::sqlList($sql,$this->db,'status_change',$status_change,$param).
                     '</span>'; 
            
            $param['class'] = 'form-control input-medium input-inline';       
            $html .= '<span id="email_comment" style="display:none"> add Comment&raquo;'.
                     Form::textInput('email_comment',$email_comment,$param).
                     '</span>';
                    
            $html .= '&nbsp;<input type="submit" name="action_submit" value="Apply action to selected '.
                     $this->row_name_plural.'" class="btn btn-primary">';
        }  
        
        return $html; 
    }
  
    //update multiple records based on selected action
    protected function updateTable() {
        $error_str = '';
        $error_tmp = '';
        $message_str = '';
        $audit_str = '';
        $audit_count = 0;
        $html = '';
            
        $action = Secure::clean('basic',$_POST['table_action']);
        if($action === 'SELECT') {
            $this->addError('You have not selected any action to perform on '.$this->row_name_plural.'!');
        } else {
            if($action === 'STATUS_CHANGE') {
                $status_change = Secure::clean('integer',$_POST['status_change']);
                $audit_str = 'Status change['.$status_change.'] ';
                if($status_change === 'NONE') $this->addError('You have not selected a valid status['.$status_change.']!');
            }
            
            if($action === 'EMAIL_LOGIN') {
                $email_comment = Secure::clean('email',$_POST['email_comment']);
                Validate::text('email_comment',0,250,$email_comment,$error_str);
                $audit_str = 'Email login ';
                if($error_str != '') $this->addError('INVAID email comment['.$email_comment.']!');
            }
            
            if(!$this->errors_found) {     
                foreach($_POST as $key => $value) {
                    if(substr($key,0,8) === 'checked_') {
                        $reserve_id = substr($key,8);
                        $audit_str .= 'Reserve ID['.$reserve_id.'] ';
                                            
                        if($action === 'STATUS_CHANGE') {
                            $sql = 'UPDATE '.$this->table.' SET status_id = "'.$this->db->escapeSql($status_change).'" '.
                                   'WHERE reserve_id = "'.$this->db->escapeSql($reserve_id).'" ';
                            $this->db->executeSql($sql,$error_tmp);
                            if($error_tmp === '') {
                                $message_str = 'Status set['.$status_change.'] for Reserve ID['.$reserve_id.'] ';
                                $audit_str .= ' success!';
                                $audit_count++;
                                
                                $this->addMessage($message_str);                
                            } else {
                                $this->addError('Could not update status for Reserve ID['.$reserve_id.']: '.$error_tmp);                
                            }  
                        }
                        
                        if($action === 'EMAIL_LOGIN') {
                            $param = ['login_link'=>true];
                            $subject = '';
                            $message = $email_comment;
                            Helpers::sendReserveMessage($this->db,TABLE_PREFIX,$this->container,$reserve_id,$subject,$message,$param,$error_tmp);
                            if($error_tmp === '') {
                                $audit_str .= ' success!';
                                $audit_count++;
                                $this->addMessage('Reserve ID['.$reserve_id.'] sent to responsible user');      
                            } else {
                                $error = 'Cound not send reserve['.$reserve_id.'] to responsible user!';
                                if($this->debug) $error .= $error_tmp;
                                $this->addError($error);
                            }   
                        }  
                    }   
                }  
              
            }  
        }  
        
        //audit any updates except for deletes as these are already audited 
        if($audit_count != 0 and $action != 'DELETE') {
            $audit_action = $action.'_'.strtoupper($this->table);
            Audit::action($this->db,$this->user_id,$audit_action,$audit_str);
        }  
            
        $this->mode = 'list';
        $html .= $this->viewTable();
            
        return $html;
    }  
}

?>