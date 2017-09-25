<?php namespace ProcessWire;
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Website">
<head>
    <meta charset="utf-8">
    <link rel="manifest" href="<?=$config->urls->templates?>dist/manifest.json">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?=$page->title?> | <?=$homepage->title?> - <?=$homepage->summary?></title>
    <meta name="description" content="<?=$page->summary?>" />

    <meta name="google-signin-client_id" content="953795431714-qpap9svob48gbto79f3sun0dfir2apck.apps.googleusercontent.com">

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$page->title?>">
    <meta itemprop="description" content="<?=$page->summary?>">
    <meta itemprop="image" content="<?=$favicon?>">

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
    <meta property="og:image" content="<?=$homepage->logo->size(128,128)->httpUrl?>" />
    <meta property="og:description" content="<?=$page->summary?>" />
    <meta property="og:site_name" content="<?=$homepage->title?> - <?=$homepage->summary?>" />
    <meta property="og:locale" content="de_DE" />
    <meta property="article:published_time" content="<?=date('c', $page->published)?>" />
    <meta property="article:modified_time" content="<?=date('c', $page->modifed)?>" />

    <link rel="stylesheet" type="text/css" href="<?=$config->urls->templates?>dist/css/main.css" />
    <!-- FavIcons -->
    <link rel="icon" type="image/png" href="<?=$favicon?>" />
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
                        <user-profile-dropdown messages="<?=$user->hasUnreadMessages()?>" name="<?=$user->username?>" avatar="<?=$user->avatar instanceof Pageimages ? $user->avatar->first->size(64,64)->url : $user->avatar->size(64,64)->url?>">
                            <div class="dropdown__group">
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>">Profil anzeigen</a>
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>/edit">Profil bearbeiten</a>
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>/messages">Nachrichten (<?=$user->hasUnreadMessages()?>)</a>
                                <a class="dropdown__item" href="<?=$config->urls->root?>admin">Admin</a>
                            </div>
                            <div class="dropdown__group">
                                <a class="dropdown__item" href="<?=$config->urls->root?>events">Event Management</a>
                            </div>
                            <div class="dropdown__group">
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/logout">Logout</a>
                            </div>
                        </user-profile-dropdown>
                    <?else:?>
                        <a class="button navigation__item" href="<?=$pages->get('/users/login')->url?>"><?=$pages->get('/users/login')->title?></a>
                    <?endif?>
                </div>
            </nav>
        </header>
        <!-- <a href="" class="button button-dark">Miau</a> -->
        <main class="site__content">
            <article>
                <h1><?=$page->title?></h1>
                <br>
                <?=$content?>
            </article>
            <a href="<?=$page->editUrl?>">Edit <?=$page->title?></a>
        </main>
        <footer class="site__footer">
            SSF - Südstaaten Furs &copy; 2017
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
        window.api = {}
        Object.assign(window.api, <?=json_encode(
            [
                "user" => $user->name,
                "url" => $config->urls->root
            ]
        )?>)
    </script>
    <script src="<?=$config->urls->templates?>dist/main.js"></script>
    <script>
        function googleSignIn(googleUser) {
            api.google.trigger('signIn', googleUser)
        }
        function initMap(map) {
            api.google.trigger('mapInit', map)
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=$homepage->googleMapsAPIKey?>&callback=initMap" async defer></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
</body>
</html>
