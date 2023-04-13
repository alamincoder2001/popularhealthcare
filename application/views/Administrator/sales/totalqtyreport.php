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

	<div class="row" style="margin-top:15px;" v-if="sales.length > 0">
		<div class="col-md-12" style="margin-bottom: 10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">
				<table class="table table-responsive table-bordered">
					<tr>
						<th style="width: 18%;"></th>
						<template v-for="product in headerProducts">
							<th style="writing-mode: tb-rl; text-align: center;font-size:10px;width:0;">{{product.Product_Name}}</th>
						</template>
						<th style="writing-mode: tb-rl; text-align: center;font-size:8px;width:0;">Total Value</th>
					</tr>
					<tr v-for="sale in sales">
						<th style="text-align: left;font-size:10px;">{{sale.Employee_Name}}</th>
						<template v-for='item in sale.products'>
							<td style="font-size:10px;">{{item.qty}}</td>
						</template>
						<td style="font-size:10px;">{{sale.products.reduce((acc, pre) => {return acc + +pre.sale_rate}, 0).toFixed(2)}}</td>
					</tr>
					<tr style="background: #76767691;" v-if="selectedEmployee == null">
						<th style="text-align: right;font-size:10px;">Total:</th>
						<template v-for='product in headerProducts'>
							<th style="font-size:10px;">{{product.qty}}</th>
						</template>
						<th style="font-size:10px;">
							{{sales.reduce((acc, prod) => {return acc + + prod.products.reduce((ac, pre)=>{return ac + +pre.sale_rate}, 0)}, 0).toFixed(2)}}
						</th>
					</tr>
				</table>
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
				headerProducts: [],
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
			checkproduct(qty, sl) {
				if (check.length > 0) {
					return check[0]['qty'];
				} else {
					return '';
				}
			},
			onChangeSearchType() {
				this.sales = [];
				this.products = [];
				this.employees1 = [];
				this.reportingboss = [];
				this.selectedCustomer = null
				this.selectedReportingboss = null
				this.selectedProduct = null
				this.selectedEmployee = null
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
					categoryId: this.selectedReportingboss != null ? this.selectedReportingboss.category_id : '',
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
				}

				axios.post('/get_totalquantity', filter)
					.then(res => {
						this.headerProducts = res.data.allProducts;
						if (this.searchType == 'employee') {
							if (this.selectedEmployee != null) {
								this.sales = res.data.allEmployee.filter(em => em.Employee_SlNo == this.selectedEmployee.Employee_SlNo);
							} else {
								this.sales = res.data.allEmployee
							}
						} else {
							if (this.searchType == 'product' && this.selectedProduct != null) {
								this.sales = res.data.allProduct.filter(p => p.Product_SlNo == this.selectedProduct.Product_SlNo);
							} else {
								this.sales = res.data.allProduct
							}
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
							<div class="col-xs-6">
								<h4 style="margin:0;text-align:left;">
									${this.selectedReportingboss != null ? 'ReportingBoss: '+this.selectedReportingboss.Employee_Name:""}
									${this.selectedCustomer != null ? 'Customer Name: '+this.selectedCustomer.Customer_Name:""}
								</h4>
							</div>
							<div class="col-xs-6">
								<h4 style="margin:0;text-align:right;">
									${this.selectedReportingboss != null ? 'Contact No: '+this.selectedReportingboss.Employee_ContactNo:""}
									${this.selectedCustomer != null ? 'Contact No: '+this.selectedCustomer.Customer_Mobile:""}
								</h4>
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
				reportWindow.document.title = "Sales Quantity Report"

				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>