@extends('acr_menu.index')
@section('header')
    <style>
        .password + .unmask {
            position: absolute;
            right: 74px;
            top: 12px;
            text-indent: -9999px;
            width: 25px;
            height: 25px;
            background: #aaa;
            border-radius: 50%;
            cursor: pointer;
            border: none;
            -webkit-appearance: none;
        }

        .password + .unmask:before {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 17px;
            height: 17px;
            background: #e3e3e3;
            z-index: 1;
            border-radius: 50%;
        }

        .password[type="text"] + .unmask:after {
            content: "";
            position: absolute;
            top: 6px;
            left: 6px;
            width: 13px;
            height: 13px;
            background: #aaa;
            z-index: 2;
            border-radius: 50%;
        }
    </style>
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
                                    <td><input value="{{$user->pass}}" id="pass_{{$user->id}}"/>
                                        <input type="password" value="{{$user->pass}}" placeholder="Şifre" id="password" class="password">
                                        <button class="unmask" type="button" title="Mask/Unmask password to check content">Unmask</button>
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

        function changeType(x, type) {
            if (x.prop('type') == type)
                return x; //That was easy.
            try {
                return x.prop('type', type); //Stupid IE security will not allow this
            } catch (e) {
                //Try re-creating the element (yep... this sucks)
                //jQuery has no html() method for the element, so we have to put into a div first
                var html = $("<div>").append(x.clone()).html();
                var regex = /type=(\")?([^\"\s]+)(\")?/; //matches type=text or type="text"
                //If no match, we add the type attribute to the end; otherwise, we replace
                var tmp = $(html.match(regex) == null ?
                    html.replace(">", ' type="' + type + '">') :
                    html.replace(regex, 'type="' + type + '"'));
                //Copy data from old element
                tmp.data('type', x.data('type'));
                var events = x.data('events');
                var cb = function (events) {
                    return function () {
                        //Bind all prior events
                        for (i in events) {
                            var y = events[i];
                            for (j in y)
                                tmp.bind(i, y[j].handler);
                        }
                    }
                }(events);
                x.replaceWith(tmp);
                setTimeout(cb, 10); //Wait a bit to call function
                return tmp;
            }
        }
    </script>
@stop