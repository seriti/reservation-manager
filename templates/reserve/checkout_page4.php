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
  </p>

  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">Date arrive:</div>
        <div class="col-sm-6"><strong><?php echo Date::formatDate($form['date_arrive']) ?></strong></div>
      </div>
      <div class="row">
        <div class="col-sm-6">Date depart:</div>
        <div class="col-sm-6"><strong><?php echo Date::formatDate($form['date_depart']); ?></strong></div>
      </div>
      <div class="row">
        <div class="col-sm-6">No people:</div>
        <div class="col-sm-6"><strong><?php echo $form['no_people']; ?></strong></div>
      </div>

      <br/>

      <div class="row">
        <div class="col-sm-6">Group leader:</div>
        <div class="col-sm-6"><strong><?php echo $form['group_leader']; ?></strong></div>
      </div>
      <div class="row">
        <div class="col-sm-6">People Notes:</div>
        <div class="col-sm-6"><strong><?php echo nl2br($form['people_notes']); ?></strong></div>
        </div>
      </div>
    
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-12">
          <?php 
          echo '<h2>Thankyou, your reservation enquiry has been processed. We will contact you shortly.</h2>';

          if(isset($data['user_created']) and $data['user_created']) {
            echo '<h2>You are now registered with us and logged in. You have been emailed your password.</h2>';
          } 

          echo  '<p>You can <a href="account/dashboard">Manage your account</a> and reservations/enquiries, or create another enquiry</p>';
          ?>
          

        </div>
      </div>
    </div>

  </div>     

</div>