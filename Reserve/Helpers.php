<?php
namespace App\Reserve;

use Exception;
use Seriti\Tools\Calc;
use Seriti\Tools\Calendar;
use Seriti\Tools\Csv;
use Seriti\Tools\Html;
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
    
}
?>
