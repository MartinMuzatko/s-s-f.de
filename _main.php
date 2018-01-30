<?php namespace ProcessWire;
$content = ob_get_clean();
$documentTitle = isset($documentTitle) ? $documentTitle : $page->title;
$documentTitle .= $isEvent && !$isEventHome ? ' - '.$event->title : '';
$documentTitle .= ' | SSF - Die SüdstaatenFurs';
?>
<!DOCTYPE html>
<!--
    Hallo lieber Source-code schnüffler :)
    Ich setze bei der Entwicklung auf freie Software wie ProcessWire und RiotJS.
    Finde mehr über mich heraus: https://happy-css.com
 -->
<html itemscope itemtype="http://schema.org/Website">
<!-- Global site tag (gtag.js) - Google Analytics -->
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$homepage->googleAnalyticsID?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?=$homepage->googleAnalyticsID?>');
    </script>
    <meta charset="utf-8">
    <link rel="manifest" href="<?=$config->urls->templates?>dist/manifest.json">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$documentTitle?></title>
    <meta name="description" content="<?=$page->summary?>" />

    <meta name="google-signin-client_id" content="953795431714-qpap9svob48gbto79f3sun0dfir2apck.apps.googleusercontent.com">

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$documentTitle?>">
    <meta itemprop="description" content="<?=$page->summary?>">
    <meta itemprop="image" content="<?=$favicon?>">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@<?=$author->twitterHandle?>">
    <meta name="twitter:title" content="<?=$documentTitle?>">
    <meta name="twitter:description" content="<?=$page->summary?>">
    <meta name="twitter:creator" content="@<?=$author->twitterHandle?>">
    <!-- Twitter Summary card images must be at least 120x120px -->
    <meta name="twitter:image" content="<?//$page->image->first->httpUrl?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?=$documentTitle?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?=$page->httpUrl?>" />
    <meta property="og:image" content="<?=$homepage->logo->size(128,128)->httpUrl?>" />
    <meta property="og:description" content="<?=$page->summary?>" />
    <meta property="og:site_name" content="<?=$documentTitle?>" />
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
            <a href="<?=$config->urls->root?>" class="site__logo">
                <img class="logo" src="<?=$homepage->logo->height(160)->url?>" alt="">
                <div class="site__title">
                    <div class="js-fitty">SSF - Die Südstaaten Furs</div>
                </div>
            </a>
            <!-- <img src="<?=$config->urls->templates?>dist/images/home.svg" alt=""> -->
            <nav layout-align="space-between" class="site__nav navigation">
                <div class="navigation__segment">
                    <? foreach($homepage->and($homepage->children) as $child):?>
                        <a class="button navigation__item" href="<?=$child->url?>">
                            <span class="navigation__image">
                                <?=file_get_contents('dist/images/menue_'.$child->name.'.svg')?>
                            </span>
                            <span class="navigation__page"><?=$child->title?></span>
                        </a>
                    <? endforeach;?>
                </div>
                <div layout="row" layout-align="center center" class="navigation--item">
                    <?if($user->isLoggedin()):?>
                        <user-profile-dropdown
                            messages="<?=$user->hasUnreadMessages()?>"
                            name="<?=$user->username?>"
                            avatar="<?=$user->getAvatar(64)?>">
                            <div class="dropdown__group">
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>"><?=__('Profil anzeigen')?></a>
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>/edit"><?=__('Profil bearbeiten')?></a>
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/<?=$user->name?>/messages"><?=__('Nachrichten')?> (<?=$user->hasUnreadMessages()?>)</a>
                                <? if($user->hasRole('admin')): ?>
                                    <a class="dropdown__item" href="<?=$config->urls->root?>admin"><?=__('Admin')?>)</a>
                                <? endif; ?>
                            </div>
                            <? if($user->hasRole('manager')): ?>
                                <div class="dropdown__group">
                                    <a class="dropdown__item" href="<?=$config->urls->root?>events"><?=__('Event Management')?></a>
                                </div>
                            <? endif; ?>
                            <div class="dropdown__group">
                                <a class="dropdown__item" href="<?=$config->urls->root?>users/logout"><?=__('Logout')?></a>
                            </div>
                        </user-profile-dropdown>
                    <?else:?>
                        <a class="button navigation__item" href="<?=$pages->get('/users/login')->url?>">
                            <span class="navigation__image">
                                <?=file_get_contents('dist/images/login.svg')?>
                            </span>
                            <span class="navigation__page"><?=$pages->get('/users/login')->title?></span>

                        </a>
                    <?endif?>
                </div>
            </nav>
        </header>
        <main class="site__content">
            <article>
                <?=$content?>
            </article>
            <? if($user->hasRole('superuser')): ?>
                <a href="<?=$page->editUrl?>">Edit <?=$page->title?></a>
            <? endif ?>
        </main>
        <footer class="site__footer" layout="row" layout-align="space-between">
            <div flex="100" flex-gt-sm="45">
                <p>SSF - Südstaaten Furs &copy; <?=strftime('%Y', time())?></p>
                <?=$homepage->footer?>
            </div>
            <div flex="100" flex-gt-sm="45" layout="row" layout-align="end start">
                <? foreach($homepage->socialmedia as $socialmedia): ?>
                    <a class="content--margin" style="border-radius: 100%; display: block; overflow: hidden; line-height: 0;" href="<?=$socialmedia->link?>"><img width="32" src="<?=$socialmedia->image->first->url?>" alt="<?=$socialmedia->title?>"></a>
                <? endforeach ?>
            </div>
        </footer>
    </div>
    <?php
        $itemPages = $pages->get('/events/resources/items')->children('include=all')->getArray();
        $imageKeys = array_map(function($item) { return $item->name; }, $itemPages);
        $imageValues = array_map(function($item) { return $item->image->first->url ; }, $itemPages);
        $itemImages = array_combine($imageKeys, $imageValues);
        // $user = $pages->find('template=user')->getRandom();
        $api = [
            "user" => [
                "name" => $user->name,
                "username" => $user->username,
                "clubMemberID" => $user->clubMemberID,
                "avatar" => $user->getAvatar(),
            ],
            "url" => $config->urls->root,
            "images" => $itemImages
        ];
    ?>
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
    <script>
        window.api = {}
        Object.assign(window.api, <?=json_encode($api)?>)
    </script>
    <script src="<?=$config->urls->templates?>dist/main.js"></script>
    <script>
        function googleSignIn(googleUser) {
            api.google.trigger('signIn', googleUser)
        }
        function initApi(map) {
            api.google.trigger('initApi', map)
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=$homepage->googleMapsAPIKey?>&callback=initApi" async defer></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
</body>
</html>
