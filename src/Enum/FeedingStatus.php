<?php

namespace App\Enum;

enum FeedingStatus: string
{
    case Ok = 'ok';
    case DueSoon = 'due_soon';
    case Overdue = 'overdue';
}
