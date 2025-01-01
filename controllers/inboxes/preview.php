<?php Block::put('breadcrumb'); ?>
    <ul>
        <li>
            <a href="<?= Backend::url('profixs/forms/inboxes'); ?>">
                <?= trans('profixs.forms::lang.system.labels.inboxes'); ?>
            </a>
        </li>
        <li><?= e($this->pageTitle); ?></li>
    </ul>
<?php Block::endPut(); ?>

<?php if (!$this->fatalError): ?>

    <div class="form-preview">
        <?= $this->formRenderPreview(); ?>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError); ?></p>
    <p><a href="<?= Backend::url('profixs/forms/inboxes'); ?>" class="btn btn-default">Return to inboxes list</a></p>

<?php endif ?>

<a href="<?= Backend::url('profixs/forms/inboxes'); ?>" class="btn btn-primary oc-icon-caret-left">
    <?= trans('profixs.forms::lang.system.buttons.back'); ?>
</a>

<?php Event::fire('profixs.forms.inbox.preview', $this); ?>

<br><br>

<div class="dropdown dropdown-fixed">
    <button
        type="button"
        class="btn btn-default"
        data-toggle="dropdown">
        <?= trans('profixs.forms::lang.system.buttons.change_status'); ?>
    </button>
    <ul class="dropdown-menu">
        <?php foreach ($statuses as $key => $row) : ?>
        <li>
            <a href="#"
               data-request="onChangeStatus"
               data-request-data="status: '<?= $key; ?>'"
               data-request-confirm="<?= trans('profixs.forms::lang.system.alerts.confirm_change_status'); ?> [<?= $row; ?>]?">
                <?= trans($row); ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
