<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body, *{
            font-family: Arial
        }

        .kopSurat{
            overflow: auto;
            padding-bottom: .25em;
        }

        .logoKop{
            float: left;
            width: 45%;
        }

        .logoKop img{
            width: 100%;
        }

        .alamatKop{
            float: right;
            width: 45%;
        }

        h4{
            font-size: 18px;
            margin-bottom: 0;
        }

        h6{
            font-size: 10px;
            margin-bottom: 0;
        }

        .no-top-margin{
            margin-top: 0;
        }

        .p2{
            font-size: 10px;
            margin-bottom: 0;
        }

        .p1{
            font-size: 10px;
            margin-top: 0;
            margin-bottom: 0;
        }

        #titleSurat{
            overflow: auto;
        }

        #tanggalSurat{
            float: right;
            width: 20%;
        }

        .paragraph{
            margin-top: 1.5em;
        }

        .paragraph-center{
            margin-top: 1.5em;
            text-align: center;
        }

        .textTable, .itemTable{
            border-collapse: collapse;
        }

        .textTable td{
            width: 180px;
        }
        
        .textTable .tdTableTitle{
            width: 100px;
        }

        .textTable .tdTableContent{
            width: 200px;
        }

        .textTable, .textTable tr, .textTable tr td{
            font-size: 12px;
            margin-top: 0;
            margin-bottom: 0;
            border: 0;
        }

        .itemTable{
            width: 100%;
            box-sizing: border-box;
            margin-top: .25em;
        }
        
        .itemTable tr th, .itemTable tr td{
            border: 1px solid black;
            font-size: 12px;
            margin-top: 0;
            margin-bottom: 0;
            padding: 0.5em .75em .5em .75em;
        }

        .itemTable tr th{
            background-color: #4472c4;
            color: white;
        }

        .no-border{
            border: 0 solid black;
            color: white;
        }

        .no-border2{
            border: 0 solid black;
        }

        .paragraph-spacer{
            border: 1px solid black;
            width: 100%;
            height: 3em;
            margin-top: 1em;
        }

        .paragraph-column{
            width: 100%;
            text-align: center;
            margin-top: 1.5em;
        }

        .column3{
            width: 33%;
        }

        .spacer{
            color: white;
        }

        .bg-blue{
            background-color: #4472c4;
            color: white;
            padding: 0.1em .5em;
            margin-bottom: .25em;
        }

        .dateTable{
            height: 4em;
            text-align: center;
            vertical-align: bottom;
        }

        #cap{
            height: 7em;
        }
    </style>
</head>
<body>
    <div class="kopSurat">
        <div class="logoKop">
            <img src="{{ public_path('assets/static/LOGO_DARK.png') }}">
        </div>
        <div class="alamatKop">
            <h4><b>RESEP OBAT</b></h4>
            <table class="textTable">
                <tr>
                    <td class="tdTableTitle">Tanggal</td>
                    <td class="tdTableContent">: {{ $consultation->consultation_date }}</td>
                </tr>
                <tr>
                    <td class="tdTableTitle">Sesi</td>
                    <td class="tdTableContent">: {{ $consultation->doctor_schedule->schedule->session->name . ' (' . $consultation->doctor_schedule->schedule->session->start_time . ' - ' . $consultation->doctor_schedule->schedule->session->end_time . ')' }}</td>
                </tr>
                <tr>
                    <td class="tdTableTitle">Nama Pasien</td>
                    <td class="tdTableContent">: {{ $consultation->user->name }}</td>
                </tr>
                <tr>
                    <td class="tdTableTitle">Nama Dokter</td>
                    <td class="tdTableContent">: {{ $consultation->doctor_schedule->doctor->name }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="paragraph">
        <h6 class="no-top-margin">Detail Resep Obat dan Notes</h6>

        <table class="itemTable">
            <tbody>
                <tr>
                    <td>{!! nl2br(e($consultation->recipe)) !!}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="paragraph-center">
        <p class="p1">@2023 alohadoc</p>
    </div>
</body>
</html>

