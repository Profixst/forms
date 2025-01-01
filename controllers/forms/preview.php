<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('profixs/forms/forms') ?>"><?= trans('profixs.forms::lang.system.labels.forms'); ?></a></li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <div class="form-preview">
        <?= $this->formRenderPreview() ?>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('profixs/forms/forms') ?>" class="btn btn-default"><?= trans('profixs.forms::lang.system.buttons.return_to_forms_list'); ?></a></p>

<?php endif ?>
