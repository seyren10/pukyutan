<?php

namespace App\Enums;

enum GroupShareStatus: string
{
    case PENDING = "pending";
    case ACCEPTED = "accepted";
    case REJECTED = "rejected";
}
