<?php
namespace App\Reserve;

use Seriti\Tools\CURRENCY_ID;
use Seriti\Tools\Form;
use Seriti\Tools\Report AS ReportTool;

use App\Reserve\Helpers;

class CalendarReport extends ReportTool
{
     

    //configure
    public function setup() 
    {
        //$this->report_header = 'WTF';
        $param = [];
        $this->report_select_title = 'Select calendar';
        $this->always_list_reports = true;

        $param = ['input'=>['select_location','select_month_period','select_status']];
        $this->addReport('RESERVE_MONTH','Reservations monthly calendar',$param); 
        $this->addReport('RESERVE_ITEM','Reservations '.MODULE_RESERVE['labels']['item'].' calendar, by date',$param); 
        $this->addReport('RESERVE_ITEM2','Reservations '.MODULE_RESERVE['labels']['item'].' calendar, by '.MODULE_RESERVE['labels']['item'],$param); 
        
        $param = ['input'=>['select_operator','select_month_period','select_status']];
        $this->addReport('TRANSFER','Reservations calendar',$param); 

        
        $this->addInput('select_location','');
        $this->addInput('select_operator','');
        $this->addInput('select_month_period',''); 
        $this->addInput('select_status','');
    }

    protected function viewInput($id,$form = []) 
    {
        $html = '';
        
        if($id === 'select_location') {
            $param = [];
            $param['class'] = 'form-control input-medium';
            $param['xtra'] = ['ALL'=>'All locations'];
            $sql = 'SELECT location_id,name FROM '.TABLE_PREFIX.'location WHERE status = "OK" ORDER BY sort'; 
            if(isset($form['location_id'])) $location_id = $form['location_id']; else $location_id = 'ALL';
            $html .= Form::sqlList($sql,$this->db,'location_id',$location_id,$param);
        }

        if($id === 'select_operator') {
            $param = [];
            $param['class'] = 'form-control input-medium';
            $param['xtra'] = ['ALL'=>'All Operators'];
            $sql = 'SELECT operator_id,name FROM '.TABLE_PREFIX.'service_operator WHERE status = "OK" ORDER BY sort'; 
            if(isset($form['operator_id'])) $operator_id = $form['operator_id']; else $operator_id = 'ALL';
            $html .= Form::sqlList($sql,$this->db,'operator_id',$operator_id,$param);
        }

        if($id === 'select_month_period') {
            $past_years = 10;
            $future_years = 0;

            $param = [];
            $param['class'] = 'form-control input-small input-inline';
            
            $html .= 'From:';
            if(isset($form['from_month'])) $from_month = $form['from_month']; else $from_month = 1;
            if(isset($form['from_year'])) $from_year = $form['from_year']; else $from_year = date('Y');
            $html .= Form::monthsList($from_month,'from_month',$param);
            $html .= Form::yearsList($from_year,$past_years,$future_years,'from_year',$param);
            $html .= '&nbsp;&nbsp;To:';
            if(isset($form['to_month'])) $to_month = $form['to_month']; else $to_month = date('m');
            if(isset($form['to_year'])) $to_year = $form['to_year']; else $to_year = date('Y');
            $html .= Form::monthsList($to_month,'to_month',$param);
            $html .= Form::yearsList($to_year,$past_years,$future_years,'to_year',$param);
        }

        
        if($id === 'select_status') {
            $param = [];
            $param['class'] = 'form-control input-medium input-inline';
            $sql = 'SELECT status_id,CONCAT(sort,"-",name) FROM '.TABLE_PREFIX.'reserve_status ORDER BY sort'; 
            if(isset($form['status_id'])) $status_id = $form['status_id']; else $status_id = '1';
            $html .= 'Status >= '.Form::sqlList($sql,$this->db,'status_id',$status_id,$param);
        }
        
        return $html;       
    }

    protected function processReport($id,$form = []) 
    {
        $html = '';
        $error = '';
        $options = [];
        
        /*
        if($form['provider_id'] === 'ALL') {
            $html .= '<h2>(ALL payment providers, values expressed in currency - '.CURRENCY_ID.')</h2>';
        } else {
            $provider = Helpers::getProvider($this->db,TABLE_PREFIX,$form['provider_id']);
            $html .= '<h2>('.$provider['name'].', values expressed in currency - '.CURRENCY_ID.')</h2>';
        } 
        */   
        
        if($id === 'RESERVE_MONTH') {
            $html .= Helpers::reservationCalendar($this->db,TABLE_PREFIX,$form['location_id'],$form['status_id'],$form['from_month'],$form['from_year'],$form['to_month'],$form['to_year'],$options,$error);
            if($error !== '') $this->addError($error);
        }

        if($id === 'RESERVE_ITEM' or $id === 'RESERVE_ITEM2') {
            if($id === 'RESERVE_ITEM') $options['format'] = 'DATE_UNIT';
            if($id === 'RESERVE_ITEM2') $options['format'] = 'UNIT_DATE';
            $html .= Helpers::reservationItemCalendar($this->db,TABLE_PREFIX,$form['location_id'],$form['status_id'],$form['from_month'],$form['from_year'],$form['to_month'],$form['to_year'],$options,$error);
            if($error !== '') $this->addError($error);
        }


        if($id === 'TRANSFER') {
            //$html .= Helpers::getPortfolioChart($this->db,'performance',$form['portfolio_id'],$form['currency_id'],$form['from_month'],$form['from_year'],$form['to_month'],$form['to_year'],$options,$error);
            $error = 'Not coded yet';
            if($error !== '') $this->addError($error);
        }
                

        return $html;
    }

}