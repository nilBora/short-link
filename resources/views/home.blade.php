@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Get Short Link
                    
                    {{ Form::open(array('url' => '/', 'method' => 'post')) }}
                        <input type="text" name="link" />
                        <input type="submit" />
                    {{ Form::close() }}
                    
                    @if ($short_url)
                    
                        Your Link: {{ $current_url}} <br />
                        Your short link: {{ $short_url }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
