<?php namespace ProcessWire;

if (!$event->userCan('event-user-registration-edit')) {
    $session->redirect($pages->get('name=403')->url);
}

$registrations = $event->getRegistrations();
    
$regData = array_map(
    function ($registration, $id) use ($event) {
        $roles = array_map(
            function($role) {
                return $role->title;
            },
            $registration->attendeeRoles->getArray()
        );
        return [
            "id" => $id,
            "username" => $registration->profile->username,
            "firstname" => $registration->profile->firstname,
            "lastname" => $registration->profile->lastname,
            "species" => $registration->profile->species->title,
            "attendeeRoles" => join($roles, ', '),
            "sponsorlevel" => $event->getSponsorLevelForMoney($registration->donation)->title,
            "conFee" => $event->getAttendeePaymentSum($registration->profile),
            "comment" => $registration->comment,
        ];
    },
    $registrations->getArray(),
    array_keys((array) $registrations->getArray())
);

echo "\xEF\xBB\xBF";

echo join(
    array_map(
        function ($column) {
            return "\"$column\"";
        },
        array_keys($regData[0])
    ),
    ','
).PHP_EOL;

$lines = array_map(
    function ($registration) {
        $line = array_map(
            function ($value) {
                return "\"$value\"";
            },
            $registration
        );
        return join($line, ',');
    },
    $regData
);

echo join($lines, PHP_EOL);
header("Content-Type: text/csv;charset=UTF-8");
die;
