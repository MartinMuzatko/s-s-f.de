<?php namespace ProcessWire;
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Website">
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?=$page->title?> | <?=$homepage->title?> - <?=$homepage->summary?></title>
    <meta name="description" content="<?=$page->summary?>" />

    <meta name="google-signin-client_id" content="953795431714-qpap9svob48gbto79f3sun0dfir2apck.apps.googleusercontent.com">

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$page->title?>">
    <meta itemprop="description" content="<?=$page->summary?>">
    <meta itemprop="image" content="http://www.example.com/image.jpg">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@<?=$author->twitterHandle?>">
    <meta name="twitter:title" content="<?=$page->title?>">
    <meta name="twitter:description" content="<?=$page->summary?>">
    <meta name="twitter:creator" content="@<?=$author->twitterHandle?>">
    <!-- Twitter Summary card images must be at least 120x120px -->
    <meta name="twitter:image" content="<?//$page->image->first->httpUrl?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?=$page->title?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?=$page->httpUrl?>" />
    <meta property="og:image" content="http://example.com/image.jpg" />
    <meta property="og:description" content="<?=$page->summary?>" />
    <meta property="og:site_name" content="<?=$homepage->title?> - <?=$homepage->summary?>" />
    <meta property="og:locale" content="de_DE" />
    <meta property="article:published_time" content="<?=date('c', $page->published)?>" />
    <meta property="article:modified_time" content="<?=date('c', $page->modifed)?>" />

    <link rel="stylesheet" type="text/css" href="<?=$config->urls->templates?>dist/css/main.css" />

    <!-- FavIcons -->
    <link rel="icon" type="image/png" href="<?php //$homepage->image->get('name%=dark')->size(128,128)->httpUrl?>" />
</head>
<body claass="<?=$page->template->name?>">
    <div class="site__container">
        <header class="site__header">
            <div class="site__logo">
                <img src="<?=$homepage->logo->height(160)->url?>" alt="">
                <span class="site__title">SSF - Die Südstaaten Furs</span>
            </div>
            <nav layout="row" layout-align="space-between" class="site__nav navigation">
                <div layout="row">
                    <? foreach($homepage->and($homepage->children) as $child):?>
                        <a class="button navigation__item" href="<?=$child->url?>"><?=$child->title?></a>
                    <? endforeach;?>
                </div>
                <div layout="row" layout-align="center center" class="navigation--item">
                    <?if($user->isLoggedin()):?>
                        <user-profile name="<?=$user->name?>" avatar="<?=$user->avatar->first->size(128,128)->url?>">
                            <div>
                                <?=$user->name?> <a ref="logout" href="<?=$pages->get('/user/logout')->url?>">logout</a>
                            </div>
                        </user-profile>
                    <?else:?>
                        <a class="button navigation__item" href="<?=$pages->get('/user/login')->url?>"><?=$pages->get('/user/login')->title?></a>|
                        <a class="button navigation__item" href="<?=$pages->get('/user/register')->url?>"><?=$pages->get('/user/register')->title?></a>
                    <?endif?>
                </div>
            </nav>
            <? if($page != $homepage && $page->children->count): ?>
                Children:
                <nav class="site__nav">
                    <? foreach($page->children as $child):?>
                        <a class="button" href="<?=$child->url?>"><?=$child->title?></a>
                    <? endforeach;?>
                </nav>
            <? endif; ?>
            <? if($page != $homepage && $page->siblings->count): ?>
                Siblings:
                <nav class="site__nav">
                    <? foreach($page->siblings as $child):?>
                        <a class="button" href="<?=$child->url?>"><?=$child->title?></a>
                    <? endforeach;?>
                </nav>
            <? endif; ?>
        </header>
        <!-- <a href="" class="button button-dark">Miau</a> -->
        <main>
            <article>
                <h1><?=$page->title?></h1>
                <br>
                <?=$content?>
            </article>
            <a href="<?=$page->editUrl?>">Edit <?=$page->title?></a>
        </main>
        <footer>

        </footer>
    </div>
    <? if(!$user->isLoggedin()): ?>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-52989130-5', 'auto');
            ga('send', 'pageview');
        </script>
    <? endif; ?>
    <script>
        window.api = <?=json_encode(
            [
                "user" => $user->name,
                "url" => $config->urls->root
            ]
        )?>
    </script>
    <script src="<?=$config->urls->templates?>dist/main.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
</body>
</html>
