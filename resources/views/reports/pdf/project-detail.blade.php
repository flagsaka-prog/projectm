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

        .wbs-parent {
            background-color: #eef2ff;
            font-weight: bold;
        }

        .wbs-child-1 {
            padding-left: 20px;
            background-color: #fafafa;
        }

        .wbs-child-2 {
            padding-left: 40px;
            background-color: #fff;
        }

        .progress-container {
            width: 100px;
        }

        .progress-bar {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 3px;
            height: 6px;
        }

        .progress-fill {
            height: 6px;
            border-radius: 3px;
        }

        .progress-green {
            background-color: #22c55e;
        }

        .progress-yellow {
            background-color: #f59e0b;
        }

        .progress-red {
            background-color: #ef4444;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <h1>{{ $project->name }}</h1>
    <p class="text-center" style="font-size: 12px; margin-bottom: 20px;">
        PM: {{ $project->manager?->name ?? '-' }} |
        Periode: {{ $project->start_date->format('d M Y') }} s/d {{ $project->end_date->format('d M Y') }}
    </p>

    {{-- Info Box --}}
    <div class="info-box">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 25%; border: none;">Budget</td>
                <td style="border: none; font-weight: bold;">Rp {{ number_format($project->budget, 0, ',', '.') }}</td>
                <td style="width: 25%; border: none;">Estimasi Cost</td>
                <td style="border: none; font-weight: bold;">Rp
                    {{ number_format($project->estimated_cost ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none;">Actual Cost</td>
                <td style="border: none; font-weight: bold;">Rp
                    {{ number_format($project->actual_cost ?? 0, 0, ',', '.') }}</td>
                <td style="border: none;">Status</td>
                <td style="border: none; font-weight: bold;">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td>
            </tr>
        </table>
    </div>

    {{-- Tabel WBS --}}
    <h3>WORK BREAKDOWN STRUCTURE (WBS)</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">KETERANGAN PEKERJAAN</th>
                <th style="width: 10%;" class="text-center">MULAI</th>
                <th style="width: 10%;" class="text-center">SELESAI</th>
                <th style="width: 15%;" class="text-center">PROGRESS</th>
                <th style="width: 15%;" class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach ($tasks as $task)
                @php
                    $indent = $task->level > 1 ? 'wbs-child-' . ($task->level - 1) : 'wbs-parent';
                    $no++;
                @endphp
                <tr class="{{ $indent }}">
                    <td class="text-center">{{ $no }}</td>
                    <td>
                        {{ str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $task->level - 1) }}{{ $task->name }}
                    </td>
                    <td class="text-center text-xs">{{ $task->start_date?->format('d M Y') ?? '-' }}</td>
                    <td class="text-center text-xs">{{ $task->end_date?->format('d M Y') ?? '-' }}</td>
                    <td class="text-center">
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div class="progress-fill {{ $task->progress >= 100 ? 'progress-green' : ($task->progress > 0 ? 'progress-yellow' : '') }}"
                                    style="width: {{ $task->progress }}%;"></div>
                            </div>
                        </div>
                        <span style="margin-left: 5px; font-size: 10px;">{{ $task->progress }}%</span>
                    </td>
                    <td class="text-center text-xs">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y, H:i') }} | Sistem PMO
    </div>
</body>

</html>
