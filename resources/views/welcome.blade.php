<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CRM') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <main class="welcome">
        <h1 class="welcome__title">{{ config('app.name', 'CRM') }}</h1>
        <p class="welcome__text">Каркас готов. Модули CRM подключаются по мере разработки.</p>
    </main>
</body>
</html>
