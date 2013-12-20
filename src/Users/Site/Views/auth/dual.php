





<div class="span-6 center" >
<div class="tabs">
      <ul id="loginTabs" class="nav nav-tabs">
        <li class="active"><a href="#login" data-toggle="tab">Login</a></li>
        <li class=""><a href="#signup" data-toggle="tab">Signup</a></li>
      </ul>
      <div id="formContent" class="tab-content">
        <div class="tab-pane fade active in" id="login">
        	<?php echo $this->renderLayout('Users/Site/Views::auth/login.php'); ?>	     
		</div>
        <div class="tab-pane fade" id="signup">
        	<?php echo $this->renderLayout('Users/Site/Views::auth/signup.php'); ?>  	
        </div>
      </div>
</div>
</div>







