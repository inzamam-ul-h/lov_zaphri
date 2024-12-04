@extends('layouts.guest')



@section('content')



    <div class="row">

        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

        

            <div class="login-brand login-brand-color">

                <img alt="image" src="{{ asset_url('img/logo.png') }}" />

                {{ env('APP_NAME', 'Zaphri') }}

            </div>

            

            <div class="card card-auth">

            

                <div class="card-header card-header-auth">

                    <h4>Login</h4>

                </div>

                

        		<div class="card-body">

                

                    @include('flash::message')

                    @include('coreui-templates::common.errors')

                

                    <form method="post" action="{{ url('/login') }}" class="needs-validation" novalidate id="login-form">

                        @csrf

                        <div class="form-group">

                            <label for="email">Email</label>

                            <input type="email" id="email" name="email" class="form-control {{ $errors->has('email')?'is-invalid':'' }}" value="{{ old('email') }}" placeholder="Email" tabindex="1" required autofocus>

                            <div class="invalid-feedback">

                                Please fill in valid email

                            </div>

                        </div>

                        

                        <div class="form-group">

                            <div class="d-block">

                                <label for="password" class="control-label">Password</label>

                                <div class="float-right">

                                    <a href="{{ url('/password/reset') }}" class="text-small">

                                        Forgot Password?

                                    </a>

                                </div>

                            </div>

                            <input type="password" id="password" name="password" class="form-control {{ $errors->has('password')?'is-invalid':'' }}" placeholder="Password" tabindex="2" required>

                            <div class="invalid-feedback">

                                please fill in valid password

                            </div>

                        </div>

                        

                        <div class="form-group">

                            <div class="custom-control custom-checkbox">

                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">

                                <label class="custom-control-label" for="remember-me">Remember Me</label>

                            </div>

                        </div>

                        

                        <div class="form-group">

                            <button type="submit" class="btn btn-lg btn-block btn-auth-color" tabindex="4">

                                Login

                            </button>

                        </div>

                    </form>

        

                    <?php /*?><div class="text-center mt-4 mb-3">

                        <div class="text-job text-muted">Login With Social</div>

                    </div>

                    

                    <div class="row sm-gutters">

                        <div class="col-6">

                            <a class="btn btn-block btn-social btn-facebook">

                                <span class="fab fa-facebook"></span> Facebook

                            </a>

                        </div>

                        <div class="col-6">

                            <a class="btn btn-block btn-social btn-twitter">

                                <span class="fab fa-twitter"></span> Twitter

                            </a>

                        </div>

                    </div><?php */?>

                    

        		</div>

            

                <div class="mb-4 text-muted text-center">

                    Don't have an account? <a href="{{ url('/register') }}">Create One</a>

                </div>

                

        	</div>

        </div>

    </div>



@endsection



@section('js_after')

  

<script src="{{ asset_url('js/app.min.js') }}"></script>



<script src="{{ asset_url('js/scripts.js') }}"></script>



@endsection

