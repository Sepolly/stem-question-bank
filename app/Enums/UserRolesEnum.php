<?php

namespace App\Enums;

enum UserRolesEnum: string
{
    case SUPER_ADMIN = 'super admin';
    case ADMIN = 'admin';
    case CONTRIBUTOR = 'contributor';
    case VIEWER = 'viewer';
}
