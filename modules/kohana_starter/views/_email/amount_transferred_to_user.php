Dear <?php echo $idea->user->fullname ?>,<br/><br/>

S$<?php echo number_format($idea->model_request_funding->current_funding) ?> has been trasnferred to you.<br/><br/>

<b>Project Name:</b> <?php echo $idea->idea_name ?><br/>
<b>Goal:</b> <?php echo number_format($idea->model_request_funding->goal_amount) ?><br/>
<b>Current Funding:</b> <?php echo number_format($idea->model_request_funding->current_funding,2) ?><br/><br/>

Click here to login:<br>
<a href="<?= $url_login ?>"><?= $url_login ?></a>