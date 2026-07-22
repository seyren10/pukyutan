<?php

namespace App\Enums;

enum GroupStatus: string
{
    case ACTIVE = "active";
    case DRAFT = "draft";
    case COMPLETED = "completed";
}
