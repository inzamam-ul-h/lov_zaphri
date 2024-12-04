@extends('layouts.guest')

@section('content')


    <div class="row">
        <div class="col-12 col-sm-10 offset-sm-1 col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">

            <?php /*?><div class="login-brand login-brand-color d-none">
                <img alt="image" src="{{ asset_url('img/logo.png') }}" />
                {{ env('APP_NAME', 'Zaphri') }}
            </div><?php */?>

            <div class="card card-auth">

                <div class="card-header card-header-auth">
                    <h4>Register</h4>
                </div>

                <div class="card-body">
                
                    @include('flash::message')
                    @include('coreui-templates::common.errors')

                    <form action="{{ url('seller-signup') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="name">Manager Name</label>
                                <input type="text" id="name" name="name" class="form-control">
                            </div>
                            <div class="form-group col-6">
                                <label for="phone">Contact Number</label>
                                <input type="text" id="phone" name="phone" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password" class="form-control" name="password" required="">
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-12">
                                <label class="d-block">Terms & Conditions</label>
                                <textarea class="form-control" rows="5" disabled>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias quam velit odio recusandae hic ea ratione expedita corporis, porro deleniti consectetur non temporibus quos error, ex dicta in nesciunt quo! Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad natus rem consequuntur dolor tenetur rerum cumque porro. Distinctio, rem iure commodi repudiandae ea in debitis a! Dolore quo repellat temporibus. Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ea quia tempora corrupti aperiam culpa nobis unde nesciunt. Dolor dignissimos nihil dolores earum sapiente aliquid est odio, porro exercitationem, repellendus fugit.
                                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias quam velit odio recusandae hic ea ratione expedita corporis, porro deleniti consectetur non temporibus quos error, ex dicta in nesciunt quo! Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad natus rem consequuntur dolor tenetur rerum cumque porro. Distinctio, rem iure commodi repudiandae ea in debitis a! Dolore quo repellat temporibus. Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ea quia tempora corrupti aperiam culpa nobis unde nesciunt. Dolor dignissimos nihil dolores earum sapiente aliquid est odio, porro exercitationem, repellendus fugit.
                                </textarea>

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="agree" class="custom-control-input" id="agree">
                                <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-auth-color btn-lg btn-block">
                                Register
                            </button>
                        </div>

                    </form>

                </div>

                <div class="mb-4 text-muted text-center">
                    Already Registered? <a href="{{ url('/login') }}">Login</a>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js_after')

    <script src="{{ asset_url('js/app.min.js') }}"></script>

    <script src="{{ asset_url('bundles/jquery-pwstrength/jquery.pwstrength.min.js') }}"></script>
    <script src="{{ asset_url('bundles/jquery-selectric/jquery.selectric.min.js') }}"></script>

    <script src="{{ asset_url('js/page/auth-register.js') }}"></script>

    <script src="{{ asset_url('js/scripts.js') }}"></script>

@endsection