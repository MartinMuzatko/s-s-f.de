<?php namespace ProcessWire;

function setPageModules($page, $data, $contentPage = false) {
	$page->of(false);
	$pages = $page->pageModules;
	//$page->pageModules->removeAll();
	// foreach ($pages as $pageModule) {
	// 	if ($pageModule)
	// 	$pageModule->delete(true);
	// }
	// page deletion has to be made as a second call BEFORE creating page with same name again.
	// $page->save();
	if (!$contentPage) {
		$contentPage = wire('pages')->get('template=page-contents');
	}
	
	foreach ($data as $key => $moduleData) {
		if(isset($moduleData->id)) {
			$module = wire('pages')->get($moduleData->id);
			$module->of(false);
			setFieldValues($moduleData, $module);
			$module->save();
		} else {
			$module = new Page();
			$module->parent = $contentPage;
			$module->of(false);
			$module->template = $moduleData->template;
			$module->title = $page->title.'_'.$moduleData->template;
			$module->save();
			$module->of(false);
			setFieldValues($moduleData, $module);
			$module->save();
			$moduleData->id = $module->id;
			$page->pageModules->add($module);
		}
	}
	$page->save();
	$page->of(false);
	
	foreach($data as $key => $moduleData) {
		// TODO: SORTING!
		// $module = wire('pages')->get($moduleData->id);
		// $module->sort = $key + 1;
		// $module->save();
	}
	$page->pageModules->save();
	foreach ($page->pageModules as $module) {
		$index = array_filter($data, function($item) use ($module) {
			return $item->id == $module->id;
		});
		if (!count($index)) {
			$module->delete(true);
		}
	}
	$page->save();
}

function setFieldValues($data, $page) {
	return array_map(
		function($key, $value) use ($page) {
			if ($page->{$key} instanceof RepeaterPageArray) {
				$page->save();
				$repeaterItem = $page->{$key}->getNew();
				//$repeaterItem->of(false);
				array_map(function($val) use ($repeaterItem) {
					setFieldValues($val, $repeaterItem);
				}, $value);
				//$repeaterItem->parent = $page;
				$repeaterItem->save();
				$page->{$key}->add($repeaterItem);
				//$page->
			} else {
				$page->{$key} = html_entity_decode($value);
			}
		},
		array_keys((array) $data),
		(array) $data
	);
}

function getFieldValues($page, $fields, $jsonSafeTransport = true) {
    return array_map(
        function($field) use ($page, $jsonSafeTransport) {
            if ($field->type instanceof FieldtypeImage) {
                $image = $page->{$field->name};
                if($image->count) {
                    $image = $image instanceof Pageimages ? $image->first->httpUrl : $image->httpUrl;
                } else {
                    $image = '';
                }
                return [$field->name => $image];
			}
			if ($field->type instanceof FieldtypePage) {
                return [$field->name => $page->{$field->name}->title];
            }
			if ($field->type instanceof FieldtypeRepeater) {
                $repeaterPages = $page->{$field->name};
                $repeaterFields = array_map(
                    function($repeaterPage) use($jsonSafeTransport) {
                        $fields = getFieldValues($repeaterPage, $repeaterPage->fields->getArray(), $jsonSafeTransport);
                        return call_user_func_array('array_merge', $fields);
                    },
                    $repeaterPages->getArray()
                );
                return [$field->name => $repeaterFields];
            }
            return [$field->name => $jsonSafeTransport ? htmlentities($page->{$field->name}) : $page->{$field->name}];
        },
        $fields
    );
}

function sendNotification($notification, $receiver, $context) {
	if (!isset($eventsystemUser)) {
		$eventsystemUser = \ProcessWire\Wire('users')->get('name=eventsystem');
	}
	$footer = processText($notification->parent->footer, $context);
	$message = new WireData();
	$message->setArray([
		'title' => processText($notification->title, $context),
		'text' => processText($notification->text, $context).$footer,
		'sender' => $eventsystemUser,
		'read' => false,
	]);
	return $receiver->sendMessage($message);
}

function processText($text, $context) {
	preg_match_all('/(\{\{[\w\.]+?\}\})/m', $text, $matches);
	foreach ($matches[0] as $match) {
		if (count($match)) {
			$attributes = explode('.', preg_replace('/[\{\}]/m', '', $match));
			$attribute = $context->{$attributes[0]}->{$attributes[1]};
			$text = str_replace($match, $attribute, $text);
		}
	}
	return $text;
}

function getAttendeeStatusLabel($status) {
	$labels = [
		'new' => 'Neu',
		'pending' => 'Zahlung ausstehend',
		'accepted' => 'Bezahlt',
		'waiting' => 'Warteliste',
		'signedoff' => 'Abgemeldet',
		'dismissed' => 'Verbannt',
	];
	return $labels[$status];
}

function getAttendeeStatusColor($status) {
	$labels = [
		'new' => 'success',
		'pending' => 'warning',
		'accepted' => 'success',
		'waiting' => 'warning',
		'signedoff' => 'error',
		'dismissed' => 'error',
	];
	return $labels[$status];
}