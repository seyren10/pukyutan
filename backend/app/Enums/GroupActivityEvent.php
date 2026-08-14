<?php

namespace App\Enums;

enum GroupActivityEvent: string
{
    case GroupActivated = 'group.activated';
    case GroupCompleted = 'group.completed';
    case RoundStarted = 'round.started';
    case CycleDisbursed = 'cycle.disbursed';
    case ShareAccepted = 'share.accepted';
    case ShareRejected = 'share.rejected';
    case ContributionRecorded = 'contribution.recorded';
}