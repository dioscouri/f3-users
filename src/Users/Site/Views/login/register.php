<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 well well-sm">
            <legend>
                <i class="glyphicon glyphicon-globe"></i>
                Register
            </legend>
            <form action="./register" method="post" class="form" role="form">
                <div class="row">
                    <div class="col-xs-6 col-md-6">
                        <input class="form-control" name="first_name" placeholder="First Name" type="text" required autofocus />
                    </div>
                    <div class="col-xs-6 col-md-6">
                        <input class="form-control" name="last_name" placeholder="Last Name" type="text" required />
                    </div>
                </div>
                <input class="form-control" name="email" placeholder="Your Email" type="email" /> <input class="form-control" name="password" placeholder="New Password" type="password" />
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
            </form>
        </div>
    </div>
</div>