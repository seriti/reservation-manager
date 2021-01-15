<?php
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$list_param['class'] = 'form-control edit_input';
$date_param['class'] = 'form-control edit_input bootstrap_date';

$date = getdate();
if($form['date_arrive'] == '') $form['date_arrive'] = date('Y-m-d',mktime(0,0,0,$date['mon'],$date['mday']+1,$date['year']));
if($form['date_depart'] == '') $form['date_depart'] = date('Y-m-d',mktime(0,0,0,$date['mon'],$date['mday']+2,$date['year']));
if($form['no_people'] == '') $form['no_people'] = 2;

$labels = $data['labels'];

?>

<div id="checkout_div">

  <p>
  <?php
  echo '<h1>'.$labels['package'].': '.$data['package']['title'].'</h1>';

  if(isset($data['user_id'])) {
      echo '<h2>Hi there <strong>'.$data['user_name'].'</strong>. you are logged in and ready to proceed with enquiry process.</h2>';
  } else {
      echo '<h2>If you are already a user <a href="/login">please login</a> before you proceed.</h2>';
      echo '<h2>If you are not a user then you can proceed and you will be registered automatically.</h2>';
  }
  ?>
  <h2>NB:Please indicate preferred dates. You will be contacted to confirm all details.</h2>
  <br/>
  </p>
  
  <div class="row">
    <div class="col-sm-3">Date arrive:</div>
    <div class="col-sm-3">
    <?php 
    echo Form::textInput('date_arrive',$form['date_arrive'],$date_param);
    ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">Date depart:</div>
    <div class="col-sm-3">
    <?php 
    echo Form::textInput('date_depart',$form['date_depart'],$date_param);
    ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">No people:</div>
    <div class="col-sm-3">
    <?php 
    echo Form::textInput('no_people',$form['no_people'],$list_param);
    ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6"><input type="submit" name="Submit" value="Proceed" class="btn btn-primary"></div>
  </div>  

    
  
</div>