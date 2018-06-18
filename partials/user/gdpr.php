<?php namespace ProcessWire;
if (!$isMe && !$isAdmin) {
    $session->redirect($pages->get('name=404')->url);
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->of(false);
    $user->hasReadPrivacyPolicy = true;
    $user->save();
    $session->redirect($pages->get('/')->url.$profileUrl);
}

?>
<div class="content--padded">
    <user-gdpr></user-gdpr>
    <p class="notification notification--warning">
        Auskunft, Berichtigung, Löschung und Sperrung, Widerspruchsrecht Nach <a target="_blank" href="https://dsgvo-gesetz.de/art-15-dsgvo/">§ 15 DSGVO</a> bist Du jederzeit berechtigt, gegenüber
        dem Südstaaten Furs Verein um umfangreiche Auskunftserteilung zu den zu Deiner Person gespeicherten Daten zu ersuchen.
        Gemäß <a target="_blank" href="https://dsgvo-gesetz.de/art-17-dsgvo/">§ 17 DSGVO</a> kannst Du jederzeit 
        gegenüber dem SüdstaatenFurs Verein die Berichtigung, Löschung und Sperrung einzelner personenbezogener Daten verlangen.
        Du kannst darüber hinaus jederzeit ohne Angabe von Gründen von Deinem Widerspruchsrecht Gebrauch machen und die erteilte Einwilligungserklärung mit Wirkung für die Zukunft abändern oder gänzlich widerrufen. 
        Du kannst den Widerruf entweder postalisch, per E-Mail oder per Fax an den SüdstaatenFurs Verein übermitteln. 
        Es entstehen Dir dabei keine anderen Kosten als die Portokosten bzw. die Übermittlungskosten nach den bestehenden Basistarifen.
    </p>
</div>
