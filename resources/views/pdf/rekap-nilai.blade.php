<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Siswa</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 12px;
        }

        .student-info {
            margin-bottom: 16px;
        }

        .student-info table {
            width: 100%;
        }

        .student-info td {
            padding: 4px;
            vertical-align: top;
        }

        h3 {
            margin: 16px 0 6px;
            font-size: 14px;
        }

        table.rekap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.rekap th,
        table.rekap td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }

        table.rekap th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>

    {{-- ===================== --}}
    {{-- HEADER --}}
    {{-- ===================== --}}
    <div class="header">
        <h2>REKAP NILAI SISWA</h2>
        <p>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
    </div>

    {{-- ===================== --}}
    {{-- INFO SISWA --}}
    {{-- ===================== --}}
    <div class="student-info">
        <table>
            <tr>
                <td width="15%">Nama</td>
                <td width="2%">:</td>
                <td width="33%">{{ $student['name'] }}</td>

                <td width="15%">Kelas</td>
                <td width="2%">:</td>
                <td width="33%">{{ $student['kelas'] }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $student['nis'] }}</td>

                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    {{-- ===================== --}}
    {{-- REKAP PER MAPEL --}}
    {{-- ===================== --}}
    @foreach ($rows as $row)
        <h3>{{ $row['mapel'] }}</h3>

        <table class="rekap">
            <tr>
                @foreach ($row['headers'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($row['headers'] as $header)
                    <td>
                        {{ $row['scores'][$header] }}
                    </td>
                @endforeach
            </tr>
        </table>
    @endforeach

    {{-- ===================== --}}
    {{-- FOOTER --}}
    {{-- ===================== --}}
    <div class="footer">
        <p>
            Dicetak pada {{ date('d-m-Y') }}
        </p>
    </div>

</body>
</html>
