<style>
	.v-select {
		margin-top: -2.5px;
		float: right;
		min-width: 180px;
		margin-left: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#searchForm select {
		padding: 0;
		border-radius: 4px;
	}

	#searchForm .form-group {
		margin-right: 5px;
	}

	#searchForm * {
		font-size: 13px;
	}

	.record-table {
		width: 100%;
		border-collapse: collapse;
	}

	.record-table thead {
		background-color: #0097df;
		color: white;
	}

	.record-table th,
	.record-table td {
		padding: 3px;
		border: 1px solid #454545;
	}

	.record-table th {
		text-align: center;
	}
</style>
<div id="salesTotalquantityRecord">
	<div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
		<div class="col-md-12">
			<form class="form-inline" id="searchForm" @submit.prevent="getsalesRecord">
				<div class="form-group">
					<label>Search Type</label>
					<select class="form-control" v-model="searchType" @change="onChangeSearchType">
						<option value="">All</option>
						<option value="customer">By Customer</option>
						<option value="employee">By Employee</option>
						<option value="product">By Product</option>
					</select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'customer' && customers.length > 0 ||  searchType == 'quantity' ? '' : 'none'}">
					<label>Customer</label>
					<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'employee' && reportingboss.length > 0 ? '' : 'none'}">
					<label>Reporting Boss</label>
					<v-select v-bind:options="reportingboss" v-model="selectedReportingboss" label="Employee_Name"></v-select>
				</div>
				<div class="form-group" :style="{display: employees.length > 0 ? '':'none'}">
					<label>Employee</label>
					<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name"></v-select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'product' && products.length > 0 ? '' : 'none'}">
					<label>Product</label>
					<v-select v-bind:options="products" v-model="selectedProduct" label="display_text" @input="sales = []"></v-select>
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateFrom">
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateTo">
				</div>

				<div class="form-group" style="margin-top: -5px;">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="margin-top:15px;display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
		<div class="col-md-12" style="margin-bottom: 10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent" v-if="searchType != 'employee'">
				<table class="table table-responsive table-bordered">
					<thead>
						<tr>
							<th>Sl</th>
							<th>Product Code</th>
							<th>Product Name</th>
							<th>Total Sale Qty</th>
							<th>Total Sale Amount</th>
						</tr>
					</thead>
					<tbody>
						<template v-for="sale in sales">
							<tr>
								<td colspan="5" style="text-align:center;background: #10285C;font-weight:bold;color:white;">{{ sale.category_name }}</td>
							</tr>
							<tr v-for="(product, index) in sale.products" :key="index">
								<td>{{index + 1}}</td>
								<td>{{ product.product_code }}</td>
								<td>{{ product.product_name }}</td>
								<td style="text-align:right;">{{ product.quantity }}</td>
								<td style="text-align:right;">{{ product.price }}</td>
							</tr>
						</template>
						<tr v-if="sales.length > 0" style="font-weight:bold;background:#454545;color:white;">
							<td colspan="3" style="text-align:right;padding:5px;">Total:</td>
							<td style="text-align:right;padding:5px;">{{ sales.reduce((acc, product) => {product.reduce((ac, pre) => {return ac + +pre.quantity},0)},0) }}</td>
							<td style="text-align:right;padding:5px;">{{ sales.reduce((acc, product) => {product.reduce((ac, pre) => {return ac + +parseFloat(pre.price)},0)},0).toFixed(2) }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="table-responsive" id="reportContent" v-else>
				<template v-for="sale in sales">
					<table class="table table-responsive table-bordered">
						<tr>
							<th colspan="5" style="text-align:center;background: #10285C;font-weight:bold;color:white;">{{ sale.Employee_Name }}</th>
						</tr>
						<tr>
							<th>Sl</th>
							<th>Product Code</th>
							<th>Product Name</th>
							<th>Total Sale Qty</th>
							<th>Total Sale Amount</th>
						</tr>
						<tr v-for="(product, index) in sale.products" :key="index">
							<td>{{index + 1}}</td>
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td style="text-align:right;">{{ product.product_qty }}</td>
							<td style="text-align:right;">{{ product.product_price }}</td>
						</tr>
						<tr>
							<th colspan="3" style="text-align:right;padding:5px;">Total:</th>
							<th style="text-align:right;padding:5px;">{{ sale.products.reduce((acc, pre) => {return acc + +pre.product_qty},0) }}</th>
							<th style="text-align:right;padding:5px;">{{sale.products.reduce((acc, pre) => {return acc + +pre.product_price}, 0).toFixed(2)}}</th>
						</tr>
					</table>
				</template>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salesTotalquantityRecord',
		data() {
			return {
				searchType: '',
				dateFrom: moment().format('YYYY-MM-DD'),
				dateTo: moment().format('YYYY-MM-DD'),
				customers: [],
				selectedCustomer: null,
				reportingboss: [],
				selectedReportingboss: null,
				employees1: [],
				selectedEmployee: null,
				products: [],
				selectedProduct: null,
				sales: [],
				searchTypesForRecord: ['', 'customer', 'employee', 'product'],
			}
		},
		computed: {
			employees() {
				if (this.selectedReportingboss != null) {
					return this.employees1.filter(em => em.Reportingboss_Id == this.selectedReportingboss.Reportingboss_Id)
				} else {
					return this.employees1;
				}
			}
		},
		methods: {
			onChangeSearchType() {
				this.sales = [];
				this.products = [];
				this.employees1 = [];
				this.reportingboss = [];
				this.selectedCustomer = null
				this.selectedReportingboss = null
				this.selectedProduct = null
				if (this.searchType == 'product') {
					this.getProducts();
				} else if (this.searchType == 'customer') {
					this.getCustomers();
				} else if (this.searchType == 'employee') {
					this.getReportingBoss();
					this.getEmployee();
				}
			},

			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getReportingBoss() {
				axios.get('/get_reporting_boss').then(res => {
					this.reportingboss = res.data;
				})
			},

			getEmployee() {
				axios.get('/get_employees').then(res => {
					this.employees1 = res.data
				})
			},


			getsalesRecord() {
				let filter = {
					productId: this.selectedProduct == null || this.selectedProduct.Product_SlNo == '' ? '' : this.selectedProduct.Product_SlNo,
					customerId: this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == '' ? '' : this.selectedCustomer.Customer_SlNo,
					reportingBossId: this.selectedReportingboss == null ? '' : this.selectedReportingboss.Reportingboss_Id,
					employeeId: this.selectedEmployee == null ? '' : this.selectedEmployee.Employee_SlNo,
					dateFrom: this.dateFrom,
					dateTo: this.dateTo
				}

				axios.post('/get_totalquantity', filter)
					.then(res => {
						let sales = res.data;
						if (this.searchType != 'employee') {
							sales = _.chain(sales)
								.groupBy('Category')
								.map(sale => {
									return {
										category_name: sale[0].Category,
										products: _.chain(sale)
											.groupBy('Product_SlNo')
											.map(product => {
												return {
													product_code: product[0].Product_Code,
													product_name: product[0].Product_Name,
													quantity: product[0].product_qty,
													price: product[0].product_price
												}
											})
											.value()
									}
								})
								.value();

							this.sales = sales
						} else {
							sales = _.chain(sales)
								.groupBy('Employee_SlNo')
								.map(sale => {
									return {
										Employee_SlNo: sale[0].Employee_SlNo,
										Employee_Name: sale[0].Employee_Name,
										products: sale[0].salesQty,
									}
								})
								.value();

							this.sales = this.selectedEmployee != null ? sales.filter(e => e.Employee_SlNo == this.selectedEmployee.Employee_SlNo) : sales
						}
					})
			},

			async print() {

				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Sales Total Quantity Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
				reportWindow.document.body.innerHTML += reportContent;


				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>