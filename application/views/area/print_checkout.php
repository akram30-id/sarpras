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
                border: 1px solid #ddd;
                padding: 5;
            }
        </style>
    </head>
    <body>
		<table id="tbl-title">
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
		</table>

        <table id="table">
			<tr>
				<th>No</th>
				<th>KODE CHECKOUT</th>
				<th>KODE BOOKING</th>
				<th>KODE RUANGAN</th>
				<th>NAMA RUANGAN</th>
				<th>TGL CHECKOUT</th>
				<th>USER CHECKOUT</th>
				<th>TANDA TANGAN</th>
				<th>TGL DIBUAT</th>
			</tr>
			<?php 
			$no = 1;
			foreach ($report as $key => $r){ ?>
				<tr>
					<td><?= $no++; ?></td>
					<td><?= $r->checkout_code ?></td>
					<td><?= $r->submission_area_code ?></td>
					<td><?= $r->area_code ?></td>
					<td><?= $r->area_name ?></td>
					<td><?= date('d F Y H:i', strtotime($r->checkout_date)) ?></td>
					<td><?= $r->area_name ?></td>
					<td><img src="<?= $r->signature ?>" height="30" alt="..."></td>
					<td><?= date('d F Y', strtotime($r->created_at)) ?></td>
				</tr>
			<?php } ?>
        </table>
    </body>
</html>
