{if:navigation_pager_item_active}
<li class="active"><strong>{variable:navigation_pager_item_name}</strong></li>
{else:navigation_pager_item_active}
<li><a href="{variable:navigation_pager_item_href}">{variable:navigation_pager_item_name}</a></li>
{endif:navigation_pager_item_active}

