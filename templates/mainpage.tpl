{extends file='base.tpl'}

{block name='title' prepend}The main page - {/block}

{block name='content'}
    <nav>
        <h3>Try these pages</h3>
        <ul>
            <li><a href="/category/">Categories</a></li>
            <li><a href="/product/1/">Product #1</a></li>
        </ul>
    </nav>
    <p>It's the main page</p>
{/block}
