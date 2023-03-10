<div id="orderInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<order-invoice v-bind:sales_id="salesId"></order-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/orderInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#orderInvoice',
		components: {
			orderInvoice
		},
		data(){
			return {
				salesId: parseInt('<?php echo $salesId;?>')
			}
		}
	})
</script>

