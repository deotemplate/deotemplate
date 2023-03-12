{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($no_follow) AND $no_follow}
	{assign var='no_follow_text' value='rel="nofollow"'}
{else}
	{assign var='no_follow_text' value=''}
{/if}

{if isset($p) AND $p}	
	{if ($n*$p) < $nb_items }
		{assign var='blogShowing' value=$n*$p}
	{else}
		{assign var='blogShowing' value=($n*$p-$nb_items-$n*$p)*-1}
	{/if}
	{if $p==1}
		{assign var='blogShowingStart' value=1}
	{else}
		{assign var='blogShowingStart' value=$n*$p-$n+1}
	{/if}
        
	<nav class="pagination">
		<div class="col-sp-12 col-xs-12 col-md-6 col-lg-4 text-md-left text-sp-center showing">		
			{if $nb_items > 1}
				{l s='Showing' mod='deotemplate'} {$blogShowingStart} - {$blogShowing} {l s='of' mod='deotemplate'} {$nb_items} {l s='items' mod='deotemplate'}	
			{else}
				{l s='Showing' mod='deotemplate'} {$blogShowingStart} - {$blogShowing} {l s='of' mod='deotemplate'} 1 {l s='item' mod='deotemplate'}
			{/if}
		</div>
		{if $start!=$stop}
			<div id="pagination{if isset($paginationId)}_{$paginationId}{/if}" class="col-sp-12 col-xs-12 col-md-6 col-lg-8">			
				<ul class="page-list clearfix text-md-right text-sp-center">
					{if $p != 1}
						{assign var='p_previous' value=$p-1}
						<li id="pagination_previous{if isset($paginationId)}_{$paginationId}{/if}" class="previous">							
							<a {$no_follow_text} rel="prev" href="{$link->goPage($requestPage, $p_previous)}">
								<i class="deo-custom-icons"></i>
								<span>{l s='Previous' mod='deotemplate'}</span>
							</a>
						</li>
					{else}
						<li id="pagination_previous{if isset($paginationId)}_{$paginationId}{/if}" class="previous disabled">							
							<a rel="prev" href="#">
								<i class="deo-custom-icons"></i>
								<span>{l s='Previous' mod='deotemplate'}</span>
							</a>
						</li>
					{/if}
					{if $start==3}
						<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
						<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 2)}">2</a></li>
					{/if}
					{if $start==2}
						<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
					{/if}
					{if $start>3}
						<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
						<li class="truncate">...</li>
					{/if}
					{section name=pagination start=$start loop=$stop+1 step=1}
						{if $p == $smarty.section.pagination.index}
							<li class="current disabled">
								<a {$no_follow_text} href="{$link->goPage($requestPage, $smarty.section.pagination.index)}">
									{$p}
								</a>
							</li>
						{else}
							<li>
								<a {$no_follow_text} href="{$link->goPage($requestPage, $smarty.section.pagination.index)}">
									{$smarty.section.pagination.index}
								</a>
							</li>
						{/if}
					{/section}
					{if $pages_nb>$stop+2}
						<li class="truncate">...</li>
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb==$stop+1}
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb==$stop+2}
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb-1)}">
								{$pages_nb-1|intval}
							</a>
						</li>
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb > 1 AND $p != $pages_nb}
						{assign var='p_next' value=$p+1}
						<li id="pagination_next{if isset($paginationId)}_{$paginationId}{/if}" class="next">						
							<a {$no_follow_text} rel="next" href="{$link->goPage($requestPage, $p_next)}">							
								<span>{l s='Next' mod='deotemplate'}</span>
								<i class="deo-custom-icons"></i>
							</a>
						</li>
					{else}
						<li id="pagination_next{if isset($paginationId)}_{$paginationId}{/if}" class="next disabled">						
							<a rel="next" href="#">	
								<span>{l s='Next' mod='deotemplate'}</span>
								<i class="deo-custom-icons"></i>
							</a>
						</li>
					{/if}
				</ul>			
			</div>
		{/if}
	</nav>	
{/if}