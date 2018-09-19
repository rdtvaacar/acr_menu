<?php

namespace Acr\Menu\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class AcrMenu extends Model

{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    function altMenus()
    {
        return $this->hasMany('Acr\Menu\Model\AcrMenu', 'parent_id', 'id');
    }

    function role()
    {
        return $this->belongsTo('Acr\Menu\Model\AcrRole');
    }

    function ust_menu()
    {
        return $this->hasOne('Acr\Menu\Model\AcrMenu', 'id', 'parent_id');
    }
}
