<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
        }

        h3 {
            font-size: 12px;
            margin-top: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bg-red {
            background-color: #fecaca;
        }

        .bg-green {
            background-color: #d1fae5;
        }

        .bg-yellow {
            background-color: #fef9c3;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        .wbs-parent {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .wbs-child-1 {
            padding-left: 20px;
        }

        .wbs-child-2 {
            padding-left: 40px;
        }

        .progress-bar {
            width: 100px;
            background-color: #e5e7eb;
            border-radius: 3px;
            display: inline-block;
        }

        .progress-fill {
            height: 6px;
            border-radius: 3px;
            background-color: #3b82f6;
        }
    </style>
</head>

<body>

    <h1>LAPORAN PORTOFOLIO PROJECT</h1>
    <p class="text-center" style="font-size: 12px; margin-bottom: 20px;">
        Periode: {{ $startDate }} s/d {{ $endDate }}
    </p>

    {{-- 1. Ringkasan EVM --}}
    <h3>1. Ringkasan Earned Value Management (EVM)</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th class="text-right">Budget</th>
                <th class="text-right">PV</th>
                <th class="text-right">EV</th>
                <th class="text-right">AC</th>
                <th class="text-center">SPI</th>
                <th class="text-center">CPI</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($evmData as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td class="text-right">{{ number_format($row['budget'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['pv'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['ev'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['ac'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row['spi'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $row['cpi'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $row['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <pagebreak>

        {{-- 2. Tabel Keterlambatan --}}
        <h3>2. Tugas Terlambat</h3>
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Deadline</th>
                    <th class="text-center">Terlambat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lateTasks as $row)
                    <tr class="bg-red">
                        <td>{{ $row['task_name'] }}</td>
                        <td>{{ $row['project_name'] }}</td>
                        <td>{{ $row['end_date'] }}</td>
                        <td class="text-center"><strong>{{ $row['late_days'] }} hari</strong></td>
                        <td class="text-center">{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada task terlambat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Dicetak pada: {{ now()->format('d F Y, H:i') }} | Sistem PMO
        </div>
</body>

</html>
