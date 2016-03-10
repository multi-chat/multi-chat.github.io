//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 
<h2>Member only page</h2>
<p><a href='logout.php'>Logout</a></p>
