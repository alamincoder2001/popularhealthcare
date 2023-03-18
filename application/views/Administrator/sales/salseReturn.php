<style>
	.v-select {
		margin-top: -2.5px;
		float: right;
		width: 100%;
		margin-left: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 28px;
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
</style>

<div class="row" id="salesReturn">
	<div class="col-12">
		<div class="widget-box ">
			<div class="widget-header rhcolor">
				<h4 class="widget-title">Return Information</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="widget-body" style="background:#fff6f6">
				<div class="widget-main">
					<div class="row">
						<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:10px;">
							<div class="row">
								<div class="form-group">
									<label class="col-xs-4 col-lg-1 control-label no-padding-right" style="margin-top: 3px; margin-bottom:5px;"> Employee </label>
									<div class="col-xs-8 col-lg-3" style="margin-top: 3px; margin-bottom:5px;">
										<v-select v-bind:options="employees" id="employee" v-model="selectedEmployee" label="Employee_Name" placeholder="Select Employee"></v-select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 col-lg-1 control-label no-padding-right" style="margin-top: 3px;"> Customer </label>
									<div class="col-xs-8 col-lg-3" style="margin-top: 3px;">
										<v-select id="customer" v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 col-lg-2 control-label no-padding-right"> Return Date </label>
									<div class="col-xs-8 col-lg-2">
										<input type="date" class="form-control" v-model="salesReturn.returnDate">
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-lg-4">
							<form v-on:submit.prevent="addToCartReturn">
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Product </label>
									<div class="col-xs-8">
										<v-select id="product" v-bind:options="products" v-model="selectedProduct" label="display_text" @input="onChangeProduct"></v-select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right" style="margin-top: 5px;"> Quantity </label>
									<div class="col-xs-8" style="margin-top: 5px;">
										<input type="number" min="0" step="0.01" placeholder="Qty" class="form-control" @input="productReturnTotal" id="quantity" v-model="selectedProduct.quantity" autocomplete="off" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Manufa. Date </label>
									<div class="col-xs-8">
										<input type="date" id="ExpireDate" class="form-control" v-model="selectedProduct.manufac_date" required />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Expire Date </label>
									<div class="col-xs-8">
										<input type="date" id="ExpireDate" class="form-control" v-model="selectedProduct.expire_date" required />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Batch No. </label>
									<div class="col-xs-8">
										<input type="text" placeholder="Batch" class="form-control" v-model="selectedProduct.Batch_No" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Purchase Rate </label>
									<div class="col-xs-8">
										<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" ref="rate" v-model="selectedProduct.Product_Purchase_Rate" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Sale Rate </label>
									<div class="col-xs-8">
										<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" @input="productReturnTotal" ref="rate" v-model="selectedProduct.Product_SellingPrice" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Amount </label>
									<div class="col-xs-8">
										<input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> </label>
									<div class="col-xs-8">
										<button type="submit" class="btn btn-default pull-right">Add to Cart</button>
									</div>
								</div>
							</form>
						</div>

						<div class="col-xs-12 col-lg-8">
							<div class="table-responsive">
								<table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
									<thead>
										<tr class="">
											<th style="width:3%;color:#000;">Sl</th>
											<th style="width:20%;color:#000;">Product Name</th>
											<th style="width:8%;color:#000;">Batch_No</th>
											<th style="width:8%;color:#000;">Manuf. Date</th>
											<th style="width:8%;color:#000;">Ex. Date</th>
											<th style="width:3%;color:#000;">Qty</th>
											<th style="width:2%;color:#000;">Rate</th>
											<th style="width:6%;color:#000;">Total Amount</th>
											<th style="width:0%;color:#000;">Action</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(product, sl) in cart">
											<td>{{ sl + 1 }}</td>
											<td>{{ product.productCode }} - {{ product.name }}</td>
											<td>{{product.Batch_No}}</td>
											<td>{{product.manufac_date}}</td>
											<td>{{product.expire_date}}</td>
											<td>{{ product.quantity }}</td>
											<td>{{ product.salesRate }}</td>
											<td>{{ product.total }}</td>
											<td><a href="" v-on:click.prevent="removeFromReturnCart(sl)"><i class="fa fa-trash"></i></a></td>
										</tr>

										<tr style="font-weight: bold">
											<td style="text-align:right" colspan="5">Total = </td>
											<td style="text-align:center">{{ cart.reduce((prev,curr)=> {return prev + +curr.quantity},0) }} </td>
											<td style="text-align:center"></td>
											<td style="text-align:center"></td>
											<td style="text-align:center"></td>
										</tr>

										<tr>
											<td colspan="9"></td>
										</tr>

										<tr style="font-weight: bold;">
											<td colspan="6">Note</td>
											<td colspan="3">Total</td>
										</tr>

										<tr>
											<td colspan="6">
												<textarea class="form-control" v-model="salesReturn.note"></textarea>
											</td>
											<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ cart.reduce((prev,curr)=> {return prev + +curr.total},0) }}</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div style="text-align:right;margin-top: 10px;">
								<button @click="saveSalesReturn" class="btn btn-success btn-sm text-white">Save Return</button>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salesReturn',
		data() {
			return {
				employees: [],
				selectedEmployee: {
					Employee_SlNo: "<?php echo $this->session->userdata('EmployeeID'); ?>",
					Employee_Name: "<?php echo $this->session->userdata('Employee_name'); ?>",
					Category_ID: "<?php echo $this->session->userdata('Category_ID'); ?>"
				},
				customers: [],
				selectedCustomer: {
					Customer_SlNo: '',
					display_name: 'Select Customer',
				},
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Batch_No: '',
					quantity: 0,
					Product_Purchase_Rate: 0,
					Product_SellingPrice: 0,
					total: 0
				},
				cart: [],
				salesReturn: {
					returnId: parseInt('<?php echo $returnId; ?>'),
					returnDate: moment().format('YYYY-MM-DD'),
					employeeId: "",
					customerId: "",
					total: 0.00,
					note: ''
				},
				userType: '<?php echo $this->session->userdata("accountType"); ?>'
			}
		},
		created() {
			this.getCustomers();
			this.getProducts();
			this.getEmployees();
			if (this.salesReturn.returnId != 0) {
				this.getSaleReturn()
			}
		},
		methods: {
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: '',
						Customer_Name: 'General Customers',
						Customer_Type: 'G',
						display_name: 'General Customers'
					})
				})
			},

			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},

			productReturnTotal() {
				this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
			},

			onChangeProduct() {
				if (this.selectedProduct.Product_SlNo == '') {
					return
				}

				axios.post('/get_batchs', {
						productId: this.selectedProduct.Product_SlNo
					})
					.then(res => {
						this.batches = res.data.filter(item => item.curQty > 0);
					})
				document.querySelector("#quantity").focus();
			},

			addToCartReturn() {
				let product = {
					productId: this.selectedProduct.Product_SlNo,
					productCode: this.selectedProduct.Product_Code,
					name: this.selectedProduct.Product_Name,
					manufac_date: this.selectedProduct.manufac_date,
					expire_date: this.selectedProduct.expire_date,
					Batch_No: this.selectedProduct.Batch_No,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate,
					salesRate: this.selectedProduct.Product_SellingPrice,
					quantity: this.selectedProduct.quantity,
					total: this.selectedProduct.total,
				}

				if (product.productId == '') {
					alert('Select Product');
					return;
				}
				if (product.Batch_No == undefined) {
					alert('Batch No required');
					return;
				}

				if (product.quantity == 0 || product.quantity == '') {
					alert('Enter quantity');
					return;
				}

				if (product.salesRate == 0 || product.salesRate == '') {
					alert('Enter sales rate');
					return;
				}

				let cartInd = this.cart.findIndex(p => p.productId == product.productId);
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}
				this.cart.unshift(product);
				this.clearReturnProduct();
				document.querySelector('#product input[role="combobox"]').focus();
				this.salesReturn.total = this.cart.reduce((acc, pre) => {
					return acc + +parseFloat(pre.total)
				}, 0).toFixed(2);
			},

			removeFromReturnCart(ind) {
				this.cart.splice(ind, 1);
			},

			saveSalesReturn() {
				if (this.selectedCustomer.Customer_SlNo == '') {
					alert("Please select customer")
					document.querySelector("#customer [type='search']").focus()
					return
				}

				if (this.selectedEmployee.Employee_SlNo == '') {
					alert("Please select employee")
					document.querySelector("#employee [type='search']").focus()
					return
				}
				if (this.cart.length == 0) {
					alert("Cart is empty")
					return
				}

				this.salesReturn.employeeId = this.selectedEmployee.Employee_SlNo
				this.salesReturn.customerId = this.selectedCustomer.Customer_SlNo
				let data = {
					salesReturn: this.salesReturn,
					cart: this.cart,
				}

				let url = '/add_sales_return';
				if (this.salesReturn.returnId != 0) {
					url = '/update_sales_return';
				}

				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Success. Do you want to view invoice?');
						if (conf) {
							window.open('/sale_return_invoice/' + r.id, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/salesReturn';
						} else {
							window.location = '/salesReturn';
						}
					}
				})
			},

			clearReturnProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Batch_No: '',
					quantity: 0,
					Product_Purchase_Rate: 0,
					Product_SellingPrice: 0,
					total: 0
				}
			},

			getSaleReturn() {
				axios.post("/get_sale_returns", {
						id: this.salesReturn.returnId
					})
					.then(res => {
						this.salesReturn = {
							returnId: res.data.returns[0].SaleReturn_SlNo,
							returnDate: res.data.returns[0].SaleReturn_ReturnDate,
							customerId: res.data.returns[0].customerId,
							total: res.data.returns[0].SaleReturn_ReturnAmount,
							note: res.data.returns[0].SaleReturn_Description
						}
						res.data.returnDetails.forEach(p => {
							let prod = {
								productId: p.SaleReturnDetailsProduct_SlNo,
								productCode: p.Product_Code,
								name: p.Product_Name,
								Batch_No: p.Batch_No,
								manufac_date: p.manufac_date,
								expire_date: p.expire_date,
								purchaseRate: p.purchaseRate,
								salesRate: p.salesRate,
								quantity: p.SaleReturnDetails_ReturnQuantity,
								total: p.SaleReturnDetails_ReturnAmount,
							}
							this.cart.push(prod)
						})

						this.selectedCustomer = {
							Customer_SlNo: res.data.returns[0].customerId,
							display_name: res.data.returns[0].display_name,
						}
						this.selectedEmployee = {
							Employee_SlNo: res.data.returns[0].employeeId,
							Employee_Name: res.data.returns[0].employee_name,
						}
					})
			}
		}
	})
</script>