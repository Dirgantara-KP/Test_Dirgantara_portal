<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Aktif</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #4f46e5;
            color: white;
            padding: 20px 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .body-content {
            padding: 30px;
            color: #333;
            line-height: 1.6;
        }
        .body-content h2 {
            color: #4f46e5;
            margin-top: 0;
        }
        .details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .details p {
            margin: 6px 0;
        }
        .details strong {
            color: #374151;
        }
        .footer {
            background: #f9fafb;
            padding: 15px 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Event Telah Aktif</h1>
        </div>

        <div class="body-content">
            <h2>Halo,</h2>
            <p>Event berikut telah berubah status menjadi <strong>Aktif</strong> dan siap untuk diakses oleh peserta:</p>

            <div class="details">
                <p><strong>ID Event:</strong> {{ $event->kode_event }}</p>
                <p><strong>Nama Event:</strong> {{ $event->nama_event }}</p>
                <p><strong>Tanggal:</strong> {{ $event->tanggal_event->format('d M Y') }}</p>
                <p><strong>Jam:</strong> {{ \Illuminate\Support\Carbon::parse($event->jam_mulai)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($event->jam_selesai)->format('H:i') }}</p>
                <p><strong>Status:</strong> Aktif</p>
            </div>

            <p>Peserta yang terdaftar dapat mulai mengerjakan ujian sesuai dengan jadwal yang telah ditentukan.</p>

            <p style="margin-top: 20px;">Terima kasih,</p>
            <p><strong>Tim Admin Portal Test Dirgantara</strong></p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem Portal Test Dirgantara.</p>
            <p>&copy; {{ date('Y') }} Portal Test Dirgantara. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

