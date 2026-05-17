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

        h2 {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
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

        .parent-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .child-1 {
            padding-left: 25px;
        }

        .child-2 {
            padding-left: 50px;
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
    <h2>Work Breakdown Structure (WBS) - Progress Report</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">NO</th>
                <th style="width: 45%">KETERANGAN PEKERJAAN</th>
                <th style="width: 15%">PIC</th>
                <th style="width: 15%">MULAI</th>
                <th style="width: 10%">SELESAI</th>
                <th style="width: 10%">PROGRESS</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach ($tasks as $task)
                <tr>
                    <td colspan="6" class="parent-row" style="background-color: #e2e8f0; font-size: 12px;">
                        <strong>{{ $task->name }}</strong>
                    </td>
                </tr>

                @php $no = 0; @endphp
                @foreach ($task->subtasks as $child1)
                    <tr>
                        <td class="text-center child-1">{{ ++$no }}</td>
                        <td class="child-1">{{ $child1->name }}</td>
                        <td class="text-center text-xs child-1">
                            {{ $child1->start_date ? $child1->start_date->format('d M Y') : '-' }}</td>
                        <td class="text-center text-xs child-1">
                            {{ $child1->end_date ? $child1->end_date->format('d M Y') : '-' }}</td>
                        <td class="text-center child-1">
                            {{ $child1->progress }}%
                        </td>
                    </tr>

                    @if ($child1->subtasks->count() > 0)
                        @php $subNo = 0; @endphp
                        @foreach ($child1->subtasks as $child2)
                            <tr>
                                <td class="text-center child-2">{{ $no }}.{{ ++$subNo }}</td>
                                <td class="child-2">{{ $child2->name }}</td>
                                <td class="text-center text-xs child-2">
                                    {{ $child2->start_date ? $child2->start_date->format('d M Y') : '-' }}</td>
                                <td class="text-center text-xs child-2">
                                    {{ $child2->end_date ? $end_date->format('d M Y') : '-' }}</td>
                                <td class="text-center child-2">
                                    {{ $child2->progress }}%
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y, H:i') }} | Project: {{ $project->name }}
    </div>
</body>

</html>
