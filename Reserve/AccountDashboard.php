<?php
namespace App\Reserve;

use Seriti\Tools\Date;
use Seriti\Tools\CURRENCY_SYMBOL;
use Seriti\Tools\Dashboard AS DashboardTool;

class AccountDashboard extends DashboardTool
{
     

    //configure
    public function setup($param = []) 
    {
        $error = '';  
        $this->col_count = 2;

        $user = $this->getContainer('user'); 

        //Class accessed outside /App/Reserve so cannot use TABLE_PREFIX constant
        $module = $this->container->config->get('module','reserve');
        $table_prefix = $module['table_prefix'];

        $sql = 'SELECT * FROM '.$table_prefix.'user_extend WHERE user_id = "'.$user->getId().'" ';
        $user_extend = $this->db->readSqlRecord($sql);


        //check cart contents
        $cart_html = ''; 
        /*
        $temp_token = $user->getTempToken(False);
        $cart = Helpers::getCart($this->db,$table_prefix,$temp_token);
        if($cart === 0 ) {
            $cart_html .= 'Your reservation cart is empty.';
        } else {
            $cart_html .=  'You have '.$cart['item_count'].' item/s in your reservation cart.<br/>'.
                           '<a href="/public/cart">Click to view cart contents:<span class="glyphicon glyphicon-shopping-cart"></span></a>';
        }
        */

        //check for active orders
        $sql = 'SELECT reserve_id,date_create FROM '.$table_prefix.'reserve '.
               'WHERE user_id_responsible = "'.$user->getId().'" AND date_arrive > CURDATE() '.
               'ORDER BY date_arrive ';
        $new_reserve = $this->db->readSqlList($sql);
        if($new_reserve === 0) {
            $reserve_html = 'NO pending reservations';
        } else {
            $reserve_html .= '<ul>';
            foreach($new_reserve as $reserve_id => $date_create) {
                $reserve = Helpers::getReservation($this->db,$table_prefix,$reserve_id,$error);
                if($error !== '') {
                    $reserve_html .= '<li>Error: '.$error.'</li>';
                } else {
                    $people_link = '<a href="javascript:open_popup(\'reserve_people?id='.$reserve_id.'\',600,600)">(manage people)</a>';
                    $edit_link = '<a href="reserve?mode=edit&id='.$reserve_id.'">edit</a>';

                    $missing = [];
                    $notes = '';
                    if(!$reserve['terms_accepted']) $missing[] = 'T&C not accepted';
                    if(!$reserve['people_notes']) $missing[] = 'Notes & preferences';
                    if(!$reserve['group_leader']) $missing[] = 'Group leader';
                    if(count($missing)) {
                      $notes = '<b>'.$this->icons['required'].'</b> '.implode(', ',$missing);
                      $msg = 'Reserve ID-<b>'.$reserve_id.'</b> arriving <b>'.Date::formatDate($reserve['date_arrive']).'</b> '.
                             'needs your attention. Please '.$edit_link.' reservation.';
                      $this->addMessage($msg);
                    }

                    $reserve_html .= '<li>'.
                                   'Reserve ID:<b>'.$reserve_id.'</b> '.$edit_link.' '.$notes.'<br/>'.
                                   'Status: <strong>'.$reserve['status'].'</strong><br/>'.
                                   'No people: '.$reserve['no_people'].' '.$people_link.'<br/>'.
                                   'Arrive: <b>'.Date::formatDate($reserve['date_arrive']).'</b> & '.
                                   'Depart: '.Date::formatDate($reserve['date_depart']).'<br/>'.
                                   '</li>'; 
                }
               
            }
            $reserve_html .= '</ul>';
        }    

        //(block_id,col,row,title)
        $this->addBlock('USER',1,1,'Your data: <a href="profile?mode=edit">edit</a>');
        $this->addItem('USER','<strong>Email:</strong> '.$user->getEmail());
        $this->addItem('USER','<strong>Email alternative:</strong> '.nl2br($user_extend['email_alt']));
        $this->addItem('USER','<strong>Cellphone:</strong> '.$user_extend['cell']);
        $this->addItem('USER','<strong>Landline:</strong> '.$user_extend['tel']);
        $this->addItem('USER','<strong>Billing Address:</strong><br/>'.nl2br($user_extend['bill_address']));
        
        //$this->addBlock('CART',1,2,'Shopping cart');
        //$this->addItem('CART',$cart_html); 

        $this->addBlock('RESERVE',2,1,'Pending Reservations');
        $this->addItem('RESERVE',$reserve_html);  
        
    }

}

?>