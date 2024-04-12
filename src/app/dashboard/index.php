<?php

use Lib\Auth\Auth;

$auth = new Auth();

if ($isPost) {
    $auth->logout();
    redirect('/auth/login');
}


?>
<p><?= $auth->getPayload()['userRole'] ?? '' ?></p>
<p>Hello from dashboard</p>

<form method="post">
    <button type="submit" name="logout">Logout</button>
</form>