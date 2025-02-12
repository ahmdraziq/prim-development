@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href={{ URL::asset("assets/libs/select2/select2.min.css") }} rel="stylesheet" type="text/css">
<link href={{ URL::asset("assets/libs/spectrum-colorpicker2/spectrum.min.css") }} rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Yuran</h4>
            {{-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Murid >> Tambah Murid</li>
            </ol> --}}
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-md-12">

        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="post" action="{{ route('fees.store') }}" enctype="multipart/form-data" class="form-validation outer-repeater">
            {{ csrf_field() }}
            <div class="card-body" data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer">
                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Yuran" data-parsley-required-message="Sila masukkan nama yuran" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organizationdd" class="form-control" data-parsley-required-message="Sila pilih organisasi" required>
                            <option value="" selected>Pilih Organisasi</option>
                            @foreach ($organization as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="form-group categoryhide">
                        <label>Kategori Yuran</label>
                        <select name="category" id="category" class="select2 form-control select2-multiple"
                            multiple="multiple" multiple data-placeholder="Pilih Kategori">
                        </select>
                    </div> --}}


                    <div class="inner-repeater mb-4 form-group categoryhide">
                        <div data-repeater-list="inner-group" class="inner mb-3">
                            <label class="form-label">Kategori Yuran</label>
                            <div data-repeater-item class="inner mb-3 row">
                                <div class="col-md-10 col-sm-8">
                                    <select name="category[]" class="inner cat form-control" data-parsley-required-message="Sila pilih kategori" required>
                                        <option value="" selected>Pilih Kategori</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="d-grid">
                                        <input data-repeater-delete type="button" id="btnDelete"
                                            class="btn btn-danger inner mt-2 w-100 mt-sm-0" value="Buang" />
                                    </div>
                                </div>



                                <div class="initial">
                                    <div class="cbhidedetails form-check-inline pb-3 pt-3">

                                    </div>

                                </div>
                            </div>
                        </div>

                        <input data-repeater-create type="button" id="btnAdd" class="btn btn-success inner"
                            value="Tambah Kategori" />
                    </div>
                    {{-- 
                    <div class="categoryhide form-group">
                        <label>Kategori Yuran</label>
                        <div>
                            <select name="category" id="category" class="form-control">
                                <option value="" selected>Pilih Organisasi</option>
                            </select>
    
                            <div class="cbhidedetails form-check-inline pb-3 pt-3">
    
                            </div>
                        </div>
                        

                        <button type="button" id="btnAdd" class="btn btn-primary repeater-add-btn">Tambah
                            Kategori</button>

                    </div> --}}

                    <div class="yearhide form-group">
                        <label>Tahun</label>
                        <select name="year" id="year" class="form-control" data-parsley-required-message="Sila pilih tahun" required>
                            <option value="" selected>Pilih Tahun</option>
                        </select>
                    </div>

                    <div class="cbhide form-check-inline pb-3 pt-3">

                    </div>

                    <div class="form-group mb-0">
                        <div class="text-lg-right">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                    </>
                </div>
                <!-- /.card-body -->



        </form>
    </div>
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-repeater/jquery-repeater.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{ URL::asset('assets/libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script>
    // $(document).ready(function(){
    //     $('.cat').on('change', function(e) {

        
    //     });
    // });

    $(document).ready(function () {
        $('.form-validation').parsley();

        $("#organizationdd").prop("selectedIndex", 1).trigger('change');
    });

    $(document).on('change', '.cat', function(e) {
        console.log("cat cb" + e.target.value);
        is_changed++;
        var categoryid = e.target.value;
        var _token = $('input[name="_token"]').val();
        $('#btnAdd').show();

        if(is_clicked > 1 && is_clicked == category_length){
            $('#btnAdd').hide();
        }

        // if(is_changed > 1){

        //     var template = "<div class='new_cb_details'></div>";
        //             $('.initial').append(template);

        //     $.ajax({
        //         url: "{{ route('category.getDetails') }}",
        //         method: "POST",
        //         data: {
        //             cid: categoryid,
        //             _token: _token
        //         },
        //         success: function(result) {

        //             $('#btnAdd').show();
        //             $(".new_cb_details label").remove();
        //             $('.new_cb_details').remove();


                   
                    
        //             // $('.new_cb_details').show();
        //             // $('#cb_details').remove();
        //             // $(".cbhidedetails label").remove();
        //             $(".new_cb_details").append(
        //                 "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAllCategory' name='all' value=''/> Semua Butiran </label>"
        //             );

        //             jQuery.each(result.categorylist, function(key, value) {
        //                 $(".new_cb_details").append(
        //                     "<label for='cb_details' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingleCategory form-check-input' type='checkbox' id='cb_details' name='cb_details[]' value='" +
        //                     value.id + "'/> " + value.nama + " </label>");
        //             });
        //         }
        //     })
        // }
        // else{
        //     $.ajax({
        //         url: "{{ route('category.getDetails') }}",
        //         method: "POST",
        //         data: {
        //             cid: categoryid,
        //             _token: _token
        //         },
        //         success: function(result) {

        //             $('#btnAdd').show();

        //             $('.cbhidedetails').show();
        //             // $('#cb_details').remove();
        //             $(".cbhidedetails label").remove();
        //             $(".cbhidedetails").append(
        //                 "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAllCategory' name='all' value=''/> Semua Butiran </label>"
        //             );

        //             jQuery.each(result.categorylist, function(key, value) {
        //                 $(".cbhidedetails").append(
        //                     "<label for='cb_details' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingleCategory form-check-input' type='checkbox' id='cb_details' name='cb_details[]' value='" +
        //                     value.id + "'/> " + value.nama + " </label>");
        //             });
        //         }
        //     })
        // }
            
    });

    // $(document).ready(function(){
        
        $('#btnAdd').hide();
        // $('#btnDelete').hide();

        $('.categoryhide').hide();
        $('.cbhidedetails').hide();
        $('.yearhide').hide();
        $('.cbhide').hide();

        // var organizationid = $("#organizationdd option:selected").val();

        // $("#organizationdd").prop("selectedIndex", 1).trigger('change');

        var organization_id ;
        var is_clicked = 0;
        var is_changed = 0;
        var category_length = 0;
        var is_clicked_add = 0;


        // ************************** checkbox category ********************************

        $(document).on('change', '#checkedAllCategory', function() {
            if (this.checked) {
                $(".checkSingleCategory").each(function() {
                    this.checked = true;
                })
            } else {
                $(".checkSingleCategory").each(function() {
                    this.checked = false;
                })
            }
        });

        // ************************** checkbox category ********************************

        $(document).on('change', '.checkSingleCategory', function() {
            // console.log('asdf');
            // $('#cb_class').not(this).prop('checked', this.checked);
            if ($(this).is(":checked")) {
                var isAllChecked = 0;
                $(".checkSingleCategory").each(function() {
                    if (!this.checked)
                        isAllChecked = 1;
                })
                if (isAllChecked == 0) {
                    $("#checkedAllCategory").prop("checked", true);
                }
            } else {
                $("#checkedAllCategory").prop("checked", false);
            }
        });

        // ************************** checkbox class ********************************

        $(document).on('change', '#checkedAll', function() {
            if (this.checked) {
                $(".checkSingle").each(function() {
                    this.checked = true;
                })
            } else {
                $(".checkSingle").each(function() {
                    this.checked = false;
                })
            }
        });

        // ************************** checkbox class ********************************

        $(document).on('change', '.checkSingle', function() {
            // console.log('asdf');
            // $('#cb_class').not(this).prop('checked', this.checked);
            if ($(this).is(":checked")) {
                var isAllChecked = 0;
                $(".checkSingle").each(function() {
                    if (!this.checked)
                        isAllChecked = 1;
                })
                if (isAllChecked == 0) {
                    $("#checkedAll").prop("checked", true);
                }
            } else {
                $("#checkedAll").prop("checked", false);
            }
        });

        // ************************** retrieve checkbox class ********************************
        $('#year').change(function() {
            if ($(this).val() != '') {
                var organizationid = $("#organizationdd option:selected").val();
                var year = $("#year option:selected").val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchClass') }}",
                    method: "POST",
                    data: {
                        oid: organizationid,
                        year: year,
                        _token: _token
                    },
                    success: function(result) {
                        $('.cbhide').show();
                        $('#cb_class').remove();
                        $(".cbhide label").remove();
                        $(".cbhide").append(
                            "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAll' name='all' value=''/> Semua Kelas </label>"
                        );
                        // console.log(result.success.oid);
                        jQuery.each(result.success, function(key, value) {
                            $(".cbhide").append(
                                "<label for='cb_class' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingle form-check-input' data-parsley-required-message='Sila pilih kelas' type='checkbox' id='cb_class' name='cb_class[]' value='" +
                                value.cid + "'/> " + value.cname + " </label>");
                        });
                    }
                })
            }
        });
        
        var timesChange = 0;
        $('#organizationdd').change(function(e) {
            timesChange++;
            console.log(timesChange);
            if (timesChange>1) {
                organization_id = e.target.value;
                getCategory2(organization_id);
            } else {
                organization_id = e.target.value;
                // console.log(organization_id);
                getCategory(organization_id);
            }
        });


        // ************************** retrieve dropdown category and dropdown year student ********************************

        // loopCategory(category_length);

        // function loopCategory(length){
        //     console.log("length: " + length);
        //         for(let i=1; i <= category_length; i++){
        //             $('#btnAdd').trigger('click');
        //         }
        // }

        getCategoryLengthAndLoop();

        function getCategoryLengthAndLoop(){
                var _token          = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ route('fees.fetchYear') }}",
                    method: "POST",
                    data: {
                        oid: organization_id,
                        _token: _token
                    },
                    success: function(result) {
                        let length = result.category.length;
                        
                        for(let i=1; i <= length; i++){
                            $('#btnAdd').trigger('click');
                            is_clicked_add++;
                            // $(".cat").prop("selectedIndex", 2).trigger('change');
                        }
                        
                    }
            });
        }


        function getCategory(organization_id){

            console.log(organization_id);
            var _token          = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('fees.fetchYear') }}",
                method: "POST",
                data: {
                    oid: organization_id,
                    _token: _token
                },
                success: function(result) {
                    is_clicked++;
                    category_length = result.category.length;
                    console.log("cc: "+ category_length);

                    if(is_clicked > 1 && is_clicked == category_length){

                        $('.categoryhide').show();
                        $('.cat').empty();
                        $('.yearhide').show();
                        $('#year').empty();
                        if(result.category){
                            // $("#category").append("<option value='' selected> Pilih Kategori</option>");

                            jQuery.each(result.category, function(key, value) {
                                    $(".cat").append("<option value='" + value.id + "'> " + value.nama + "</option>");
                            });

                            for(let i=0; i < category_length; i++){
                                console.log(is_clicked_add);
                                $('select[name="outer-group[0][inner-group]['+ i +'][category]"] option:eq("1")');
                                    // $("input[name*='inner-group["+ i +"]']").options.selectedIndex = 2;

                            }

                            // $('.cbhidedetails').hide();
                            if (result.success.type_org == 1 || result.success.type_org == 2) {
                                $("#year").append("<option value='' selected> Pilih Tahun</option>");
                                $("#year").append("<option value='1'>Tahun 1</option>");
                                $("#year").append("<option value='2'>Tahun 2</option>");
                                $("#year").append("<option value='3'>Tahun 3</option>");
                                $("#year").append("<option value='4'>Tahun 4</option>");
                                $("#year").append("<option value='5'>Tahun 5</option>");
                                $("#year").append("<option value='6'>Tahun 6</option>");
                            } else if (result.success.type_org == 3) {
                                $("#year").append("<option value='' selected> Pilih Tingkatan</option>");
                                $("#year").append("<option value='1'>Tingkatan 1</option>");
                                $("#year").append("<option value='2'>Tingkatan 2</option>");
                                $("#year").append("<option value='3'>Tingkatan 3</option>");
                                $("#year").append("<option value='4'>Tingkatan 4</option>");
                                $("#year").append("<option value='5'>Tingkatan 5</option>");
                                $("#year").append("<option value='6'>Tingkatan 6</option>");
                            }
                        }
                        $('#btnAdd').hide();
                        // $('#btnDelete').show();

                    }
                    else{
                        $('.categoryhide').show();
                        // $('#category').empty();
                        $('.yearhide').show();
                        $('#year').empty();
                        if(result.category){

                            // $("#category").append("<option value='' selected> Pilih Kategori</option>");
                            jQuery.each(result.category, function(key, value) {
                                $(".cat").append("<option value='" + value.id + "'> " + value.nama + "</option>");
                            });

                            if (result.success.type_org == 1 || result.success.type_org == 2) {
                                $("#year").append("<option value='' selected> Pilih Tahun</option>");
                                $("#year").append("<option value='1'>Tahun 1</option>");
                                $("#year").append("<option value='2'>Tahun 2</option>");
                                $("#year").append("<option value='3'>Tahun 3</option>");
                                $("#year").append("<option value='4'>Tahun 4</option>");
                                $("#year").append("<option value='5'>Tahun 5</option>");
                                $("#year").append("<option value='6'>Tahun 6</option>");
                            } else if (result.success.type_org == 3) {
                                $("#year").append("<option value='' selected> Pilih Tingkatan</option>");
                                $("#year").append("<option value='1'>Tingkatan 1</option>");
                                $("#year").append("<option value='2'>Tingkatan 2</option>");
                                $("#year").append("<option value='3'>Tingkatan 3</option>");
                                $("#year").append("<option value='4'>Tingkatan 4</option>");
                                $("#year").append("<option value='5'>Tingkatan 5</option>");
                                $("#year").append("<option value='6'>Tingkatan 6</option>");
                            }
                        }
                    }

                    
                    
                    
                    // $('.cbhidedetails').hide();
                    $('.cbhide').hide();
                    
                    
                }
            })
        }

        
       
</script>

<script src="{{ URL::asset('assets/js/pages/form-repeater.int.js')}}"></script>

@endsection