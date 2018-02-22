<?php

namespace Acr\Menu\Controllers;

use Acr\Menu\Model\AcrMenu;
use Acr\Menu\Model\AcrRole;
use Acr\Menu\Model\AcrUser;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\MesajController;
use URL;

class AcrMenuController extends Controller
{


    function change_user_pw(Request $request)
    {
        $user_model = new AcrUser();
        $sifre      = $request->pw;
        $user_model->where('id', $request->user_id)->update(['pass' => $sifre, 'password' => bcrypt($sifre)]);
    }

    function user_login(Request $request)
    {
        $user_id    = $request->user_id;
        $user_model = new AcrUser();
        $user_model->where('id', Auth::user()->id)->update([
            'son_giris' => date('Y-m-d H:i'),
            'ip' => $request->ip()
        ]);
        Auth::loginUsingId($user_id);
    }

    function menu_ara(Request $request)
    {
        return self::acr_sol_menu($request->ara);
    }

    function roles()
    {

        $user_model = new AcrUser();
        $user       = $user_model->where('id', Auth::user()->id)->with('roles')->first();
        $roleIds    = [];
        foreach ($user->roles as $role) {
            $roleIds[] = $role->id;
        }
        return $roleIds;
    }

    function index(Request $request)
    {

        return View('acr_menu::anasayfa');
    }

    function menuler()
    {
        $menu_model = new AcrMenu();
        $menu_data  = $menu_model->with('role')->get();
        $menuler    = self::menu_body($menu_data);
        return View('acr_menu::menuler', compact('menuler'));
    }

    function acr_sol_menu($ara = null)
    {
        $menu_model = new AcrMenu();
        if (Auth::check()) {
            $user_model = new AcrUser();
            $user       = $user_model->find(Auth::user()->id);
            foreach ($user->roles as $role) {
                $role_ids[] = $role->id;
            }
            if (empty($ara)) {
                $menuler = $menu_model->where('parent_id', 0)->with([
                    'altMenus' => function ($q) {
                        $q->orderBy('sira');
                    }
                ])->whereIn('role_id', $role_ids)->orderBy('sira')->get();
            } else {
                $menuler = $menu_model->where('parent_id', 0)->where('name', 'like', "%$ara%")->with([
                    'altMenus' => function ($q) use ($ara) {
                        $q->orderBy('sira');
                        $q->where('name', 'like', "%$ara%");
                    }
                ])->whereIn('role_id', $role_ids)->orderBy('sira')->get();
            }

        } else {
            if (empty($ara)) {
                $menuler = $menu_model->where('parent_id', 0)->with([
                    'altMenus' => function ($q) {
                        $q->orderBy('sira');
                    }
                ])->whereIn('role_id', [6, 8])->orderBy('sira')->get();
            } else {
                $menuler = $menu_model->where('parent_id', 0)->where('name', 'like', "%$ara%")->with([
                    'altMenus' => function ($q) use ($ara) {
                        $q->orderBy('sira');
                        $q->where('name', 'like', "%$ara%");

                    }
                ])->whereIn('role_id', [6, 8])->orderBy('sira')->get();
            }

        }

        return self::menu($menuler);
    }

    function search_form()
    {
        $search = '<div method="post" action="/acr/menu/ara_btn" class="sidebar-form">
        <div class="input-group">
          <input type="text" id="acr_menu_ara" class="form-control" placeholder="İşlemlerde Ara...">
              <span class="input-group-btn">
                <div  name="search" id="search-btn" class="btn btn-flat">
                  <i class="fa fa-search"></i>
                </div>
              </span>
        </div>
      </div>';
        return $search;
    }

    function users(Request $request)
    {
        $s          = $request->input('search');
        $user_model = new AcrUser();
        $role_model = new AcrRole();
        $roles      = $role_model->get();
        if (!empty($s)) {
            if (is_numeric($s)) {
                $users = $user_model->with('roles')->orWhere('id', $s)->paginate(50);
            } else {
                $users = $user_model->with('roles')->orWhere('id', $s)->orWhere('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('username', 'like', "%$s%")->paginate(50);

            }
        } else {
            $users = $user_model->with('roles')->paginate(50);
        }
        foreach ($users as $user) {
            $role_ids[$user->id][] = [];
            foreach ($user->roles as $role) {
                $role_ids[$user->id][] = $role->id;
            }
        }
        return View('acr_menu::users', compact('users', 'roles', 'role_ids', 's'));
    }

    function role_update(Request $request)
    {
        $user_role = explode("_", $request->input('user_role'));
        $user_id   = $user_role[0];
        $role_id   = $user_role[1];
        $sorgu     = DB::table('role_user')->where('user_id', $user_id)->where('role_id', $role_id);
        if ($sorgu->count() > 0) {
            $sorgu->delete();
        } else {
            DB::table('role_user')->insert(['user_id' => $user_id, 'role_id' => $role_id]);
        }

    }

    function menu($menuler)
    {
        if (!empty(URL::current())) {
            $url = str_replace([$_SERVER['HTTP_HOST'], 'http://', 'https://'], ['', '', ''], URL::current());
        } else {
            $url = '/';
        }
        $veri  = '<ul class="sidebar-menu tree" data-widget="tree">';
        $veri  .= '<li class="header">İŞLEMLER</li>';
        $satir = 1;
        foreach ($menuler as $menu) {
            $menuV = array();
            foreach ($menu->altMenus as $altMenu_id) {
                $menuV[] = trim($altMenu_id->link);
            }
            // $ust_id = DB::table('menu')->insertGetId(array('isim' => $menus));
            $active = in_array($url, $menuV) ? 'active' : '';
            if (count($menu->altMenus) > 0) {
                $veri .= '<li class="treeview ' . $active . '" style=" border-bottom: rgba(0, 37, 43, 1) 1px solid;">
                        <a href="#">
                            <i class="' . $menu->class . '"></i> <span>
                            ' . $menu->name . '</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">';
                foreach ($menu->altMenus as $altMenu) {
                    // DB::table('menu')->insertGetId(array('isim' => $menuT, 'ust_id' => $ust_id, 'link' => $menu));

                    $active2 = $url == trim($altMenu->link) ? 'active' : '';
                    $veri    .= '<li class="' . $active2 . '">
                                    <a href="' . $altMenu->link . '">
                                        <i class="' . $altMenu->class . '"></i>
                                        ' . $altMenu->name . '</a>
                                </li>';
                }
                $veri .= '</ul>';
                $veri .= '</li>';
            } else {
                $active2 = $url == trim($menu->link) ? 'active' : '';
                $veri    .= '<li class="' . $active2 . '" onclick="load()" style=" border-bottom: rgba(0, 37, 43, 1) 1px solid;">
                        <a href="' . $menu->link . '">
                    <i class="' . $menu->class . '"></i>
                    <span>' . $menu->name . '</span>
                    </a>
                    </li>';
            }
            $satir++;
            $menuV = '';
        }
        $veri .= '</ul>';
        $veri .= '<div id="sidebar_menu_search"></div>';
        return $veri;
    }

//d ad as dsad as das
    /*function acrMenu($tab, $datas, $parent_id = 0, $limit = 0)
    {
        $user_model = new AcrUser();
        $roles      = $user_model->where('id', Auth::user()->id)->roles()->get();
        foreach ($roles as $role) {
            $role_ids[] = $role->id;
        }
        if ($limit > 1000) return '';
        $tree = '<ul class="sidebar-menu">';
        for ($i = 0, $ni = count($datas); $i < $ni; $i++) {
            if ($datas[$i]['parent_id'] == $parent_id) {
                if (in_array($datas[$i]['role_id'], $role_ids)) {
                    $tree .= '<li class="treeview" style=" border-bottom: rgba(0, 37, 43, 1) 1px solid;" > <a href="' . $datas[$i]['link'] . '">';
                    $tree .= '';
                    $tree .= $datas[$i]['name'] . '</a>';
                    $tree .= self::acrMenu($tab, $datas, $datas[$i]['id'], $limit++);
                    $tree .= '</li>';
                }
            }
        }
        $tree .= '</ul>';
        return $tree;
    }*/

    function delete(Request $request)
    {
        $menu_model = new AcrMenu();
        $menu_model->where('id', $request->input('id'))->delete();

    }

    function menu_body($menuler)
    {
        $satir = 1;
        $veri  = '';
        foreach ($menuler as $menu) {
            $veri .= self::menu_satir($menu, $satir);
            $satir++;
        }

        return $veri;

    }

    function menu_satir($menu, $satir)
    {
        $veri = '<tr id="menuSatir' . $menu->id . '">';
        $veri .= self::menu_satir_td($menu, $satir);
        $veri .= '</tr>';
        return $veri;
    }

    function menu_satir_td($menu, $satir)
    {

        $veri = '<td>' . $satir . '</td>';
        $veri .= '<td>' . $menu->name . '</td>';
        $veri .= '<td>' . $menu->role->name . '</td>';
        $veri .= '<td>' . $menu->link . '</td>';
        $veri .= '<td>' . $menu->class . '</td>';
        $veri .= '<td>';
        $veri .= '<span onclick="mainMenuSiraAzalt(' . $menu->id . ')" style=" cursor:pointer; font-size: 26px" class="glyphicon glyphicon-triangle-bottom"></span>';
        $veri .= '<span id="sira' . $menu->id . '">' . $menu->sira . '</span>';
        $veri .= '<span onclick="mainMenuSiraArttir(' . $menu->id . ')" style=" cursor:pointer; font-size: 26px" class="glyphicon glyphicon-triangle-top"></span>';
        $veri .= '</td>';
        $veri .= '<td style=" width: 120px;">';
        $veri .= '<button onclick="acrMenuDuzenle(' . $menu->id . ')" class="btn btn-warning " style=" float: left;">DZN</button>';
        $veri .= '<button onclick="mainMenuSil(' . $menu->id . ')" class="btn btn-danger" style=" float: right;">SİL</button>';
        $veri .= '</td>';
        return $veri;
    }

    function siraCogalt(Request $request)
    {
        $menu_id    = $request->input('id');
        $menu_model = new AcrMenu();
        $menu       = $menu_model->find($menu_id);
        $menu->sira = $menu->sira + 1;
        $menu->save();
        return $menu->sira;
    }

    function siraAzalt(Request $request)
    {
        $menu_id    = $request->input('id');
        $menu_model = new AcrMenu();
        $menu       = $menu_model->find($menu_id);
        $menu->sira = $menu->sira - 1;
        $menu->save();
        return $menu->sira;
    }

    function duzenle(Request $request)
    {
        if ($request->input('id')) {
            $id = $request->input('id');
        } else {
            $id = 0;
        }
        $menu_model = new AcrMenu();
        $role_model = new AcrRole();
        $ustMenuler = $menu_model->where('parent_id', 0)->get();
        $menu       = $menu_model->where('id', $id)->first();
        $roles      = $role_model->get();
        $form       = '<label>Menü İsmi</label>';
        $form       .= '<input class="form-control" id="name' . $id . '" value="' . @$menu->name . '"/>';
        $form       .= '<label>Menü Link</label>';
        $form       .= '<input class="form-control" id="link' . $id . '" value="' . @$menu->link . '"/>';
        $form       .= '<label>Menü Roller</label>';
        $form       .= '<select class="form-control" id="role_id_' . $id . '">';
        foreach ($roles as $role) {
            @$select = $role->id == $menu->role_id ? 'selected' : '';
            @$form .= '<option ' . $select . ' value="' . @$role->id . '">' . @$role->name . '</option>';
        }
        $form .= '</select>';
        $form .= '<label>Menü Class</label>';
        $form .= '<input class="form-control" id="class' . $id . '" value="' . @$menu->class . '"/>';
        $form .= '<label>Menüye Bağla</label>';
        $form .= '<select class="form-control" id="parent_id' . $id . '">';
        $form .= '<option value="0">Ana Menü Olsun</option>';
        foreach ($ustMenuler as $ustMenu) {
            @$select = $ustMenu->id == $menu->parent_id ? 'selected' : '';
            @$form .= '<option ' . $select . ' value="' . @$ustMenu->id . '">' . @$ustMenu->name . '</option>';
        }
        $form .= '</select>';
        $form .= '<button onclick="menuKaydet(' . $id . ')" class=" btn btn-primary btn-block">KAYDET</button>';
        return $form;
    }

    function update(Request $request)
    {
        $menu_model = new AcrMenu();

        $data  = array(
            'name' => $request->input('name'),
            'link' => $request->input('link'),
            'class' => $request->input('class'),
            'parent_id' => $request->input('parent_id'),
            'role_id' => $request->input('role_id'),

        );
        $satir = '<span class="glyphicon glyphicon-refresh" class="tool" title="Sayfa Yenileme Gerektirir"></span>';
        if ($request->input('id')) {
            $id = $request->input('id');
            $menu_model->where('id', $id)->update($data);
            $menu = $menu_model->where('id', $id)->first();

            $veri = self::menu_satir_td($menu, $satir);

            return $veri;
        } else {
            $id   = $menu_model->insertGetId(($data));
            $menu = $menu_model->where('id', $id)->first();
            return self::menu_satir($menu, $satir);
        }

    }

}