<?php
use Seriti\Tools\Date;
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$list_param['class'] = 'form-control edit_input';
$text_param['class'] = 'form-control edit_input';
$textarea_param['class'] = 'form-control edit_input';

$labels = $data['labels'];
?>

<div id="checkout_div">

  <p>
  <?php echo '<h1>'.$labels['package'].': '.$data['package']['title'].'</h1>'; ?>
  <h2>Please check all your details and process reservation enquiry, or go back using bread crumb trail above</h2>
  </p>
  
  <div class="row">
    <div class="col-sm-3">Date arrive:</div>
    <div class="col-sm-3"><strong><?php echo Date::formatDate($form['date_arrive']) ?></strong></div>
  </div>
  <div class="row">
    <div class="col-sm-3">Date depart:</div>
    <div class="col-sm-3"><strong><?php echo Date::formatDate($form['date_depart']); ?></strong></div>
  </div>
  <div class="row">
    <div class="col-sm-3">No people:</div>
    <div class="col-sm-3"><strong><?php echo $form['no_people']; ?></strong></div>
  </div>

  <br/>

  <div class="row">
    <div class="col-sm-3">Group leader:</div>
    <div class="col-sm-3"><strong><?php echo $form['group_leader']; ?></strong></div>
  </div>
  <div class="row">
    <div class="col-sm-3">People Notes:</div>
    <div class="col-sm-3"><strong><?php echo nl2br($form['people_notes']); ?></strong></div>
    </div>
  </div>

  <br/>

  <div class="row">
    <div class="col-sm-3">Your email address:</div>
    <div class="col-sm-3">
      <?php 
      if(isset($data['user_id'])) {
          echo '<strong>'.$data['user_email'].'</strong>';
          if(isset($data['user_created']) and $data['user_created']) {
             echo '</div><div class="col-sm-3"><i>You are now registered with us and logged in. You have been emailed your password.</i>';
          }
      } else {
          echo Form::textInput('user_email',$form['user_email'],$text_param); 
      }    
      ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">Your name:</div>
    <div class="col-sm-3">
      <?php 
      if(isset($data['user_id'])) {
          echo '<strong>'.$data['user_name'].'</strong>';
      } else {
          echo Form::textInput('user_name',$form['user_name'],$text_param); 
      }    
      ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">Your Cell:</div>
    <div class="col-sm-3">
      <?php echo Form::textInput('user_cell',$form['user_cell'],$text_param); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">Bill to address:</div>
    <div class="col-sm-3">
    <?php echo Form::textAreaInput('user_bill_address',$form['user_bill_address'],50,5,$textarea_param); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6"><input type="submit" name="Submit" value="Confirm Reservation Enquiry" class="btn btn-primary"></div>
  </div>  

</div>
