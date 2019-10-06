<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>City Weather</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        <script src="https://kit.fontawesome.com/c6fd8e2134.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div id="app">
            <search-form></search-form>
        </div>
        <script src="{{asset('js/app.js')}}"></script>
    </body>
</html>
