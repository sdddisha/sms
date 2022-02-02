<!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Reset password</title>

        <!-- Custom fonts for this template-->
        <link href="{{asset('files/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="{{asset('files/css/sb-admin-2.min.css')}}" rel="stylesheet">

    </head>

    <body class="bg-gradient-primary">

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Reset Password!</h1>
                                        </div>
                                        <form class="user"  method="POST" action="{{ url('student/reset-password')}}">
                                            @csrf
                                            <input type="hidden" name="token" value="{{ $token }}">
                                            
                                            <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp" name="email"
                                                placeholder="Enter Email Address...">
                                                <div>
                                                <input type="password" name="password" class="form-control form-control-user"
                                                    id="exampleInputPassword" placeholder="New Password" >
                                            </div>
                                            </div>
                                            
                                            <input type="submit" class="btn btn-primary btn-user btn-block" value="Login">
                                                                                      
                                        
                                            @if ($errors->has('password_confirmation'))
                                      <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                  @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="{{asset('files/vendor/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('files/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{asset('files/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{asset('files/js/sb-admin-2.min.js')}}"></script>

    </body>

    </html>