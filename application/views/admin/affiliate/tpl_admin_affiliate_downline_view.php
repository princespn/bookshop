<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h2 class="text-sm-center"><?= lang('referral_downline') ?></h2>
		</div>
	</div>
	<hr/>
	<div id="downline">
		<table id="downline-table" class="table">
			<tr valign="top">
				<td colspan="7" align="center">
					<div class="downline-box">
						<?php if (!empty($affiliate_data['sponsor_id'])): ?>
						<a href="<?=admin_url('affiliate_downline/view/' . $affiliate_data['sponsor_id'] )?>"><i class="fa fa-arrow-up "></i></a><br/>
						<?php endif; ?>
						<i class="fa fa-user fa-5x "></i></a><br/>
						<small><?= $affiliate_data['fname'] . ' ' . $affiliate_data['lname'] ?></small>
						<br/>
						<i class="fa fa-arrow-down "></i>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<td align="center">
					<table class="table">
						<tr valign="top">
							<?= $rows['results'] ?>
						</tr>
					</table>
				</td>
			</tr>
		</table>


	</div>
</div>