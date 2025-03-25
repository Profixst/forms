<div data-control="toolbar">
    <a
        href="<?= Backend::url('profixs/forms/inboxes/export') ?>"
        class="btn btn-primary oc-icon-plus">
        <?= trans('profixs.forms::lang.system.buttons.export'); ?>
    </a>
    <button
        class="btn btn-danger oc-icon-trash-o"
        disabled="disabled"
        onclick="$(this).data('request-data', { checked: $('.control-list').listWidget('getChecked') })"
        data-request="onDelete"
        data-request-confirm="<?= trans('profixs.forms::lang.system.alerts.confirm_delete_inboxes'); ?>"
        data-trigger-action="enable"
        data-trigger=".control-list input[type=checkbox]"
        data-trigger-condition="checked"
        data-request-success="$(this).prop('disabled', 'disabled')"
        data-stripe-load-indicator>
        <?= trans('profixs.forms::lang.system.buttons.delete_selected'); ?>
    </button>
</div>
