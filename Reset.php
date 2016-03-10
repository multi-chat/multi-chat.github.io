<form role="form" method="post" action="" autocomplete="off">
    <div class="form-group">
        <input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email" value="" tabindex="1">
    </div>
    
    <hr>
    <div class="row">
        <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Sent Reset Link" class="btn btn-primary btn-block btn-lg" tabindex="2"></div>
    </div>
</form>
<?php
if(isset($_GET['action'])){

    //check the action
    switch ($_GET['action']) {
        case 'active':
            echo "<h2 class='bg-success'>Your account is now active you may now log in.</h2>";
            break;
        case 'reset':
            echo "<h2 class='bg-success'>Please check your inbox for a reset link.</h2>";
            break;
    }
}
?>
//email validation
if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $error[] = 'Please enter a valid email address';
} else {
    $stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
    $stmt->execute(array(':email' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(empty($row['email'])){
        $error[] = 'Email provided is not on recognised.';
    }
        
}
//create the activation code
$token = md5(uniqid(rand(),true));
$stmt = $db->prepare("UPDATE members SET resetToken = :token, resetComplete='No' WHERE email = :email");
$stmt->execute(array(
    ':email' => $row['email'],
    ':token' => $token
));

//send email
$to = $row['email'];
$subject = "Password Reset";
$body = "<p>Someone requested that the password be reset.</p>
<p>If this was a mistake, just ignore this email and nothing will happen.</p>
<p>To reset your password, visit the following address: <a href='".DIR."resetPassword.php?key=$token'>".DIR."resetPassword.php?key=$token</a></p>";

$mail = new Mail();
$mail->setFrom(SITEEMAIL);
$mail->addAddress($to);
$mail->subject($subject);
$mail->body($body);
$mail->send();

//redirect to index page
header('Location: login.php?action=reset');
exit;
