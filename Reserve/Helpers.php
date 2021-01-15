<?php
namespace App\Reserve;

use Exception;
use Psr\Container\ContainerInterface;

use Seriti\Tools\Audit;
use Seriti\Tools\Calc;
use Seriti\Tools\Calendar;
use Seriti\Tools\Csv;
use Seriti\Tools\Html;
use Seriti\Tools\Image;
use Seriti\Tools\Pdf;
use Seriti\Tools\Doc;
use Seriti\Tools\Date;
use Seriti\Tools\STORAGE;
use Seriti\Tools\SITE_TITLE;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_DOCS;
use Seriti\Tools\UPLOAD_TEMP;
use Seriti\Tools\SITE_NAME;
use Seriti\Tools\AJAX_ROUTE;


class Helpers {
       
    //generic record get, add any exceptions you want
    public static function get($db,$table_prefix,$table,$id,$key = '') 
    {
        $table_name = $table_prefix.$table;

        if($key === '') $key = $table.'_id';    
        
        if($table === 'reserve') {
            $sql = 'SELECT * FROM '.$table_name.' WHERE '.$key.' = "'.$db->escapeSql($id).'" ';
        } else {
            $sql = 'SELECT * FROM '.$table_name.' WHERE '.$key.' = "'.$db->escapeSql($id).'" ';
        }

        $record = $db->readSqlRecord($sql);
                        
        return $record;
    } 

    public static function getPackage($db,$table_prefix,$package_id,&$error)  
    {
        $error = '';
        $table_package = $table_prefix.'package';
        $table_category = $table_prefix.'package_category';
        
        $sql = 'SELECT P.package_id,P.package_code,P.category_id,P.title,P.info,P.body_html,'.
                      'C.name AS category '.
               'FROM '.$table_package.' AS P '.
               'JOIN '.$table_category.' AS C ON(P.category_id = C.category_id) '.
               'WHERE package_id = "'.$db->escapeSql($package_id).'" ';
        $package = $db->readSqlRecord($sql);
        if($package == 0) {
            $error .= 'Package ID['.$package_id.'] invalid.';
        } else {
            //get other package info
        }

        return $package; 
    }    

    public static function getReservation($db,$table_prefix,$reserve_id,&$error)  
    {
        $error = '';
        $reserve = [];
        
        $table_location = $table_prefix.'location';
        $table_package = $table_prefix.'package';
        $table_reserve = $table_prefix.'reserve';
        $table_reserve_item = $table_prefix.'reserve_item';
        $table_item = $table_prefix.'item';
        $table_status = $table_prefix.'reserve_status';
        $table_transfer = $table_prefix.'reserve_transfer';
        $table_operator = $table_prefix.'service_operator';
        $table_people = $table_prefix.'reserve_people';
               
        $sql = 'SELECT R.reserve_id,R.code,R.source_id,R.location_id,R.package_id,R.no_people,R.date_arrive,R.date_depart,R.status_id,'.
                      'R.itinerary_notes,R.admin_notes,R.emergency_notes,R.people_notes,R.group_leader,R.terms_accepted,'.
                      'S.name AS status,S.sort AS status_no,L.name AS location,R.user_id_responsible,P.title AS package '.
               'FROM '.$table_reserve.' AS R JOIN '.$table_status.' AS S ON(R.status_id = S.status_id) '.
                     'LEFT JOIN '.$table_location.' AS L ON(R.location_id = L.location_id) '.
                     'LEFT JOIN '.$table_package.' AS P ON(R.package_id = P.package_id) '.
               'WHERE R.reserve_id = "'.$db->escapeSql($reserve_id).'" ';
        $reserve = $db->readSqlRecord($sql);
        if($reserve == 0) {
            $error .= 'Reservation ID['.$reserve_id.'] invalid.';
        } else {    
            $sql = 'SELECT RI.item_id, RI.date_arrive, RI.date_depart, RI.no_people, I.name '.
                   'FROM '.$table_reserve_item.' AS RI JOIN '.$table_item.' AS I ON(RI.item_id = I.item_id) '.
                   'WHERE RI.reserve_id = "'.$db->escapeSql($reserve_id).'" '.
                   'ORDER BY RI.date_arrive';
            $reserve['items'] = $db->readSqlArray($sql);

            $sql = 'SELECT T.transfer_id,T.type_id,T.operator_id,O.name AS operator,T.operator_fee,T.total_cost, '.
                          'T.date,T.start_time,T.start_place,T.end_time,T.end_place,T.no_people,T.notes  '.
                   'FROM '.$table_transfer.' AS T JOIN '.$table_operator.' AS O ON(T.operator_id = O.operator_id) '.
                   'WHERE T.reserve_id = "'.$db->escapeSql($reserve_id).'" '.
                   'ORDER BY T.date, T.start_time';
            $reserve['transfers'] = $db->readSqlArray($sql);

            $sql = 'SELECT people_id,name,title,date_birth,sharing '.
                   'FROM '.$table_people.' WHERE reserve_id = "'.$db->escapeSql($reserve_id).'" '.
                   'ORDER BY name ';
            $reserve['people'] = $db->readSqlArray($sql);
        }    
        
        return $reserve;
    }

    public static function sendReserveMessage($db,$table_prefix,ContainerInterface $container,$reserve_id,$subject,$message,$param=[],&$error)
    {
        $html = '';
        $error = '';
        $error_tmp = '';

        if(!isset($param['cc_admin'])) $param['cc_admin'] = true;
        if(!isset($param['login_link'])) $param['login_link'] = true;

        $system = $container['system'];
        $mail = $container['mail'];
        $user = $container['user'];
        $user_id = $user->getId();

        //setup email parameters
        $mail_footer = $system->getDefault('RESERVE_EMAIL_FOOTER','');
        $mail_param = [];
        $mail_param['format'] = 'html';
        if($param['cc_admin']) $mail_param['bcc'] = MAIL_FROM;
       
        $reserve = self::getReservation($db,$table_prefix,$reserve_id,$error_tmp);
        if($reserve === false or $error_tmp !== '') {
            $error .= 'Could not get Reservation details: '.$error_tmp;
        } else {
            if($reserve['user_id_responsible'] == 0) {
                $error .= 'No responsible user linked to reservation';
            } else {
                $responsible_user = $user->getUser('ID',$reserve['user_id_responsible']);
                if($responsible_user == 0) $error .= 'Responsible user['.$reserve['user_id_responsible'].'] Invalid.';
            }    
        }    

        if($error === '') {
            
            $login_html = '';
            if($param['login_link']) {
                $days_expire = 7;
                $login_url = $user->resetEmailLoginToken($reserve['user_id_responsible'],$days_expire);
                $login_html = '<br/><h3>Click <a href="'.$login_url.'">here to login</a> and manage reservation</h3>';
            }

            $mail_from = ''; //will use default MAIL_FROM
            $mail_to = $responsible_user['email'];

            $mail_subject = SITE_NAME.' Reservation ID['.$reserve_id.'] ';
            $audit_str = 'Reserve ID['.$reserve_id.'] ';

            if($subject !== '') $mail_subject .= ': '.$subject;
            
            $mail_body = '<h1>Attention: '.$responsible_user['name'].'</h1>';
            if($message !== '') $mail_body .= '<h2>'.$message.'</h2>';

            $mail_body .= '<h2>Reservation for : '.$reserve['package'].'</h2>';
            $mail_body .= 'No People : <b>'.$reserve['no_people'].'</b><br/>';
            $mail_body .= 'Arrive on <b>'.Date::formatDate($reserve['date_arrive']).'</b> and depart on <b>'.Date::formatDate($reserve['date_depart']).'</b><br/>';
            $mail_body .= 'Status : <b>'.$reserve['status'].'</b><br/>';

            $mail_body .= $login_html;
            
           
            /* Payments lonked to invoices NOT reservations??
            if($reserve['payments'] !== 0) {
                $mail_body .= '<h3>Payments</h3>'.Html::arrayDumpHtml($reserve['payments'],$html_param);
            }
            */
    
            $mail_body .= '<br/><br/>'.$mail_footer;
            
            $mail->sendEmail($mail_from,$mail_to,$mail_subject,$mail_body,$error_tmp,$mail_param);
            if($error_tmp != '') { 
                $error .= 'ERROR sending reservation details to email['. $mail_to.']:'.$error_tmp; 
                $audit_str .=  $error_str;
            } else {
                $audit_str .= 'SUCCESS sending reservation details to email['. $mail_to.']'; 
            }

            Audit::action($db,$user_id,'RESERVE_EMAIL',$audit_str);
        }

        if($error === '') return true; else return false;
    }

    public static function getMonthlySequence($from_month,$from_year,$to_month,$to_year)  {
        $months = [];
        
        //get all months and populate default empty price array
        $no_months = Date::getNoMonths($from_month,$from_year,$to_month,$to_year);
        $year = $from_year;
        $month = $from_month;

        for($n = 1; $n <= $no_months; $n++) {
            $months[$n] = ['mon'=>$month,'year'=>$year];

            $month++;
            if($month > 12) {
                $month = 1;
                $year = $year + 1;
            }
        }

        return $months;
    } 

    public static function formatReservation($reserve = [],$options = [])
    {
        $html = '';

        if(!isset($options['link'])) $options['link'] = true;
        if(!isset($options['tag'])) $options['tag'] = true;
        if(!isset($options['width'])) $options['width'] = 600;
        if(!isset($options['height'])) $options['height'] = 600;

        if($options['link']) {
            $href = "javascript:open_popup('reserve_detail?id=".$reserve['reserve_id']."',".$options['width'].",".$options['height'].")";
            $code .= '<a href="'.$href.'">'.$reserve['code'].'</a>';
        } else {
            $code .= $reserve['code'];
        }    

        $html .= $code.'('.$reserve['no_people'].') '.$reserve['status_no'];

        return $html;
    } 
        
    
    public static function reservationCalendar($db,$table_prefix,$location_id,$status_id,$from_month,$from_year,$to_month,$to_year,$options = [],&$error)
    {
        $error = '';
        $html = '';

        $table_location = $table_prefix.'location';
        $table_reserve = $table_prefix.'reserve';
        $table_item = $table_prefix.'item';
        $table_status = $table_prefix.'reserve_status';

        $calendar = new Calendar();

        $status = self::get($db,$table_prefix,'reserve_status',$status_id,'status_id'); 

        $date_from = date('Y-m-d',mktime(0,0,0,$from_month,1,$from_year));
        $date_to = date('Y-m-d',mktime(0,0,0,$to_month+1,0,$to_year));

        $months = self::getMonthlySequence($from_month,$from_year,$to_month,$to_year);

        
        $sql = 'SELECT R.reserve_id, R.code, R.source_id, R.location_id, R.package_id, R.no_people, R.date_arrive, R.date_depart, R.status_id ,'.
                      'S.name AS status,S.sort AS status_no '.
               'FROM '.$table_reserve.' AS R JOIN '.$table_status.' AS S ON(R.status_id = S.status_id) '.
               'WHERE R.date_arrive <= "'.$date_to.'" AND R.date_depart >= "'.$date_from.'" AND '.
                     'S.sort >= '.$status['sort'].' ';
        if($location_id !== 'ALL') $sql .= 'AND R.location_id = "'.$db->escapeSql($location_id).'"';

        $reservations = $db->readSqlArray($sql);
        if($reservations == 0) $error .= 'No reservations found over period from '.$date_from.' to '.$date_to;
        

        if($error !== '') return false;


        $cal_options = [];
        foreach($reservations as $reserve_id => $reserve) {
            $event_html = self::formatReservation($reserve);
            //NB: departure date is NOT an occupancy date
            //$depart = Date::getDate($reserve['date_depart']);
            //$date_to = date('Y-m-d',mktime(0,0,0,$depart['mon'],$depart['mday']-1,$depart['year'])); 
            $calendar->addEvent($reserve['date_arrive'],$reserve['date_depart'],$event_html,$cal_options);
        }

        $month_options = [];
        foreach($months as $month) {
            $month_start = date('Y-m-d',mktime(0,0,0,$month['mon'],1,$month['year']));
            $month_end = date('Y-m-d',mktime(0,0,0,$month['mon']+1,0,$month['year']));
            $html .= '<h2>'.Date::monthName($month['mon']).'-'.$month['year'].'</h2>';
            $html .=  $calendar->show('MONTH',$month_start,$month_end,$month_options);
        }

        return $html;
    }

    public static function reservationItemCalendar($db,$table_prefix,$location_id,$status_id,$from_month,$from_year,$to_month,$to_year,$options = [],&$error)
    {
        $error = '';

        if(!isset($options['format'])) $options['format'] = 'DATE_UNIT'; //'UNIT_DATE' other option

        $table_location = $table_prefix.'location';
        $table_reserve = $table_prefix.'reserve';
        $table_reserve_item = $table_prefix.'reserve_item';
        $table_item = $table_prefix.'item';
        $table_status = $table_prefix.'reserve_status';

        $calendar = new Calendar();

        $status = self::get($db,$table_prefix,'reserve_status',$status_id,'status_id'); 

        $date_from = date('Y-m-d',mktime(0,0,0,$from_month,1,$from_year));
        $date_to = date('Y-m-d',mktime(0,0,0,$to_month+1,0,$to_year));

        $sql = 'SELECT item_id,name FROM '.$table_item.' WHERE status = "OK" ORDER By sort ';
        $items = $db->readSqlArray($sql);
        if($items == 0) $error .= 'No reservation items found at location';
        

        $sql = 'SELECT RI.item_id, RI.date_arrive, RI.date_depart, RI.no_people, I.name, '.
                      'R.reserve_id, R.code, R.no_people AS res_no_people, R.date_arrive AS res_date_arrive, R.date_depart AS res_date_depart, R.status_id, '.
                      'S.name AS status, S.sort AS status_no '.
               'FROM '.$table_reserve_item.' AS RI JOIN '.$table_item.' AS I ON(RI.item_id = I.item_id) JOIN '.
                       $table_reserve.' AS R ON(RI.reserve_id = R.reserve_id) JOIN '.$table_status.' AS S ON(R.status_id = S.status_id) '.
               'WHERE RI.date_arrive <= "'.$date_to.'" AND RI.date_depart >= "'.$date_from.'" AND '.
                     'S.sort >= '.$status['sort'].' ';
        if($location_id !== 'ALL') $sql .= 'AND R.location_id = "'.$db->escapeSql($location_id).'"';

        $reservations = $db->readSqlArray($sql);
        if($reservations == 0) $error .= 'No reservation '.MODULE_RESERVE['labels']['item'].'s found over period from '.$date_from.' to '.$date_to;
        
        if($error !== '') return false;

        foreach($items as $item_id => $item) {
            $calendar->addUnit($item_id,$item['name']);
        }

        $cal_options = [];
        foreach($reservations as $item_id => $reserve) {
            $event_html = self::formatReservation($reserve);
            $cal_options['unit_id'] = $item_id;
            $depart = Date::getDate($reserve['date_depart']);
            $date_to = date('Y-m-d',mktime(0,0,0,$depart['mon'],$depart['mday']-1,$depart['year'])); 
            $calendar->addEvent($reserve['date_arrive'],$date_to,$event_html,$cal_options);
        }

        $cal_options = [];
        $html = $calendar->show($options['format'],$date_from,$date_to,$cal_options);


        return $html;
    }

    public function getPackageImageGallery($db,$table_prefix,$s3,$package_id,$param = [])
    {
        $html = '';

        if(!isset($param['access'])) $param['access'] = MODULE_RESERVE['images']['access'];

        $sql = 'SELECT title,info '.
               'FROM '.$table_prefix.'package '.
               'WHERE package_id = "'.$db->escapeSql($package_id).'" AND status <> "HIDE"';
        $package = $db->readSqlRecord($sql);
        if($package === 0) {
            $html = '<h1>Package no longer available.</h1>';
            return $html;
        } else {
            $html .= '<h1><a href="Javascript:onClick=window.close()">&laquo;go back</a> '.$package['title'].'</h1>';
        }


        $location_id = 'PACIMG'.$package_id;
        $sql = 'SELECT file_id,file_name,file_name_tn,caption AS title '.
               'FROM '.$table_prefix.'file WHERE location_id = "'.$db->escapeSql($location_id).'" ';
        $images = $db->readSqlArray($sql);
        if($images != 0) {
            //setup amazon links
            foreach($images as $id => $image) {
                $url = $s3->getS3Url($image['file_name'],['access'=>$param['access']]);
                $images[$id]['src'] = $url;
            }

            if(count($images) == 1) {
                foreach($images as $image) {
                    $html .= '<img src="'.$image['src'].'" class="img-responsive center-block">';    
                }  
            } else {  
                $options = array();
                $options['img_style'] = 'max-height:600px;';
                //$options['src_root'] = ''; stored on AMAZON
                $type = 'CAROUSEL'; //'THUMBNAIL'
                
                $html .= Image::buildGallery($images,$type,$options);
                
            }  
            
        } 

        return $html; 
    }
    
}
?>
