<?php

namespace Acr\Menu\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;
use DB;

class AcrRole extends Model

{
    protected $table = 'roles';

    protected $connection = 'mysql4';
    /**
     * The database table used by the model.
     *
     * @var string
     */
}
