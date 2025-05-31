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
            margin: 5px;
        }

        /* LOGO + TÍTULO */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .header-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .header-cell.left {
            width: 25%;
        }

        .header-cell.center {
            width: 50%;
            text-align: center;
        }

        .header-cell.right {
            width: 25%;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #b7791f; /* yellow-700 */
            margin: 0;
        }
        
        h2 {
            font-size: 20px;
            color: #84cc16; /* lime-700 */
            margin-top: 30px;
            margin-bottom: 10px;
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
            background-color:rgb(240, 198, 44); 
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
