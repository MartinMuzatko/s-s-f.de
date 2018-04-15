<?php namespace ProcessWire;
    if (!$event->userCan('event-user-registration-edit')) {
        $session->redirect($pages->get('name=403')->url);
    }
    $paidRange = $event->getAttendeesPerDay('paid!=', 'paid');
    $registrationRange = $event->getAttendeesPerDay();
?>
<div class="content content--padded">
    <h2>Registrierungen Managen für <?=$event->title?></h2>
    <manage-registrations event="<?=$event->name?>"></manage-registrations>
    <h2>Registrierungensverlauf</h2>
    <p><span class="badge badge--warning">Registriert</span> <?=$page->getRegistrations()->count?></p>
    <p><span class="badge badge--success">Bezahlt</span> <?=$page->getRegistrations('attendeeStatus=accepted')->count?></p>
    <h3>Pro Tag</h3>
</div>
<ssf-chart series="{[<?=json_encode(array_values($registrationRange))?>, <?=json_encode(array_values($paidRange))?>]}" labels="{<?=str_replace('"', "'", json_encode(array_keys($registrationRange)))?>}"></ssf-chart>
<h3 class="content content--padded">Kumulativ</h3>
<script>
    function transformCumulative(array) {
        var sum = []
        sum[0] = array[0]
        array.reduce(function(a,b,i) {
            return sum[i] = a+b
        })
        return sum
    }
    var cumulativeSeriesReg = transformCumulative(<?=json_encode(array_values($registrationRange))?>)
    var cumulativeSeriesPaid = transformCumulative(<?=json_encode(array_values($paidRange))?>)
</script>
<ssf-chart series="{[cumulativeSeriesReg, cumulativeSeriesPaid]}" labels="{<?=str_replace('"', "'", json_encode(array_keys($registrationRange)))?>}"></ssf-chart>
