<?php namespace ProcessWire;

function editButton($page) {
	ob_start();
	?>
	<? if(wire('user')->isLoggedin()): ?>
        <p style="position: absolute; background: rgba(255,255,255,.25); padding: .5em; top: 0; right: 0; z-index: 100000; width: auto;">
            <a style="color: black;" href="<?=$page->editURL?>">Edit</a>
        </p>
    <? endif;
	return ob_get_clean();
}

function tags($page) {
	$tags = $page->tags;
	ob_start();
	?>
		<div class="tags">
		<?php
			foreach ($tags as $tag)
			{
				$items = wire('pages')->find('tags='.$tag->title);
				$count = $items->count;
				?>
		            <a href="<?=$tag->httpUrl?>"
                        style="background: <?=$tag->color?>">
                        <?=$tag->title?>
                        <small><?=$count?></small>
                    </a>
				<?php
			}
		?>
		</div>
	<?php
	return ob_get_clean();
}

function listingOfSeries($page, $hideLinkToParent = true) {

	$listingText = '';

	// is Parent Overview
	if ($page->parent->template->name == 'overview') {
		$listingText = $page->parent->contenttypesingular;

		// is Content Type Series
		if ($page->parent->contenttype->title == 'series') {
			$listingText = getIndex($page).'. '.$page->parent->contenttypesingular.' of ';
			if ($hideLinkToParent) {
				$listingText .= $page->parent->title;
			} else {
				$listingText .= '<a href="'.$page->parent->httpUrl.'">'.$page->parent->title.'</a>';
			}
		}
	}
	return $listingText;
}

function articlePreview($page) {
	ob_start();
	?>
		<div class="article-preview">
			<a href="<?=$page->httpUrl?>">
				<? if($page->image->first):?>
					<img src="<?=$page->image->first->size(1920, 500)->httpUrl?>" alt="">
				<? endif; ?>
				<div class="title">
					<h3><?=$page->title?></h3>
					<small><time><?=date('jS \o\f F Y', $page->published)?></time></small>
					<br><?=listingOfSeries($page)?>
					<p><?=$page->summary?></p>
				</div>
			</a>
			<p><?=tags($page)?></p>
		</div>
	<?php
	return ob_get_clean();
}

function getIndex($page)
{
	return array_search($page, explode('|', $page->siblings)) + 1;
}
