<?php

namespace App\Enum;

enum Status: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in progress';
    case DONE = 'done';
}
