Dear <?php echo $name ?>,<br/><br/>

<?php echo $message ?>
<br/><br/>
Details:<br/>
<b>Owner:</b> <?php echo $idea->user->fullname ?><br/>
<b>Owner's Email:</b> <?php echo $idea->user->username ?><br/>
<b>Project Name:</b> <?php echo $idea->idea_name ?><br/>
<b>Goal:</b> <?php echo number_format($idea->model_request_funding->goal_amount) ?><br/>
<b>Completed Funding:</b> S$<?php echo number_format($idea->model_request_funding->current_funding) ?><br/><br/>
