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
					<option value="area">By Area</option>
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
		<!-- area -->
		<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'area' ? '' : 'none'}">
			<div class="col-xs-2">
				<v-select v-bind:options="areas" v-model="selectedArea" label="District_Name" placeholder="Select area"></v-select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-xs-2">
				<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getDues" style="border: 0px;height: 30px;padding: 0 10px;">
			</div>
		</div>
	</div>

	<div class="col-md-12" style="display: none" v-bind:style="{display: dues.length > 0 ? '' : 'none'}">
		<a href="" style="margin: 7px 0;display:block;width:50px;" v-on:click.prevent="print">
			<i class="fa fa-print"></i> Print
		</a>
		<div class="table-responsive" id="reportTable">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Customer Id</th>
						<th>Customer Name</th>
						<th>Owner Name</th>
						<th>Address</th>
						<th>Customer Mobile</th>
						<th>Due Amount</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="data in dues">
						<td>{{ data.Customer_Code }}</td>
						<td>{{ data.Customer_Name }}</td>
						<td>{{ data.owner_name }}</td>
						<td>{{ data.Customer_Address }}</td>
						<td>{{ data.Customer_Mobile }}</td>
						<td style="text-align:right">{{ parseFloat(data.dueAmount).toFixed(2) }}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr style="font-weight:bold;">
						<td colspan="5" style="text-align:right">Total Due</td>
						<td style="text-align:right">{{ parseFloat(totalDue).toFixed(2) }}</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerDueList',
		data(){
			return {
				searchType: 'all',
				customers1: [],
				selectedCustomer: null,
				areas: [],
				selectedArea: null,
				reportingboss: [],
				selectedReportingboss: null,
				employees1: [],
				selectedEmployee: null,
				dues: [],
				totalDue: 0.00
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
		methods:{
			onChangeSearchType(){
				if(this.searchType == 'customer' && this.customers.length == 0){
					this.selectedArea = null;
					this.selectedEmployee = null;
					this.getCustomers();
				} else if(this.searchType == 'area' && this.areas.length == 0) {
					this.selectedCustomer = null;
					this.selectedEmployee = null;
					this.getAreas();
				}else if(this.searchType == 'employee' && this.employees.length == 0) {
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
			getAreas() {
				axios.get('/get_districts').then(res => {
					this.areas = res.data;
				})
			},
			getEmployees(){
				axios.get('/get_employees').then(res => {
					this.employees1 = res.data;
				})
			},
			getDues(){
				if(this.searchType == 'customer' && this.selectedCustomer == null){
					alert('Select customer');
					console.log(this.selectedCustomer);
					return;
				}
				// if(this.searchType == 'employee' && this.selectedEmployee == null){
				// 	alert('Select Employee');
				// 	return;
				// }

				let data = {					
					customerId: this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo,
					districtId: this.selectedArea == null ? null : this.selectedArea.District_SlNo,
				}
				
				if(this.selectedReportingboss == null){
					if (this.searchType == 'employee') {
						if (this.selectedEmployee.Department_ID == 1) {
							data.Derma_Id = this.selectedEmployee.Employee_SlNo
						}else if(this.selectedEmployee.Department_ID == 2){
							data.Healthcare_Id = this.selectedEmployee.Employee_SlNo
						}else{
							data.Nutrition_Id = this.selectedEmployee.Employee_SlNo
						}
					}
				}else{
					data.Reportingboss_Id = this.selectedReportingboss.Employee_SlNo
				}

				axios.post('/get_customer_due', data).then(res => {
					if(this.searchType == 'customer'){
						this.dues = res.data;
					} else {
						this.dues = res.data.filter(d => parseFloat(d.dueAmount) != 0);
					}
					this.totalDue = this.dues.reduce((prev, cur) => { return prev + parseFloat(cur.dueAmount) }, 0);
				})
			},
			async print(){
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Customer due report</h4 style="text-align:center">
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