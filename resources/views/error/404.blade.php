<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 50px;
        }

        p {
            font-size: 20px;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        img {
            max-width: 50%;
            height: auto;
        }
    </style>
</head>

<body>
    <img src="{{ asset('assets/images/not-found.svg') }}" alt="404 Image">
    <h1>404</h1>
    <p>Page Not Found</p>
    <p>Sorry, the page you are looking for could not be found.</p>
    <p><a href="{{ url()->previous() }}">
            <button class="btn btn-primary">Go to Previous Page</button></a>
    </p>
</body>

</html>