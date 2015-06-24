<p>Hello {{@user.first_name}}!</p>
<p>Thanks for creating an account with us.  Please click the link below to confirm your email address:</p>
<p><a href="{{@base_url}}/login/validate/token/{{@user.id}}">{{@base_url}}/login/validate/token/{{@user.id}}</a></p>
<p>If you have problems, please copy and paste the above URL into your web browser.</p>
<p>Your token is: {{@user.id}}</p>
<p>Thanks.</p> 