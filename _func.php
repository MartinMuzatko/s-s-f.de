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
