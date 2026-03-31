<?php
/**
 * Forms reference.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 class="mb-3">Forms</h2>
<form class="border rounded p-4 bg-white" action="#" method="post" style="max-width:640px;">
	<div class="mb-3">
		<label class="form-label" for="ref-email">Email</label>
		<input class="form-control" id="ref-email" type="email" placeholder="email@example.com">
	</div>
	<div class="mb-3">
		<label class="form-label" for="ref-select">Select</label>
		<select class="form-select" id="ref-select">
			<option>Choose one</option>
			<option>Option 1</option>
		</select>
	</div>
	<div class="mb-3">
		<label class="form-label" for="ref-textarea">Message</label>
		<textarea class="form-control" id="ref-textarea" rows="3"></textarea>
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
