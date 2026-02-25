<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iconito Login </title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-t-50 p-b-90">
            <form class="login100-form validate-form flex-sb flex-w" action="{{ route('login.post') }}" method="post">
                <span class="login100-form-title p-b-51"> Login </span>
                <label for="" class="m-b-10"> <strong>Example:</strong> demo-store.myshopify.com</label>
                <div class="wrap-input100 validate-input m-b-16">
                    <input class="input100" type="text" name="store" value="{{ old('store') }}" placeholder="Shopify Store">
                    <span class="focus-input100"></span>
                </div>
                @error('store') <span class="text-danger"> {{$message}} </span> @enderror

                <div class="container-login100-form-btn m-t-17">
                    <button class="login100-form-btn"> Login </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
