<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a202c;
        }

        h1, h2 {
            color: #2c7a7b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e6fffa;
        }

        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .section {
            margin-bottom: 30px;
        }

        .summary p {
            margin: 4px 0;
        }

        .status-completed { color: #38a169; }
        .status-pending { color: #dd6b20; }
        .status-canceled { color: #e53e3e; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
