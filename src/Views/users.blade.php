@extends('acr_menu.index')
@section('header')
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
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
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
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
    </script>
@stop