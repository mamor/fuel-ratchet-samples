<?php if ($errors = Session::get_flash('errors')): ?>
<div class="alert alert-error">
<button class="close" data-dismiss="alert">×</button>
<p><?php echo implode('</p><p>', $errors); ?></p>
</div>
<?php endif; ?>

<?php echo Form::open(); ?>
<?php echo Form::input('username', Input::post('username'), array('placeholder' => 'Your name ...', 'maxlength' => '10')); ?>
<br />
<?php echo Form::hidden(Config::get('security.csrf_token_key'), Security::fetch_token()); ?>
<?php echo Form::submit('submit', 'Join', array('class' => 'btn')); ?>
<?php echo Form::close(); ?>
