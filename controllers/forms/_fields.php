<?php if($this->action == 'create') : ?>
	<br>
	<b>
		<i><?= trans('profixs.forms::lang.system.labels.save_form_before_use'); ?></i>
	</b>
<?php else : ?>

<?= $this->relationRender('fields') ?>

<?php endif ?>
