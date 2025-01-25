<?php

use Framework\Session;

$success_message = Session::get_flash('success_message');
$error_message = Session::get_flash('error_message');

?>

<?php if ($success_message): ?>
    <div class="message bg-green-100 p-3 my-3">
        <?= $success_message ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="message bg-red-100 p-3 my-3">
        <?= $error_message ?>
    </div>
<?php endif; ?>