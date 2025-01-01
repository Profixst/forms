<?php Block::put('breadcrumb') ?>
    <ul>
        <li>
            <a href="<?= Backend::url('profixs/forms/forms') ?>">
                <?= trans('profixs.forms::lang.system.labels.forms'); ?>
            </a>
        </li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?php Block::put('form-contents'); ?>

        <div class="layout-row min-size">
            <?= $this->formRenderOutsideFields(); ?>
        </div>
        <div class="layout-row">
            <?= $this->formRenderPrimaryTabs(); ?>
        </div>

        <div class="form-buttons">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="Saving Form..."
                    class="btn btn-primary">
                    <?= trans('profixs.forms::lang.system.buttons.save'); ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="Saving Form..."
                    class="btn btn-default">
                    <?= trans('profixs.forms::lang.system.buttons.save_and_close'); ?>
                </button>
                <button
                    type="button"
                    class="oc-icon-trash-o btn-icon danger pull-right"
                    data-request="onDelete"
                    data-load-indicator="Deleting Form..."
                    data-request-confirm="Delete this form?">
                </button>
                <span class="btn-text">
                    <?= trans('profixs.forms::lang.system.labels.or'); ?>
                    <a href="<?= Backend::url('profixs/forms/forms') ?>">
                        <?= trans('profixs.forms::lang.system.buttons.cancel'); ?>
                    </a>
                </span>
            </div>
        </div>

    <?php Block::endPut() ?>

    <?php Block::put('form-sidebar') ?>
        <div class="hide-tabs"><?= $this->formRenderSecondaryTabs() ?></div>
    <?php Block::endPut() ?>

    <?php Block::put('body') ?>
        <?= Form::open([
            'class' => 'layout stretch',
        ]) ?>
            <?= $this->makeLayout('form-with-sidebar') ?>
        <?= Form::close() ?>
    <?php Block::endPut() ?>


<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p>
        <a href="<?= Backend::url('profixs/forms/forms') ?>" class="btn btn-default">
            <?= trans('profixs.forms::lang.system.buttons.return_to_forms_list'); ?>
        </a>
    </p>

<?php endif ?>

