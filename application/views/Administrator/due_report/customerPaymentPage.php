<style>
	.v-select {
		margin-bottom: 5px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
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

	#customerPayment label {
		font-size: 13px;
	}

	#customerPayment select {
		border-radius: 3px;
		padding: 0;
	}

	#customerPayment .add-button {
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
	}

	#customerPayment .add-button:hover {
		background-color: #41add6;
		color: white;
	}
</style>
<div id="customerPayment">
	<div class="row" style="border-bottom: 1px solid #ccc;padding-bottom: 15px;margin-bottom: 15px;">
		<div class="col-md-12">
			<form @submit.prevent="saveCustomerPayment">
				<div class="row">
					<div class="col-md-5 col-md-offset-1">
						<div class="form-group">
							<label class="col-md-4 control-label">Transaction Type</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<select class="form-control" v-model="payment.CPayment_TransactionType" required>
									<option value="CR">Receive</option>
									<option value="CP">Payment</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Payment Type</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<select class="form-control" v-model="payment.CPayment_Paymentby" required>
									<option value="cash">Cash</option>
									<option value="bank">Bank</option>
								</select>
							</div>
						</div>
						<div class="form-group" style="display:none;" v-bind:style="{display: payment.CPayment_Paymentby == 'bank' ? '' : 'none'}">
							<label class="col-md-4 control-label">Bank Account</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select account"></v-select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Employee</label>
							<label class="col-md-1">:</label>
							<div class="col-md-6">
								<select class="form-control" v-if="employees.length == 0"></select>
								<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" v-if="employees.length > 0"></v-select>
							</div>
							<div class="col-md-1" style="padding-left:0;margin-left: -3px;">
								<a href="/employee" target="_blank" class="add-button"><i class="fa fa-plus"></i></a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Customer</label>
							<label class="col-md-1">:</label>
							<div class="col-md-6 col-xs-11">
								<v-select v-bind:options="filterCustomers" v-model="selectedCustomer" label="display_name" @input="getCustomerDue" v-if="customers.length > 0"></v-select>
							</div>
							<div class="col-md-1 col-xs-1" style="padding-left:0;margin-left: -3px;">
								<a href="/customer" target="_blank" class="add-button"><i class="fa fa-plus"></i></a>
							</div>
						</div>
						<div class="form-group" v-if="selectedCustomer != null && selectedCustomer.Customer_SlNo != undefined">
							<label class="col-md-4 control-label">Invoice</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7 col-xs-11">
								<v-select v-bind:options="invoices" v-model="selectedInvoice" label="SaleMaster_InvoiceNo" @input="getCustomerInvoice"></v-select>
							</div>
						</div>
						<!-- <div class="form-group">
							<label class="col-md-4 control-label">Due</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="payment.CPayment_previous_due" disabled>
							</div>
						</div> -->

						<div class="form-group">
							<label class="col-md-4 control-label text-center" style="border: 1px dashed #cbc070;">
								Customer Due <br />
								<p class="m-0">{{parseFloat(payment.CPayment_previous_due).toFixed(2)}}</p>
							</label>
							<label class="col-md-1">:</label>
							<label class="col-md-4 control-label text-center" style="border: 1px dashed #cbc070;">
								Invoice Due <br />
								<p class="m-0">{{invoiceDue}}</p>
							</label>
							<label class="col-md-3 control-label text-center" style="border: 1px dashed #cbc070;">
								Prev. Due <br />
								<p class="m-0">{{previousDue}}</p>
							</label>
						</div>
					</div>

					<div class="col-md-5">
						<div class="form-group">
							<label class="col-md-4 control-label">Payment Date</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<input type="date" class="form-control" v-model="payment.CPayment_date" required @change="getCustomerPayments" v-bind:disabled="userType == 'u' ? true : false">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Description</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="payment.CPayment_notes">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Previous Due</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<input type="number" step="0.01" min="0" class="form-control" v-model="payment.CPayment_adjustment">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Invoice Amount</label>
							<label class="col-md-1">:</label>
							<div class="col-md-7">
								<input type="number" step="0.01" min="0" class="form-control" v-model="payment.CPayment_amount" required>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-7 col-md-offset-5">
								<input type="submit" class="btn btn-success btn-sm" value="Save">
								<input type="button" class="btn btn-danger btn-sm" value="Cancel" @click="resetForm">
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="payments" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.CPayment_invoice }}</td>
							<td>{{ row.CPayment_date }}</td>
							<td>{{ row.Customer_Name }}</td>
							<td>{{ row.transaction_type }}</td>
							<td>{{ row.payment_by }}</td>
							<td>{{ row.totalAmount }}</td>
							<td>{{ row.CPayment_notes }}</td>
							<td>{{ row.CPayment_Addby }}</td>
							<td>
								<button type="button" class="button edit" @click="window.location = `/paymentAndReport/${row.CPayment_id}`">
									<i class="fa fa-file-o"></i>
								</button>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<button type="button" class="button edit" @click="editPayment(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deletePayment(row.CPayment_id)">
										<i class="fa fa-trash"></i>
									</button>
								<?php } ?>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerPayment',
		data() {
			return {
				payment: {
					CPayment_id: 0,
					CPayment_customerID: null,
					CPayment_employeeID: null,
					CPayment_TransactionType: 'CR',
					CPayment_Paymentby: 'cash',
					account_id: null,
					CPayment_date: moment().format('YYYY-MM-DD'),
					CPayment_amount: 0,
					CPayment_adjustment: 0.00,
					CPayment_notes: '',
					CPayment_previous_due: 0.00,
					SaleMaster_InvoiceNo: ''
				},
				payments: [],
				customers: [],
				filterCustomers: [],
				selectedCustomer: {
					display_name: 'Select Customer',
					Customer_Name: ''
				},
				invoices: [],
				selectedInvoice: {
					SaleMaster_InvoiceNo: '',
				},
				invoiceDue: 0,
				previousDue: 0,
				employees: [],
				selectedEmployee: {
					Employee_SlNo: '',
					Employee_Name: 'Select Employee',
				},
				accounts: [],
				selectedAccount: null,
				editAble: false,
				userType: '<?php echo $this->session->userdata("accountType"); ?>',

				columns: [{
						label: 'Transaction Id',
						field: 'CPayment_invoice',
						align: 'center'
					},
					{
						label: 'Date',
						field: 'CPayment_date',
						align: 'center'
					},
					{
						label: 'Customer',
						field: 'Customer_Name',
						align: 'center'
					},
					{
						label: 'Transaction Type',
						field: 'transaction_type',
						align: 'center'
					},
					{
						label: 'Payment by',
						field: 'payment_by',
						align: 'center'
					},
					{
						label: 'Amount',
						field: 'totalAmount',
						align: 'center'
					},
					{
						label: 'Description',
						field: 'CPayment_notes',
						align: 'center'
					},
					{
						label: 'Saved By',
						field: 'CPayment_Addby',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 10,
				filter: ''
			}
		},
		computed: {
			filteredAccounts() {
				let accounts = this.accounts.filter(account => account.status == '1');
				return accounts.map(account => {
					account.display_text = `${account.account_name} - ${account.account_number} (${account.bank_name})`;
					return account;
				})
			},
		},
		watch: {
			selectedEmployee(employee) {
				if (employee == undefined) return;
				let customer = this.customers.filter(item => {
					return item.Derma_Id == employee.Employee_SlNo || item.Nutrition_Id == employee.Employee_SlNo || item.Healthcare_Id == employee.Employee_SlNo
				})
				this.filterCustomers = customer;
			}
		},
		created() {
			this.getCustomers();
			this.getEmployees();
			this.getAccounts();
			this.getCustomerPayments();
		},
		methods: {
			getCustomerPayments() {
				axios.post('/get_customer_payments', {
					data: ''
				}).then(res => {
					this.payments = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
					this.filterCustomers = res.data;
				})
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getCustomerDue() {
				if (this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == undefined) {
					return;
				}
				axios.post('/get_customer_due', {
					customerId: this.selectedCustomer.Customer_SlNo
				}).then(res => {
					this.previousDue = res.data[0].previousDueCustomer - res.data[0].cashReceivedAdjustment
					this.payment.CPayment_previous_due = res.data[0].dueAmount;
					if (this.editAble == true) {
						this.payment.CPayment_previous_due = (+parseFloat(this.invoiceDue)+parseFloat(this.previousDue)+parseFloat(this.payment.CPayment_amount) + parseFloat(this.payment.CPayment_adjustment)).toFixed(2)
					}
				})

				
				axios.post('/get_customer_due_invoice', {
					customerId: this.selectedCustomer.Customer_SlNo
				}).then(res => {
					this.invoices = res.data.filter(invoice => invoice.invoiceDue > 0)
				})
			},

			getCustomerInvoice() {
				if (this.selectedInvoice == null) {
					this.invoiceDue = 0;
					return
				}
				this.invoiceDue = this.selectedInvoice.invoiceDue
			},

			getAccounts() {
				axios.get('/get_bank_accounts')
					.then(res => {
						this.accounts = res.data;
					})
			},
			saveCustomerPayment() {
				if (this.payment.CPayment_Paymentby == 'bank') {
					if (this.selectedAccount == null) {
						alert('Select an account');
						return;
					} else {
						this.payment.account_id = this.selectedAccount.account_id;
					}
				} else {
					this.payment.account_id = null;
				}
				if (this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == undefined) {
					alert('Select Customer');
					return;
				}
				// if (this.selectedInvoice == null || this.selectedInvoice.SaleMaster_InvoiceNo == "") {
				// 	alert('Select Invoice');
				// 	return;
				// }
				if (this.selectedEmployee == null || this.selectedEmployee.Employee_SlNo == undefined) {
					alert('Select Employee');
					return;
				}
				if (parseFloat(this.payment.CPayment_amount) + parseFloat(this.payment.CPayment_adjustment) > parseFloat(this.payment.CPayment_previous_due)) {
					alert("Payment amount does not greater than customer due")
					return
				}
				if (parseFloat(this.payment.CPayment_amount) > parseFloat(this.invoiceDue) && this.selectedInvoice != null) {
					alert("Payment amount does not greater than Invoice due")
					return
				}
				if (parseFloat(this.payment.CPayment_amount) == 0 && this.selectedInvoice != null && this.selectedInvoice.SaleMaster_InvoiceNo != '') {
					alert("Amount field is required")
					return
				}

				if (this.selectedInvoice.SaleMaster_InvoiceNo == '' && parseFloat(this.payment.CPayment_amount) > 0) {
					alert("Can not pay invoice due");
					return
				}

				this.payment.CPayment_customerID = this.selectedCustomer.Customer_SlNo;
				this.payment.CPayment_employeeID = this.selectedEmployee.Employee_SlNo;
				this.payment.SaleMaster_InvoiceNo = this.selectedInvoice.SaleMaster_InvoiceNo;

				let url = '/add_customer_payment';
				if (this.payment.CPayment_id != 0) {
					url = '/update_customer_payment';
				}
				axios.post(url, this.payment).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.getCustomerPayments();
						let invoiceConfirm = confirm('Do you want to view invoice?');
						if (invoiceConfirm == true) {
							window.open('/paymentAndReport/' + r.paymentId, '_blank');
						}
					}
				})
			},
			editPayment(payment) {
				this.editAble = true
				let keys = Object.keys(this.payment);
				keys.forEach(key => {
					this.payment[key] = payment[key];
				})

				this.selectedCustomer = {
					Customer_SlNo: payment.CPayment_customerID,
					Customer_Name: payment.Customer_Name,
					display_name: `${payment.CPayment_customerID} - ${payment.Customer_Name}`
				}

				this.selectedEmployee = {
					Employee_SlNo: payment.CPayment_employeeID,
					Employee_Name: payment.Employee_Name,
				}
				this.selectedInvoice = {
					SaleMaster_InvoiceNo: payment.SaleMaster_InvoiceNo,
					invoiceDue: payment.CPayment_amount
				}
				this.payment.CPayment_previous_due = payment.CPayment_amount+payment.CPayment_adjustment

				if (payment.CPayment_Paymentby == 'bank') {
					this.selectedAccount = {
						account_id: payment.account_id,
						account_name: payment.account_name,
						account_number: payment.account_number,
						bank_name: payment.bank_name,
						display_text: `${payment.account_name} - ${payment.account_number} (${payment.bank_name})`
					}
				}
			},
			deletePayment(paymentId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_customer_payment', {
					paymentId: paymentId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getCustomerPayments();
					}
				})
			},
			resetForm() {
				this.payment.CPayment_id = 0;
				this.payment.CPayment_customerID = '';
				this.payment.CPayment_amount = '';
				this.payment.CPayment_notes = '';

				this.selectedCustomer = null
				this.selectedEmployee = null
				this.selectedInvoice = {
					SaleMaster_InvoiceNo: '',
				}

				this.payment.CPayment_previous_due = 0;
				this.invoiceDue = 0;
				this.previousDue = 0;
				this.editAble = false
			}
		}
	})
</script>