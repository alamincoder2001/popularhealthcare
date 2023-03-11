<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
		height: 30px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
</style>

<div class="row" id="customerDueList">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;padding-bottom:5px;">
		<div class="form-group">
			<div class="col-xs-2">
				<select class="form-select" v-model="searchType" v-on:change="onChangeSearchType" style="width:100%;">
					<option value="all">All</option>
					<option value="employee">By Employee</option>
					<option value="customer">By Customer</option>
				</select>
			</div>
		</div>
		<!-- reporting boss -->
		<div class="form-group" v-if="searchType == 'employee'">
			<div class="col-xs-2">
				<v-select v-bind:options="reportingboss" v-model="selectedReportingboss" label="Employee_Name" placeholder="Select Reporting Boss"></v-select>
			</div>
		</div>
		<!-- employee -->
		<div class="form-group" v-if="searchType == 'employee'">
			<div class="col-xs-2">
				<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" placeholder="Select Employee"></v-select>
			</div>
		</div>
		<!-- customer -->
		<div class="form-group" v-if="searchType == 'customer' || selectedEmployee != null">
			<div class="col-xs-2">
				<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select customer"></v-select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-xs-2">
				<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getDueInvoices" style="border: 0px;height: 30px;padding: 0 10px;">
			</div>
		</div>
	</div>

	<div class="col-md-12" style="display: none" v-bind:style="{display: invoices.length > 0 ? '' : 'none'}">
		<a href="" style="margin: 7px 0;display:block;width:50px;" v-on:click.prevent="print">
			<i class="fa fa-print"></i> Print
		</a>
		<div class="table-responsive" id="reportTable">
			<table class="table table-bordered">
				<tbody>
                    <template v-for="(invoice, index) in invoices">
                        <tr style="background: #d9d9d9;">
                            <th colspan="8">{{invoice.Customer_Code}}-{{invoice.Customer_Name}}</th>
                        </tr>
                        <tr>
                            <th>Sl</th>
                            <th>Invoice No.</th>
                            <th>Sales Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Customer Payment</th>
                            <th>Remaining Balance</th>
                        </tr>
                        <tr v-for="(item, sl) in invoice.customers">
                            <td>{{sl + 1}}</td>
                            <td>{{item.SaleMaster_InvoiceNo}}</td>
                            <td>{{item.SaleMaster_SaleDate}}</td>
                            <td>{{item.SaleMaster_TotalSaleAmount}}</td>
                            <td>{{item.SaleMaster_PaidAmount}}</td>
                            <td>{{item.SaleMaster_DueAmount}}</td>
                            <td>{{item.customerPaymentAmount}}</td>
                            <td>{{item.invoiceDue}}</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="5" style="text-align:center">Total</td>
                            <td style="text-align:center">{{invoice.customers.reduce((ac, pre) => {return ac + +parseFloat(pre.SaleMaster_DueAmount)}, 0).toFixed(2)}}</td>
                            <td style="text-align:center">{{invoice.customers.reduce((ac, pre) => {return ac + +parseFloat(pre.customerPaymentAmount)}, 0).toFixed(2)}}</td>
                            <td style="text-align:center">{{invoice.customers.reduce((ac, pre) => {return ac + +parseFloat(pre.invoiceDue)}, 0).toFixed(2)}}</td>
                        </tr>
                    </template>
                    <tr v-if="invoices.length == 0">
                        <th colspan="8">Not Found Data in Table</th>
                    </tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerDueList',
		data(){
			return {
				searchType: 'all',
				customers1: [],
				selectedCustomer: null,
				reportingboss: [],
				selectedReportingboss: null,
				employees1: [],
				selectedEmployee: null,
				invoices: [],
			}
		},
		created() {
			this.getCustomers();
		},
		computed: {
			employees() {
				if (this.selectedReportingboss != null) {
					return this.employees1.filter(em => em.Reportingboss_Id == this.selectedReportingboss.Reportingboss_Id)
				} else {
					return this.employees1;
				}
			},
			customers() {
				if (this.selectedEmployee != null) {
					return this.customers1.filter(cus => {
						return cus.Derma_Id == this.selectedEmployee.Employee_SlNo || cus.Healthcare_Id == this.selectedEmployee.Employee_SlNo || cus.Nutrition_Id == this.selectedEmployee.Employee_SlNo;
					})
				} else {
					return this.customers1;
				}
			}
		},
        watch: {
            selectedReportingboss(){
                this.selectedEmployee = null
                this.selectedCustomer = null
            }
        },
		methods:{
			onChangeSearchType(){
				if(this.searchType == 'customer' && this.customers.length == 0){
					this.selectedArea = null;
					this.selectedEmployee = null;
					this.getCustomers();
				} else if(this.searchType == 'employee' && this.employees.length == 0) {
					this.selectedArea = null;
					this.selectedCustomer = null;
					this.getEmployees();
					this.getReportingBoss();
				}
				if(this.searchType == 'all'){
					this.selectedCustomer = null;
					this.selectedArea = null;
					this.selectedEmployee = null;
				}
			},
			getReportingBoss() {
				axios.get('/get_reporting_boss').then(res => {
					this.reportingboss = res.data;
				})
			},
			getCustomers(){
				axios.get('/get_customers').then(res => {
					this.customers1 = res.data;
				})
			},
			getEmployees(){
				axios.get('/get_employees').then(res => {
					this.employees1 = res.data;
				})
			},
			getDueInvoices(){
				if(this.searchType == 'customer' && this.selectedCustomer == null){
					alert('Select customer');
					return;
				}
				if(this.searchType == 'employee' && this.selectedEmployee == null){
					alert('Select Employee');
					return;
				}

				let data = {					
					customerId: this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo,
				}
				
				if (this.searchType == 'employee') {
					if (this.selectedEmployee.Department_ID == 1) {
						data.employeeId = this.selectedEmployee.Employee_SlNo
					}else if(this.selectedEmployee.Department_ID == 2){
						data.employeeId = this.selectedEmployee.Employee_SlNo
					}else{
						data.employeeId = this.selectedEmployee.Employee_SlNo
					}
				}

			    axios.post('/get_customer_due_invoice', data).
                    then(res => {
                        let invoices = res.data
                        invoices = _.chain(invoices)
                                .groupBy('SalseCustomer_IDNo')
                                .map(invoice => {
                                    return {
                                        Customer_Code: invoice[0].Customer_Code,
                                        Customer_Name: invoice[0].Customer_Name,
                                        customers: _.chain(invoice)
                                            .groupBy('SaleMaster_SlNo')
                                            .map(customer => {
                                                return {
                                                    SaleMaster_SaleDate: customer[0].SaleMaster_SaleDate,
                                                    SaleMaster_InvoiceNo: customer[0].SaleMaster_InvoiceNo,
                                                    SaleMaster_TotalSaleAmount: customer[0].SaleMaster_TotalSaleAmount,
                                                    SaleMaster_PaidAmount: customer[0].SaleMaster_PaidAmount,
                                                    SaleMaster_DueAmount: customer[0].SaleMaster_DueAmount,
                                                    customerPaymentAmount: customer[0].customerPaymentAmount,
                                                    invoiceDue: customer[0].invoiceDue,
                                                }
                                            })
                                            .value()
                                    }
                                })
                                .value();

                            this.invoices = invoices
				})
			},
			async print(){
				let reportContent = `
					<div class="container">
						<h3 style="text-align:center;text-decoration:underline;">Customer Due Invoice List</h3>
                        ${this.selectedEmployee != null ? "<h5>Employee Name:- "+this.selectedEmployee.Employee_Name+"</h5>" : ""}
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>