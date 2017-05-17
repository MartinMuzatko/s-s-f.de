<?php namespace ProcessWire;?>

<?php foreach ($page->children as $member): ?>
    <?=$member->profile->name?>
<?php endforeach; ?>
