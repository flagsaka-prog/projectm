<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 | Maintenance Mode</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 400px;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 20px;
            color: #1f2937;
            margin: 0 0 10px 0;
        }

        p {
            color: #6b7280;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn:hover {
            background: #2563eb;
        }

        .note {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🛠️</h1>
        <h2>Sistem Sedang Dalam Perbaikan</h2>
        <p>{{ file_exists(storage_path('framework/down_message.txt')) ? file_get_contents(storage_path('framework/down_message.txt')) : 'Mohon tunggu beberapa saat, sistem akan segera kembali normal.' }}
        </p>
        <p class="note">* Menghapus sesi maintenance di browser Anda.</p>
    </div>
</body>

</html>
