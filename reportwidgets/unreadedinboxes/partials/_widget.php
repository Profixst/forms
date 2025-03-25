<div class="report-widget">
    <h3><?= trans('profixs.forms::lang.widgets.unreaded_inboxes.name'); ?></h3>

    <?php if ($count) : ?>
    	<a href="<?= Backend::url('profixs/forms/inboxes?status=news'); ?>">
    		<p class="flash-message static info">
    			<b><?= trans('profixs.forms::lang.widgets.unreaded_inboxes.statuses.new_inboxes'); ?>: <?= $count; ?></b>
    		</p>
    	</a>
    <?php else : ?>
    	<b><?= trans('profixs.forms::lang.widgets.unreaded_inboxes.statuses.no_new_inboxes'); ?></b>
    <?php endif ?>
</div>