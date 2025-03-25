<?php if($this->user->hasAccess('profixs.forms.manage_forms')) : ?>
    <div class="control-toolbar">
        <div class="toolbar-item toolbar-primary">
            <div data-control="toolbar" data-disposable="">

                <a data-control="popup"
                   data-size="huge"
                   data-handler="onRelationButtonCreate"
                   href="javascript:;"
                   class="btn btn-sm btn-secondary oc-icon-plus">
                    <?= trans('profixs.forms::lang.system.buttons.add_field'); ?>
                </a>

    			<a href="<?= Backend::url('profixs/forms/fields/reorder?form_id=' . $this->vars['formModel']->id); ?>"
                   class="btn btn-sm btn-secondary oc-icon-list">
                    <?= trans('profixs.forms::lang.system.buttons.reorder'); ?>
                </a>
                
                <button class="btn btn-sm btn-secondary oc-icon-trash-o control-disabled" onclick="$(this).data('request-data', {
            checked: $('.form-group[data-field-name=fields] .control-list').listWidget('getChecked')
        })" disabled="" data-request="onRelationButtonDelete"
                        data-request-confirm="<?= trans('profixs.forms::lang.system.alerts.confirm_delete_fields'); ?>"
                        data-trigger-action="enable"
                        data-trigger=".form-group[data-field-name=fields] .control-list input[type=checkbox]"
                        data-trigger-condition="checked"
                        data-stripe-load-indicator="">
                    <?= trans('profixs.forms::lang.system.buttons.delete'); ?>
                </button>

            </div>
        </div>        
    </div>
<?php endif ?>