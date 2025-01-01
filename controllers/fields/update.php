<?php Block::put('breadcrumb') ?>
    <ul>
        <li>
            <a href="<?= Backend::url('profixs/forms/fields') ?>">
                <?= trans('profixs.forms::lang.system.labels.fields'); ?>
            </a>
        </li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?= Form::open(['class' => 'layout']) ?>

        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="Saving Field..."
                    class="btn btn-primary">
                    <?= trans('profixs.forms::lang.system.buttons.save'); ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="Saving Field..."
                    class="btn btn-default">
                    <?= trans('profixs.forms::lang.system.buttons.save_and_close'); ?>
                </button>
                <button
                    type="button"
                    class="oc-icon-trash-o btn-icon danger pull-right"
                    data-request="onDelete"
                    data-load-indicator="Deleting Field..."
                    data-request-confirm="Delete this field?">
                </button>
                <span class="btn-text">
                    <?= trans('profixs.forms::lang.system.labels.or'); ?>
                    <a href="<?= Backend::url('profixs/forms/fields') ?>">
                        <?= trans('profixs.forms::lang.system.buttons.cancel'); ?>
                    </a>
                </span>
            </div>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p>
        <a href="<?= Backend::url('profixs/forms/fields') ?>" class="btn btn-default">
            <?= trans('profixs.forms::lang.system.buttons.return_to_fields_list'); ?>
        </a>
    </p>

<?php endif ?>

