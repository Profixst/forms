<?php namespace ProFixS\Forms\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use ProFixS\Forms\Models\Inbox;

/**
 * UnreadedInboxes Report Widget
 *
 * @link https://docs.octobercms.com/3.x/extend/backend/report-widgets.html
 */
class UnreadedInboxes extends ReportWidgetBase
{

     public function render()
    {
            $this->vars['count'] = Inbox::getUnreadedInboxCount();
            return $this->makePartial('widget');
        }
}

