<?php
/*
** Users Model
*
*/

declare(strict_types=1);

namespace app\models;

use app\core\Model;
use DateTime;

class Analysis extends Model
{

    protected $table = 'data__5_';

    protected $allowedColumns = [
        'user_id',
        'full_name',
        'email',
        'password',
        'token',
        'token_expires_at',
        'created_at',
    ];

    protected $beforeInsert = [
        'user_id',
        'password_hash',
    ];

    protected $beforeUpdate = [
        'password_hash',
    ];

    protected $afterSelect = [];
}
