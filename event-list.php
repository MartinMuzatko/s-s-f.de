<?php namespace ProcessWire; ?>
<?if($user->hasPermission('event-user-manage')):?>
	<a href="<?=$pages->get('/events/create')->url?>" class="button button--primary">Neues Event erstellen</a>
<?endif?>
<?
	$sort = $input->get->sort ? trim($sanitizer->name($input->get->sort), ', ') : 'startDate';
	$input->whitelist('sort', $sort);
	$filter = $input->get->filter ? trim($sanitizer->name($input->get->filter), ', ') : 'all';
	$input->whitelist('filter', $filter);
	switch ($filter) {
		case 'open':
    		$eventPages = $events->getOpenEvents();
		    break;
		case 'all':
		default:
		    $eventPages = $events->getEvents();
		    break;
	}
?>
<div class="actions">
	<div class="actions__filter">
		<a class="button button--<?=$filter == 'all' ? 'secondary' : 'primary' ?>" href="<?=$page->url?>?filter=all">Alle Events</a>
        <a class="button button--<?=$filter == 'open' ? 'secondary' : 'primary' ?>" href="<?=$page->url?>?filter=open">Nur offene Events</a>
	</div>
	<div class="actions__sort">
		<a class="button button--<?=$sort == '-startDate' ? 'secondary' : 'primary' ?>" href="<?=$page->url?>?sort=-startDate">Neueste zuerst</a>
		<a class="button button--<?=$sort == 'startDate' ? 'secondary' : 'primary' ?>" href="<?=$page->url?>?sort=startDate">Älteste zuerst</a>
	</div>
</div>
<div class="event-list">
	<? foreach ($eventPages->sort($sort) as $event): ?>
		<?
			$event->guestlist = $event->getPageByModule('event-guestlist');
			$event->registration = $event->getPageByModule('event-registration');
			$event->terms = $event->getPageByModule('event-terms');
		?>
		<article class="event-list__event event" layout="row">
			<div class="event__column event__logo" hide-sm hide-md flex-gt-md="15" layout="row" layout-align="center center">
				<a href="<?=$event->url?>">
					<img src="<?=$event->logo ? $event->logo->url : ''?>" alt="<?=$event->title?>">
				</a>
			</div>
			<div class="event__column event__name" flex="100" flex-gt-md="45">
				<h2 layout="row">
					<a hide-gt-md href="<?=$event->url?>">
						<img src="<?=$event->logo ? $event->logo->url : ''?>" alt="<?=$event->title?>">
					</a>
					<a href="<?=$event->url?>">
						<?=$event->title?>
					</a>
				</h2>
				<p><?=$event->summary?></p>
				<p><?=$event->getAddress()?></p>
				<ssf-location-distance from="<?=$user->getAddress()?>" to="<?=$event->getAddress()?>"></ssf-location-distance>
				<p>
					<div>
						<div>
							Start: <?=$event->startDate?>
						</div>
						<div>
							Ende: <?=$event->endDate?>
						</div>
					</div>
				</p>
			</div>
			<div class="event__column" flex="100" flex-gt-md="40">
				<? require('./partials/event/registerstate.php') ?>
			</div>
		</article>
	<? endforeach; ?>
</div>
