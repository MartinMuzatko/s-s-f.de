<?php namespace ProcessWire;
    $paidRange = $event->getAttendeesPerDay('paid=1');
    $registrationRange = $event->getAttendeesPerDay();
?>
<div class="content content--padded">
    <h2>Registrierungen per Datum</h2>
    <p><strong>Registriert:</strong> <?=$page->getRegistrations()->count?></p>
    <p><strong>Bezahlt:</strong> <?=$page->getRegistrations('paid=1')->count?></p>
    <ssf-chart series="{[<?=json_encode(array_values($registrationRange))?>, <?=json_encode(array_values($paidRange))?>]}" labels="{<?=str_replace('"', "'", json_encode(array_keys($registrationRange)))?>}"></ssf-chart>
    <h2>Registrierungen Managen</h2>
    <div layout="row" layout-align="space-between">
        <div flex="45">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Anzahl</th>
                        <th>€ Erwartet</th>
                        <th>€ Gesamt</th>
                        <th>€ aus Confee</th>
                        <th>€ aus Sponsor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Registrierungen</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Offen</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Bezahlt</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div flex="45">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Alle</th>
                        <th>Teilnehmer</th>
                        <th>Sponsoren</th>
                        <th>Suiter</th>
                        <th>Spotter</th>
                        <th>Helfer</th>
                        <th>Security</th>
                        <th>Staff</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Registrierungen</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Offen</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Bezahlt</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="content content--padded">
    <manage-registrations></manage-registrations>
</div>
