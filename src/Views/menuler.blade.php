@extends('acr_menu.index')
@section('header')
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
@stop
@section('acr_menu')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Tüm Menüler</h3>
            <button onclick="mainMenuEkleForm()" class="btn btn-success" style="float: right;">Yeni Menu Ekle</button>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <table id="menuTable" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>Başlık</th>
                    <th>Roller</th>
                    <th>Link</th>
                    <th>Class</th>
                    <th>Sıra</th>
                    <th>İşlem</th>
                </tr>
                </thead>
                <tbody id="menuSatirlari">
                <?php echo $menuler ?>
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img src="icon/close48.png"/></span></button>
                    <h4 class="modal-title" id="myModalLabel">Menü Düzenle</h4>
                </div>
                <div id="menuModalIcerik" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>

                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <?php
    $encrypter = app('Illuminate\Encryption\Encrypter');
    $encrypted_token = $encrypter->encrypt(csrf_token());
    ?>
    <input id="token" type="hidden" value="{{$encrypted_token}}">
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->

    <script>
        $(function () {
            $('#menuTable').DataTable({
                "language": {
                    "sProcessing" : "İşleniyor...",
                    "lengthMenu"  : "Sayfada _MENU_ satır gösteriliyor",
                    "zeroRecords" : "Eşleşen sonuç yok",
                    "info"        : "Toplam _PAGES_ sayfadan _PAGE_. sayfa gösteriliyor",
                    "infoEmpty"   : "Gösterilecek öğe yok",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "search"      : "Arama yap",
                    "oPaginate"   : {
                        "sFirst"   : "İlk",
                        "sPrevious": "Önceki",
                        "sNext"    : "Sonraki",
                        "sLast"    : "Son"
                    }

                }
            });
        });
        function acrMenuDuzenle(id) {
            var $_token = $('#token').val();
            $.ajax({
                headers: {'X-XSRF-TOKEN': $_token},
                type   : 'post',
                url    : '/acr/menu/duzenle',
                data   : 'id=' + id,
                success: function (veri) {
                    $('#menuModalIcerik').html(veri);

                }
            })
            $('#menuModal').modal('show')

        }
        function mainMenuEkleForm() {
            var $_token = $('#token').val();
            $.ajax({
                headers: {'X-XSRF-TOKEN': $_token},
                type   : 'post',
                url    : '/acr/menu/duzenle',
                success: function (veri) {
                    $('#menuModalIcerik').html(veri);

                }
            })
            $('#menuModal').modal('show')
        }
        function mainMenuSiraAzalt(id) {
            var $_token = $('#token').val();
            $.ajax({
                headers: {'X-XSRF-TOKEN': $_token},
                type   : 'post',
                data   : 'id=' + id,
                url    : 'mainMenuSiraAzalt',
                success: function (veri) {
                    $('#sira' + id).html(veri);
                }
            })

        }
        function mainMenuSiraArttir(id) {
            var $_token = $('#token').val();
            $.ajax({
                headers: {'X-XSRF-TOKEN': $_token},
                type   : 'post',
                data   : 'id=' + id,
                url    : 'mainMenuSiraArttir',
                success: function (veri) {
                    $('#sira' + id).html(veri);
                }
            })
        }
        function menuKaydet(id) {
            var $_token = $('#token').val();
            var $_name = $('#name' + id).val();
            var $_link = $('#link' + id).val();
            var $_role_id = $('#role_id_' + id).val();
            var $_class = $('#class' + id).val();
            var parent_id = $('#parent_id' + id).val();
            $.ajax({
                type   : 'post',
                headers: {'X-XSRF-TOKEN': $_token},
                url    : '/acr/menu/update',
                data   : 'id=' + id + '&link=' + $_link + '&class=' + $_class + '&parent_id=' + parent_id + '&role_id=' + $_role_id + '&name=' + $_name,
                success: function (veri) {
                    $('#menuSatir' + id).html(veri);
                    $('#menuSatir' + id).addClass('bg-success');
                    $('#menuModal').modal('hide')
                    if (id == 0) {
                        $('#menuSatirlari').append(veri);
                    }
                }
            })
        }
        function mainMenuSil(id) {
            if (confirm('Bu menüyü silmek istediğinizden emin misiniz?') == true) {
                var $_token = $('#token').val();
                $.ajax({
                    headers: {'X-XSRF-TOKEN': $_token},
                    type   : 'post',
                    url    : '/acr/menu/delete',
                    data   : 'id=' + id,
                    success: function (veri) {
                        $('#menuSatir' + id).hide(400);
                    }
                })
            }
        }

    </script>
@stop