@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <div class="col-sm-12 name_shop">
    <!--<h1>فيضان کارپوریشن </h1>-->
    </div>
    <div class="col-md-3">
    </div>
        <div class="col-md-6  login_page">
                 
            <div class="panel panel-default pannel_login">
                <div class="panel-heading login_header">Please Enter Key</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/key_enter') }}">
                        {{ csrf_field() }}
                         <p style="text-align: center;">Please check your mobile and get confirmation  key</p>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Enter Key</label>

                            <div class="col-md-6">
                                <input id="phone" type="token" class="form-control" name="token" value="{{ old('token') }}">
                                @if (Session::has('error'))
                                <p style="color: red;">{{ Session::get('error') }}</p>
                              
                            @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn_login">
                                    <i class="fa fa-btn fa-sign-in"></i> Go To Login
                                </button>

                                <!--<a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
    </div>
</div>
@endsection
