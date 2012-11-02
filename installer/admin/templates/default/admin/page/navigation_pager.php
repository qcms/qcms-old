<div class="f-pager navigation-pager">
    <ul class="navigation-pager-ul">
        {if:navigation_pager_prev}
        <li class="f-pager-prev"><a href="{variable:navigation_pager_prev}">{lang_message:NAVIGATION_PAGER_PREV}</a></li>
        {else:navigation_pager_prev}
        <li class="f-pager-prev"><span>{lang_message:NAVIGATION_PAGER_PREV}</span></li>
        {endif:navigation_pager_prev}
        {variable:navigation_pager_items}
        {if:navigation_pager_next}
        <li class="f-pager-next"><a href="{variable:navigation_pager_next}">{lang_message:NAVIGATION_PAGER_NEXT}</a></li>
        {else:navigation_pager_next}
        <li class="f-pager-next"><span>{lang_message:NAVIGATION_PAGER_NEXT}</span></li>
        {endif:navigation_pager_next}
    </ul>
</div><!-- f-pager -->