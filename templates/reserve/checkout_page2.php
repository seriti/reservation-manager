<?php
use Seriti\Tools\Date;
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$list_param['class'] = 'form-control edit_input';
$text_param['class'] = 'form-control edit_input';
$textarea_param['class'] = 'form-control edit_input';


if($form['people_notes'] === '') $form['people_notes'] = $data['people_notes_default'];

$labels = $data['labels'];
?>

<div id="checkout_div">
  <p>
  <?php echo '<h1>'.$labels['package'].': '.$data['package']['title'].'</h1>'; ?>
  <h2>Please indicate who will be group leader and any other preferences members of your group will have</h2>
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
    <div class="col-sm-3">
      <?php echo Form::textInput('group_leader',$form['group_leader'],$text_param); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">People Notes:</div>
    <div class="col-sm-3">
    <?php echo Form::textAreaInput('people_notes',$form['people_notes'],50,5,$textarea_param); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6"><input type="submit" name="Submit" value="Proceed" class="btn btn-primary"></div>
  </div>  

</div>