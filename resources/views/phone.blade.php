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
                <div class="panel-heading login_header">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/save_phone') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Phone Number</label>

                            <div class="col-md-6">
                                <input id="phone" type="phone" class="form-control" placeholder="Enter No. like 03457050405" name="phone" value="{{ old('phone') }}">
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn_login">
                                    <i class="fa fa-btn fa-sign-in"></i> Get Key
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
