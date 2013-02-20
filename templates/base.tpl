<!doctype html>
<head>
    <title>{block name="title"}My site{/block}</title>
    <style type="text/css">
        body {
            margin: 0;
        }
        #content {
            overflow: hidden;
        }
        h1 {
            margin: 0;
            padding: 15px 0;
        }
        nav {
            background-color: #eee;
            float: right;
            padding: 15px;
        }
        header {
            background-color: #ccc;
        }
        footer {
            background-color: #ccc;
            margin: 0;
            padding: 15px 0;
        }
    </style>
</head>
<body>
    <header>
        {block name="header"}
        <h1><a href="/">Logo</a></h1>
        {/block}
    </header>

    <div id="content">
        {block name="content"}
        Placeholder content
        {/block}
    </div>

    <footer>
        {block name="footer"}
        <ul>
            <li><a href="/about">About</a></li>
        </ul>
        {/block}
    </footer>
</body>
