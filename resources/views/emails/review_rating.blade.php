<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iconito Review & Rating</title>

    <style>
        body{
            margin-top: 20px;
        }
        .container{
            background: #f0f2f3;
            padding: 20px 0px 0px 0px;
        }
        .container > h1{
            text-align: center;
        }
        .details{
            width: 50%;
            margin: 30px auto;
        }
        .star{
            font-size:0px;
            white-space:nowrap;
            display:inline-block;
            width:50px;
            height:50px;
            overflow:hidden;
            position:relative;
        }
        .fill-star{
            background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjRkZERjg4IiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=') no-repeat;
            background-size: contain;
        }
        .unfilled-star{
            background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjREREREREIiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=') no-repeat;
            background-size: contain;
        }
        .footer{
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            width: 100%;
            height: 50px;
            background-color: rgb(59 130 246 / 0.5);
        }
        .image-wrapper{
            text-align: center;
        }
        .text-center{
            text-align: center;
        }
        .col-sm-2{
            width: 20%;
            display: inline-block;
        }
        .col-sm-2 > label{
            font-weight: bold;
        }
        .col-sm-6{
            width: 60%;
            display: inline-block;
        }
    </style>
</head>
<body>
        <div class="container">
            <div class="image-wrapper">
                <img src="{{ asset('/images/logo.png') }}" width="150px" alt="Rpyal Apps">
            </div>
            <h3 class="text-center"> Review & Rating Email</h3>
            <div class="details">
                <div class="row">
                    <div class="col-sm-2">
                        <label> Store:</label>
                    </div>

                    <div class="col-sm-6">
                            <p> {{ $user['name'] }} </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <label> Rate Count:</label>
                    </div>

                    <div class="col-sm-6">
                            <p> {{ $data['rate'] }}/5 </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <label> Comment:</label>
                    </div>

                    <div class="col-sm-6">
                        <p> {{ $data['description'] }}</p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                @foreach(range(1,5) as $val)
                    @if($val <= $data['rate'])
                        <img class="star" src="{{ asset('/images/fill-star.png') }}" alt="">
                    @else
                        <img class="star" src="{{ asset('/images/unfill-star.png') }}" alt="">
                    @endif
                @endforeach
            </div>

            @component('mail::panel')
            <div style="text-align: center">
                <span style="color:white;font-size:12px;padding: 10px;margin-top: 10px;text-align: center;font-weight: bold;background-color: rgb(59 130 246 / 0.5);">
                    <a target="_blank" rel="noopener noreferrer" style="color:blue;" href="https://www.royal-apps.io/"> Royal Apps </a>
                    Copyright © 2023
                </span>
            </div>
            @endcomponent
        </div>
</body>
</html>
