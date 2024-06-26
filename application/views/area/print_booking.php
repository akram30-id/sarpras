<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title; ?></title>
        <style>
            #table {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                font-size: 7pt;
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
				<th>KODE BOOKING</th>
				<th>KODE RUANGAN</th>
				<th>NAMA RUANGAN</th>
				<th>RENCANA CHECK-IN</th>
				<th>RENCANA CHECK-OUT</th>
				<th>STATUS APPROVAL</th>
				<th>NAMA BOOKING</th>
				<th>KETERANGAN BOOKING</th>
				<th>STATUS CHECKOUT</th>
				<th>TGL DIBUAT</th>
			</tr>
			<?php 
			$no = 1;
			foreach ($report as $key => $r){ ?>
				<tr>
					<td><?= $no++; ?></td>
					<td><?= $r->submission_area_code ?></td>
					<td><?= $r->area_code ?></td>
					<td><?= $r->area_name ?></td>
					<td><?= date('d F Y H:i', strtotime($r->start_date)) ?></td>
					<td><?= date('d F Y H:i', strtotime($r->end_date)) ?></td>
					<td><?= $r->status_approval ?></td>
					<td><?= $r->name ?></td>
					<td><?= $r->user_notes ?></td>
					<td><?= in_array($r->is_checkout, ['', null, 0, 2]) ? 'BELUM CHECKOUT' : 'CHECKOUT' ?></td>
					<td><?= date('d F Y', strtotime($r->created_at)) ?></td>
				</tr>
			<?php } ?>
        </table>
    </body>
</html>
