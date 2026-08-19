<?php

namespace App\Enums;

enum NotificationType: string
{
    case ShareRequested = 'share.requested';
    case ShareResponded = 'share.responded';
    case ShareRevoked = 'share.revoked';
    case CycleReminder = 'cycle.reminder';
}
