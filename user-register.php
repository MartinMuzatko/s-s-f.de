<?php namespace ProcessWire; ?>
<script>
var postdata = <?=json_encode($input->post->getArray())?>
</script>
<user-register postdata="{postdata}"></user-register>
