<?php
namespace App\Reserve;

use Exception;

use Seriti\Tools\Wizard;
use Seriti\Tools\Date;
use Seriti\Tools\Form;
use Seriti\Tools\Doc;
use Seriti\Tools\Calc;
use Seriti\Tools\Secure;
use Seriti\Tools\Plupload;
use Seriti\Tools\STORAGE;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_TEMP;
use Seriti\Tools\UPLOAD_DOCS;
use Seriti\Tools\TABLE_USER;
use Seriti\Tools\SITE_NAME;

use App\Reserve\Helpers;

use App\Payment\Helpers as PaymentHelpers;
use App\Payment\Gateway;

class CheckoutWizard extends Wizard 
{
    protected $user;
    protected $temp_token;
    protected $user_id;
    protected $table_prefix;
    protected $package_id;
        
    //configure
    public function setup($param = []) 
    {
        $error = '';
        if(!defined('MODULE_RESERVE')) $error .= 'Reserve module not defined. ';
        //if(!defined('MODULE_PAYMENT')) $error .= 'Payment module not defined. ';
        if($error !== '')  throw new Exception('CONSTANT_NOT_DEFINED: '.$error);
        
        $this->table_prefix = MODULE_RESERVE['table_prefix'];
       
        $this->user = $this->getContainer('user');
        $this->temp_token = $this->user->getTempToken();

        //will return 0 if NO logged in user
        $this->user_id = $this->user->getId();

        $param['bread_crumbs'] = true;
        $param['strict_var'] = false;
        $param['csrf_token'] = $this->temp_token;
        parent::setup($param);

        //standard user cols
        $this->addVariable(array('id'=>'date_arrive','type'=>'DATE','title'=>'Date arrive','required'=>true));
        $this->addVariable(array('id'=>'date_depart','type'=>'DATE','title'=>'Date depart','required'=>true));
        $this->addVariable(array('id'=>'no_people','type'=>'INTEGER','min'=>1,'title'=>'Number of people','required'=>true));

        $this->addVariable(array('id'=>'group_leader','type'=>'STRING','title'=>'Group leader','required'=>false));
        $this->addVariable(array('id'=>'people_notes','type'=>'TEXT','title'=>'People notes','required'=>true));
        
        $this->addVariable(array('id'=>'user_email','type'=>'EMAIL','title'=>'Your email address','required'=>true));
        $this->addVariable(array('id'=>'user_name','type'=>'STRING','title'=>'Your name','required'=>false));
        $this->addVariable(array('id'=>'user_cell','type'=>'STRING','title'=>'Your name','required'=>false));
        $this->addVariable(array('id'=>'user_bill_address','type'=>'TEXT','title'=>'Billing address','required'=>true));
        
        //define pages and templates
        $this->addPage(1,'Dates','reserve/checkout_page1.php',['go_back'=>true]);
        $this->addPage(2,'People details','reserve/checkout_page2.php');
        $this->addPage(3,'Confirm enquiry','reserve/checkout_page3.php');
        $this->addPage(4,'Payment','reserve/checkout_page4.php',['final'=>true]);
            

    }

    //tell wizard what package we are dealing with
    public function initialConfig() 
    {
        if(isset($_GET['package'])) {
            $this->data['package_id'] = Secure::clean('integer',$_GET['package']);
            $this->data['package'] = Helpers::getPackage($this->db,$this->table_prefix,$this->data['package_id'],$error);
            if($error !== '') {
                throw new Exception('RESERVE_PACKAGE_ERROR: Could not process enquiry for unrecognised Package['.$this->data['package_id'].'].');
                exit;
            }

            $system = $this->getContainer('system');
            $this->data['people_notes_default'] = $system->getDefault('RESERVE_PREFERENCE','');
            
            $this->data['labels'] = MODULE_RESERVE['labels'];

            $this->saveData('data');
        }
    }

    public function processPage() 
    {
        $error = '';
        $error_tmp = '';

        //process shipping and payment options
        if($this->page_no == 1) {
            $date_arrive = $this->form['date_arrive'];
            $date_depart = $this->form['date_depart'];
            $no_people = $this->form['no_people'];

            $date_now = date('Y-m-d');
            $days_to_depart = Date::calcDays($date_now,$date_arrive,'MYSQL',['include_first'=>false]);
            if($days_to_depart < 1) $this->addError('Arrival date['.$date_arrive.'] must be tomorrow or later');
            
            $no_nights = Date::calcNights($date_arrive,$date_depart,'MYSQL');
            if($no_nights < 1 ) $this->addError('Arrival date['.$date_arrive.'] must be before departure date['.$date_depart.']');
        } 
        
        //process additional info required
        if($this->page_no == 2) {


        }  
        
        //process address details and user register if not logged in
        if($this->page_no == 3) {
            
            //check if an existing user has not logged in
            if($this->user_id == 0) {
                $exist = $this->user->getUser('EMAIL_EXIST',$this->form['user_email']);
                if($exist !== 0 ) {
                    $this->addError('Your email address is already in use!');
                    $this->addMessage('Please <a href="/login">login</a> with that email, or use a different email address.');
                }    
            }

            //register new user if not exist
            if(!$this->errors_found and $this->user_id == 0) {
                
                $password = Form::createPassword();
                $access = 'USER';
                $zone = 'PUBLIC';
                $status = 'NEW';
                $name = $this->form['user_name'];
                $email = $this->form['user_email'];

                $this->user->createUser($name,$email,$password,$access,$zone,$status,$error_tmp);
                if($error_tmp !== '') {
                    $this->addError($error_tmp);
                } else {
                    $user = $this->user->getUser('EMAIL',$email);
                    $remember_me = true;
                    $days_expire = 30;
                    $this->user->manageUserAction('LOGIN_REGISTER',$user,$remember_me,$days_expire);
                    
                    $this->data['user_created'] = true;
                    $this->data['user_name'] = $name;   
                    $this->data['user_email'] = $email;   
                    $this->data['password'] = $password;
                    $this->data['user_id'] = $user[$this->user_cols['id']];
                    //set user_id so wizard knows user created 
                    $this->user_id = $this->data['user_id'];
                    
                    $mailer = $this->getContainer('mail');
                    $to = $email;
                    $from = ''; //default config email from used
                    $subject = SITE_NAME.' user registration';
                    $body = 'Hi There '.$name."\r\n".
                            'You have been registered as a user with us. Please note your credentials below:'."\r\n".
                            'Login email: '.$email."\r\n".
                            'Login Password: '.$password."\r\n\r\n".
                            'Your are logged in for 30 days from device that you processed enquiry from, unless you logout or delete site cookies.'."\r\n".
                            'You can at any point request a password reset or login token to be emailed to you from login screen.';

                    if($mailer->sendEmail($from,$to,$subject,$body,$error_tmp)) {
                        $this->addMessage('Success sending your registration details to['.$to.'] '); 
                    } else {
                        $this->addMessage('Could not email your registration details to['.$to.'] '); 
                        $this->addMessage('This is not a biggie. You are logged in from this device for 30 days, and you can always request a password reset or new login token from login screen using your email address.');
                    } 
                }

            }


            if(!$this->errors_found) {
                $table_extend = $this->table_prefix.'user_extend';  

                $data = [];
                $data['user_id'] = $this->user_id;
                $data['cell'] = $this->form['user_cell'];
                $data['bill_address'] = $this->form['user_bill_address'];

                $extend = $this->db->getRecord($table_extend,['user_id'=>$data['user_id']]);
                if($extend === 0) {
                    $this->db->insertRecord($table_extend,$data,$error_tmp );
                } else {
                    unset($data['user_id']);
                    $where = ['extend_id' => $extend['extend_id']];
                    $this->db->updateRecord($table_extend,$data,$where,$error_tmp );
                }

                if($error_tmp !== '') {
                    $error = 'We could not save your details.';
                    if($this->debug) $error .= $error_tmp;
                    $this->addError($error);
                }
            } 

            //Create reservation enquiry
            if(!$this->errors_found) {
                $table_reserve = $this->table_prefix.'reserve';
                
                //get lowest initial status setting
                $sql = 'SELECT status_id FROM '.$this->table_prefix.'reserve_status ORDER BY sort LIMIT 1';
                $status_id = $this->db->readSqlValue($sql,0);

                $data = [];
                //NB: *** ASSIGN USER ID & REMOVE TEMP TOKEN *** this designates it as a valid order and not temp cart
                $data['user_id_create'] = $this->user_id;
                $data['user_id_responsible'] = $this->user_id;
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['status_id'] = $status_id;
                $data['package_id'] = $this->data['package_id'];
                $data['code'] = $this->data['package']['package_code'];

                $data['date_arrive'] = $this->form['date_arrive'];
                $data['date_depart'] = $this->form['date_depart'];
                $data['no_people'] = $this->form['no_people'];
                $data['group_leader'] = $this->form['group_leader'];
                $data['people_notes'] = $this->form['people_notes'];
                
                $this->data['reserve_id'] = $this->db->insertRecord($table_reserve,$data,$error_tmp);
                if($error_tmp !== '') {
                    $error = 'We could not save enquiry details.';
                    if($this->debug) $error .= $error_tmp;
                    $this->addError($error);
                } 
            }

            //send confirmation email
            if(!$this->errors_found) {
                //send user message with payment instructions
                $param = ['cc_admin'=>true];
                $subject = SITE_NAME.' Reservation enquiry';
                $message = 'Thank you for your enquiry!<br/>'.
                           'We will contact you shortly to confirm details.';

                Helpers::sendReserveMessage($this->db,$this->table_prefix,$this->container,$this->data['reserve_id'],$subject,$message,$param,$error_tmp);
                if($error_tmp !== '') {
                    $message = 'We could not email you enquiry details, but your enquiry has been successfully processed. '.
                               'Please check your account page for details.';
                    if($this->debug) $message .= '<br/>Error: '.$error_tmp;
                    $this->addMessage($message);
                } else {
                    $this->addMessage('Successfully emailed you with enquiry details.');
                }

                
            }    
   
        } 
        
    }

    public function setupPageData($no)
    {
        //if($no == 3) {}
        

        //NB: TEMP COOKIE CAN OUTLIVE USER LOGIN SESSION if did NOT select []remember me option
        if($this->user_id == 0 and isset($this->data['user_id'])) {
            unset($this->data['user_id']);
            $this->saveData('data');
        }



        //setup user data ONCE only, if a user is logged in
        if($this->user_id != 0 and !isset($this->data['user_id'])) {
            $this->data['user_id'] = $this->user_id;    
            $this->data['user_name'] = $this->user->getName();
            $this->data['user_email'] = $this->user->getEmail();

            $this->saveData('data');

            //get extended user info
            $sql = 'SELECT * FROM '.$this->table_prefix.'user_extend WHERE user_id = "'.$this->user_id.'" ';
            $user_extend = $this->db->readSqlRecord($sql);
            
            if($user_extend != 0) {
                $this->form['user_email_alt'] = $user_extend['email_alt'];
                $this->form['user_cell'] = $user_extend['cell'];
                $this->form['user_bill_address'] = $user_extend['bill_address'];

                //NB: need to save $this->data as required in subsequent pages
                $this->saveData('form');
            }    
        }
        
    }

}

?>


