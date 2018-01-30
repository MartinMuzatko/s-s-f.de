<?php namespace ProcessWire;?>
<div class="content content--padded">
    <?
        $members = $users->find('clubMemberID!=, sort=clubMemberID')
    ?>
    <table>
        <thead>
            <tr>
                <th><?=__('Avatar')?></th>
                <th><?=__('Nick')?></th>
                <th><?=__('Mitglied seit')?></th>
                <th><?=__('Aufgabengebiet')?></th>
                <th><?=__('Funktion')?></th>
            </tr>
        </thead>
        <tbody>
                <? foreach($members as $member): ?>
                    <tr>
                        <td><img class="avatar avatar--small avatar--round" src="<?=$member->getAvatar()?>" alt=""></td>
                        <td><a href="<?=$config->urls->root?>users/<?=$member->name?>"><?=$member->username?></a></td>
                        <td><?=$member->clubEntryDate?></td>
                        <td><?=$member->clubMembershipType?></td>
                        <td><?=$member->clubFunction?></td>
                    </tr>
                <? endforeach; ?>
        </tbody>
    </table>
</div>
