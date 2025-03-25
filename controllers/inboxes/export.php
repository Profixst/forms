
<?= Form::open([
    'url' => Backend::url('profixs/forms/inboxes/download-item/'),
'method' => 'POST'
]); ?>
<label><?= trans('profixs.forms::lang.system.labels.download_inboxes_for_period'); ?></label>
<?= $formWidget->render(); ?>

<div class="form-buttons">
    <div class="loading-indicator-container">
        <button
            type="submit"
            class="btn btn-primary">
            <?= trans('profixs.forms::lang.system.buttons.download'); ?>
        </button>
    </div>
</div>
<?= Form::close(); ?>
