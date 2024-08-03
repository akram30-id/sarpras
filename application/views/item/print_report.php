<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title; ?></title>
        <style>
            #table {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                font-size: 10pt;
                border-collapse: collapse;
                width: 100%;
				padding: 0;
            }

            #table td, #table th {
                border: 0px solid #ddd;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <table id="table">
			<tr>
				<td width="15%">
					<img src="<?= base_url('assets/img/apple-touch-icon.png') ?>" alt="" srcset="" style="width: 128px;">
				</td>
				<td width="70%">
					<div style="text-align:center">
						<h2><?= strtoupper($title) ?></h2>
						<hr>
						<h4><b><?= ucwords($subtitle) ?></b></h4>
					</div>
				</td>
				<td width="15%"></td>
			</tr>
			<tr>
				<td colspan="4">
					<p><b>Berita acara kehilangan barang milik yayasan</b> ini dibuat pada:</p>
				</td>
			</tr>
			<tr>
				<td colspan="1" style="padding: 0; margin: 0;">
					<p><b>Hari</b></p>
				</td>
				<td colspan="3">
					<p><?= date('l', strtotime($report->created_at)) ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Tanggal</b></p>
				</td>
				<td colspan="3">
					<p><?= date('d F Y', strtotime($report->created_at)) ?></p>
				</td>
			</tr>
			
			<!-- OLEH SAYA -->
			<tr>
				<td style="padding-top: 8px; padding-bottom: 8px;" colspan="3">
					<p>Oleh saya:</p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Nama</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->name ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b><?= $report->role > 2 ? 'NIS' : 'NIP' ?></b></p>
				</td>
				<td colspan="3">
					<p><?= $report->username ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Jabatan</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->role_name ?></p>
				</td>
			</tr>

			<!-- DENGAN INI MENYATAKAN -->
			<br>
			<tr>
				<td style="padding-top: 8px; padding-bottom: 8px;" colspan="3">
					<p>Dengan ini menyatakan dengan sebenar-benarnya, bahwa telah terjadi <?= $type == 'RUSAK' ? 'kerusakan' : 'kehilangan' ?> barang milik yayasan yang dipercayakan kepada saya dengan detail barang sebagai berikut:</p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Nama Barang</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->inventory_name ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Kode Barang</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->item_code ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Jumlah Barang</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->qty . ' ' . $report->unit_qty ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Kronologi</b></p>
				</td>
				<td colspan="3">
					<p><?= $report->reason ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<p><b>Evidence</b></p>
				</td>
				<td colspan="3">
					<img src="<?= $report->evidence ?>" alt="..." height="64">
				</td>
			</tr>
			<br>

			<!-- DENGAN INI MENYATAKAN -->
			<br>
			<tr>
				<td colspan="4">
					<p>Demikian berita acara <?= $type == 'RUSAK' ? 'kerusakan' : 'kehilangan' ?> ini saya buat dengan sebenar-benarnya.</p>
				</td>
			</tr>
			<tr>
				<td colspan="1"></td>
				<td colspan="1" style="padding-top: -32px;">
					<div style="display: flex;">
						<p><u>Depok, <?= date('d F Y') ?></u></p>
					</div>
					<div style="display: flex;">
						<div>
							<p style="margin-left: 360px; margin-top: -16px;">Yang Menyatakan,</p>
						</div>
						<div>
							<p style="margin-top: -16px; margin-left: 20px;">Mengetahui,</p>
						</div>
						<br>
						<br>
						<br>
						<br>
					</div>
				</td>
				<td colspan="1"></td>
			</tr>
			<tr>
				<td colspan="1"></td>
				<td colspan="1">
					<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: -12px;">
						<hr style="width: 128px; margin-left: 0px;">
						<hr style="width: 128px; margin-right: 0px;">
					</div>
				</td>
				<td colspan="1"></td>
			</tr>
        </table>
    </body>
</html>
