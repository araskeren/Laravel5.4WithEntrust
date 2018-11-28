@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="">
            <div class="panel panel-default">
                <div class="panel-heading">Permission</div>

                <div class="panel-body">
                    <div class="row">
                    {!! Form::open(['url' => route('auth.permission.store'),'method' => 'post']) !!}
                        {{Form::label('name', 'Nama Permission')}}
                        {{Form::text('name')}}

                        {{Form::label('description', 'Description')}}
                        {{Form::text('description')}}

                        {{Form::submit('TAMBAH')}}
                    {!! Form::close() !!}
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            {!! $html->table(['class'=>'table datatable-basic']) !!}
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content-js')
    {!! $html->scripts() !!}
@endsection