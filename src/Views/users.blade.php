@extends('acr_menu.index')
@section('header')

@stop
@section('acr_menu')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h4 style="float:left;">Tüm Üyeler</h4>
                        <form class="search-form" style="float: right; width: 200px;" method="POST" action="/acr/menu/users/search">
                            {{csrf_field()}}
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search">
                                <div class="input-group-btn">
                                    <button type="submit" name="submit" class="btn btn-danger btn-flat"><i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.input-group -->
                        </form>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-hover">
                            <tr>
                                <th>UserID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Şifre</th>
                                <th>Role</th>
                            </tr>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td><a href="/admin/user/login?user_id={{$user->id}}">{{$user->name}}</a></td>
                                    <td>
                                        {{$user->email}}<br>
                                        {{$user->username}}
                                    </td>
                                    <td>
                                        <div style="position: relative;">
                                            <div id="password_mask_{{$user->id}}" style="float: left; width: 150px;">******</div>
                                            <input onFocusOut="change_user_pw({{$user->id}})" style="display: none; float:left; width: 150px;" type="text" value="{{$user->pass}}" placeholder="Enter Password"
                                                   id="password_{{$user->id}}" class=" form-control">
                                            <div onclick="pw_goster({{$user->id}})" style="float: right; font-size: 24px;"><span class="glyphicon glyphicon-eye-open"></span></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" data-toggle="buttons">
                                            @foreach($roles as $role)
                                                <label class="btn btn-primary {{in_array($role->id, $role_ids[$user->id])? 'active':''}} ">
                                                    <input class="user_role_input" {{in_array($role->id, $role_ids[$user->id])? 'checked':''}} autocomplete="off" value="{{$user->id.'_'.$role->id}}" type="checkbox"/>
                                                    {{$role->name}}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                        {{$users->appends(['search'=>$s])->links()}}
                    </div>

                </div>
            </div>
        </div>
    </section>

@stop
@section('footer')
    <script>

        function change_user_pw(user_id) {
            var pw = $('#password_' + user_id).val();
            $.ajax({
                type: 'post',
                url : '/acr/menu/users/change/pw',
                data: 'user_id=' + user_id + '&pw=' + pw,
            });
        }

        $('.user_role_input').change(function () {
            user_role = $(this).val();
            county_get(user_role);
        });
        function county_get(city_id) {
            $.ajax({
                type   : 'post',
                url    : '/acr/menu/users/role/update',
                data   : 'user_role=' + user_role,
                success: function () {

                }
            });
        }

        function pw_goster(user_id) {
            $('#password_mask_' + user_id).toggle();
            $('#password_' + user_id).toggle();

        }
    </script>
@stop