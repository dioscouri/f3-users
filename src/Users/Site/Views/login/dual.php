<div class="container">
    <div class="row">

            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#login" data-toggle="tab">Login</a>
                </li>
                <li class="">
                    <a href="#register" data-toggle="tab">Register</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="login">
                	<?php echo $this->renderLayout('Users/Site/Views::login/login.php'); ?>	     
        		</div>
                <div class="tab-pane fade" id="register">
                	<?php echo $this->renderLayout('Users/Site/Views::login/register.php'); ?>  	
                </div>
            </div>

    </div>
</div>
