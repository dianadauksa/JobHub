<?php if ($errors): ?>
    <div class="message bg-red-100 p-3 my-3">
        <?php foreach ($errors as $error): ?>
            <ul><?= $error ?></ul>
        <?php endforeach; ?>
    </div>
<?php endif; ?>