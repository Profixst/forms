<?php namespace ProFixS\Forms\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use ProFixS\Forms\Models\Inbox;

class UnreadedInboxes extends ReportWidgetBase
{

    public function render()
    {
		$this->vars['count'] = Inbox::getUnreadedInboxCount();
			return $this->makePartial('widget');
    }
}