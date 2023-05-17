<div class="brcpasstour_notification_tags">
	<?php // General ?>
	<table class="brc-admin-table">
		<thead>
			<tr><!--tr--><th>General</th><!--tr--></tr>
		</thead>
		<tbody>
			<tr><!--tr--><td>{{sitename}}</td><!--td--><td>Site Name</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-order}}</td><!--td--><td>Order Number</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-order-date}}</td><!--td--><td>Order Date</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-order-status}}</td><!--td--><td>Order Status</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-payment-method}}</td><!--td--><td>Payment Method</td><!--tr--></tr>
		</tbody>
	</table>
	 
	<?php // WordPress Profile Details ?>
	<table class="brc-admin-table">
		<thead>
			<tr><!--tr--><th>WordPress Profile Details</th><!--tr--></tr>
		</thead>
		<tbody>
			<tr><!--tr--><td>{{wp-first-name}}</td><!--td--><td>First Name</td><!--tr--></tr>
			<tr><!--tr--><td>{{wp-last-name}}</td><!--td--><td>Last Name</td><!--tr--></tr>
			<tr><!--tr--><td>{{wp-display-name}}</td><!--td--><td>Display Name</td><!--tr--></tr>
			<tr><!--tr--><td>{{wp-email}}</td><!--td--><td>Email</td><!--tr--></tr>
		</tbody>
	</table>
	
	<?php // WooCommerce Order Details ?>
	<table class="brc-admin-table">
		<thead>
			<tr><!--tr--><th>WooCommerce Order Details</th><!--tr--></tr>
		</thead>
		<tbody>
			<tr><!--tr--><td>{{wc-product-names}}</td><!--td--><td>All Item / Product Names in Order</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-product-name-count}}</td><!--td--><td>First Item name then item counter</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-total-products}}</td><!--td--><td>Total Number of Products in Order</td><!--tr--></tr>
			<tr style="display:none;"><!--tr--><td>{{wc-total-items}}</td><!--td--><td>Total Number of Items in Order</td><!--tr--></tr>
			<tr style="display:none;"><!--tr--><td>{{wc-order-items}}</td><!--td--><td>Names of all products in Order with item counter</td><!--tr--></tr>
			<tr><!--tr--><td>{{wc-order-amount}}</td><!--td--><td>Total Amount (Incl. Tax)</td><!--tr--></tr>
		</tbody>
	</table>
</div>