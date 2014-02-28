Dear <?php echo $name ?>,<br/><br/>

<?php echo $message ?>
<br/><br/>
Details:<br/>
<b>Owner:</b> <?php echo $idea->user->fullname ?><br/>
<b>Owner's Email:</b> <?php echo $idea->user->username ?><br/>
<b>Project Name:</b> <?php echo $idea->idea_name ?><br/>
<b>UR:</b> <a href="<?php echo $idea->itemPage(true) ?>"><?php echo $idea->itemPage(true) ?></a><br/>

