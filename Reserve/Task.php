<?php
namespace App\Reserve;

use Seriti\Tools\Form;
use Seriti\Tools\Task as SeritiTask;

class Task extends SeritiTask
{
    protected $labels = MODULE_RESERVE['labels'];

    //configure
    public function setup($param = []) 
    {
        $this->col_count = 2;  

        $login_user = $this->getContainer('user'); 
        
        $this->addBlock('SETUP',1,1,'Setup data');
        $this->addTask('SETUP','LOCATION','Manage physical locations'); 
        
        if($login_user->getAccessLevel() === 'GOD') {
            $this->addBlock('SYSTEM',2,1,'System setup');
            $this->addTask('SYSTEM','USER_CLEAR','Remove orphaned user settings');
        }    
        
    }

    public function processTask($id,$param = []) {
        $error = '';
        $error_tmp = '';
        $message = '';
        $n = 0;
        
        if($id === 'LOCATION') {
            $location = 'location';
            header('location: '.$location);
            exit;
        }
        
        if($id === 'USER_CLEAR') {
            if(!isset($param['process'])) $param['process'] = false;  
                    
            if($param['process'] === 'clear') {
                $recs = Helpers::cleanUserData($this->db,$error_tmp);
                if($error_tmp === '') {
                    $this->addMessage('SUCCESSFULY removed '.$recs.' orphaned user setting records!');
                } else {
                    $error = 'Could not remove orphaned user data';
                    if($this->debug) $error .= ': '.$error_tmp;
                    $this->addError($error);   
                }     
            } else {
                $html = '';
                $class = 'form-control input-small';
                $html .= 'Please confirm that you want to remove all user settings where no valid user exists.<br/>'.
                         '<form method="post" action="?mode=task&id='.$id.'" enctype="multipart/form-data">'.
                         '<input type="hidden" name="process" value="clear"><br/>'.
                         '<input type="submit" name="submit" value="CLEAR ORPHANED RECORDS" class="'.$this->classes['button'].'">'.
                         '</form>';

                //display form in message box       
                $this->addMessage($html);      
            }
        }

        
           
    }

}

?>