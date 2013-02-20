{extends file='base.tpl'}

{block name='content'}

{if isset($request.params.category)}
    <h3>Category: {$request.params.category|ucfirst}</h3>
{/if}

{if $categories}
<h4>Categorylist</h4>
<ul>
    {foreach $categories as $c}
    <li>
        <a href="/category/{$c.name|lower}/">
            {$c.name}{if $c.desc} - {$c.desc}{/if}
        </a>
    </li>
    {/foreach}
</ul>
{/if}

{/block}
