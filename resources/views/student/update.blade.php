@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Murid</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Murid >> Edit Murid</li>
            </ol>
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
        <form method="post" action="{{ route('student.update', $student->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        @if($row->id == $student->organization_id)
                        <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div id="dkelas" class="form-group">
                    <label> Nama Kelas</label>
                    <select name="classes" id="classes" class="form-control">
                        <option value="" disabled selected>Pilih Kelas</option>
                        @foreach($listclass as $row)
                        @if($row->id == $student->classid)
                        <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Penuh</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Penuh"
                        value="{{ $student->studentname }}">
                </div>
                <div class="form-group">
                    <label>Nombor Kad Pengenalan</label>
                    <input type="text" name="icno" class="form-control" placeholder="Nombor Kad Pengenalan"
                        value="{{ $student->icno }}">
                </div>
                <div class="form-group">
                    <label>Jantina</label>
                    <div class="radio">
                        <label class="radio-inline pl-2"><input type="radio" name="gender" value="M"
                                {{ ($student->gender =="L")? "checked" : "" }}> Lelaki </label>
                        <label class="radio-inline pl-2"><input type="radio" name="gender" value="F"
                                {{ ($student->gender =="P")? "checked" : "" }}> Perempuan </label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->


        </form>
    </div>
</div>

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>


<script>
    $(document).ready(function(){
        
        $('#organization').change(function(){
        
                // $('#kelas').val('');
                // $('#murid').val('');
                
        });

            $('#organization').change(function() {
               
                if($(this).val() != '')
                {
                    var organizationid    = $("#organization option:selected").val();
                    var _token            = $('input[name="_token"]').val();
                    $.ajax({
                        url:"{{ route('parent.fetchClass') }}",
                        method:"POST",
                        data:{ oid:organizationid,
                                _token:_token },
                        success:function(result)
                        {
                             
                            $('#classes').empty();
                            $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                            jQuery.each(result.success, function(key, value){
                                // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');

                                $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                            });
                        }

                    })
                }
            });
        });
</script>
@endsection