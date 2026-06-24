<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'New';
    case Discovered = 'Discovered';
    case Contacted = 'Contacted';
    case Qualified = 'Qualified';
    case Closed = 'Closed';
}
