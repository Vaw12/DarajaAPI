<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="icon" href="{{asset('img/mpesanew.png')}}">
    <title>Laravel Daraja</title>
    
</head>
<body>
    @include('inc.navbar')
    <div class="container">
        @yield('content')
    </div>

    <script src="{{asset('js/app.js')}}"></script>
    <script type="text/javascript">
        document.getElementById('getAccessToken').addEventListener('click', (event) => {
            event.preventDefault()

            axios.post('/get-token', {})
            .then((response) => {
                console.log(response.data);
                document.getElementById('access_token').innerHTML = response.data.access_token
            })
            .catch((error) => {
                console.log(error);
            })
        })
    </script>
</body>
</html>