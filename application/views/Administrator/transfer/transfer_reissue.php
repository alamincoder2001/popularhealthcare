<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
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
</style>

<div id="productTransfer">
    <div class="row">
        <div class="col-md-7">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Transfer Information</h4>
                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>

                        <a href="#" data-action="close">
                            <i class="ace-icon fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="widget-main" style="min-height:117px;">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="control-label col-md-4" style="padding:0;">Transfer date</label>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" v-model="transfer.transfer_date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4" style="padding:0;">Transfer by</label>
                                    <div class="col-md-8">
                                        <select class="form-control" v-bind:style="{display: employees.length > 0 ? 'none' : ''}"></select>
                                        <v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" v-bind:style="{display: employees.length > 0 ? '' : 'none'}"></v-select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4" style="padding:0;">Transfer to</label>
                                    <div class="col-md-8">
                                        <select class="form-control" v-bind:style="{display: branches.length > 0 ? 'none' : ''}"></select>
                                        <v-select v-bind:options="branches" v-model="selectedBranch" label="Brunch_name" v-bind:style="{display: branches.length > 0 ? '' : 'none'}"></v-select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <textarea class="form-control" style="min-height:84px" placeholder="Note" v-model="transfer.note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Product Information</h4>
                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-up"></i>
                        </a>

                        <a href="#" data-action="close">
                            <i class="ace-icon fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="widget-main" style="min-height:117px;">
                        <div class="row">
                            <div class="col-md-9">
                                <form v-on:submit.prevent="addToCart()">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Product</label>
                                        <div class="col-md-8">
                                            <select class="form-control" v-bind:style="{display: products.length > 0 ? 'none' : ''}"></select>
                                            <v-select id="product" v-bind:options="products" v-model="selectedProduct" label="display_text" v-on:input="onChangeProduct" v-bind:style="{display: products.length > 0 ? '' : 'none'}"></v-select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Batch</label>
                                        <div class="col-md-8">
                                            <v-select id="batch" v-bind:options="batches" v-model="selectedBatch" label="display_text" @input="BatchOnChange"></v-select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Quantity</label>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" v-model="quantity" ref="quantity" required v-on:input="productTotal">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Amount</label>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" v-model="total" ref="total" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <input type="submit" class="btn btn-default pull-right btn-xs" value="Add to Cart">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-3">
                                <div style="width:100%;min-height:70px;background-color:#f5f5f5;text-align:center;border: 1px solid #8d8d8d;">
                                    <h6 style="padding:3px;margin:0;background-color:#8d8d8d;color:white;">Stock</h6>
                                    <div v-if="selectedProduct != null" style="display:none;" v-bind:style="{display: selectedProduct == null ? 'none' : ''}">
                                        <span style="padding:0;margin:0;font-size:18px;font-weight:bold;" v-bind:style="{color: productStock > 0 ? 'green' : 'red'}">{{ productStock }}</span><br>
                                        {{ selectedProduct.Unit_Name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Batch No.</th>
                            <th>Manufac_date</th>
                            <th>Expire_date</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody style="display:none" v-bind:style="{display:cart.length > 0 ? '' : 'none'}">
                        <tr v-for="(product, sl) in cart">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ product.product_code }}</td>
                            <td>{{ product.name }}</td>
                            <td>{{ product.Batch_No }}</td>
                            <td>{{ product.manufac_date }}</td>
                            <td>{{ product.expire_date }}</td>
                            <td>
                                {{product.quantity}}
                            </td>
                            <td>{{ product.total }}</td>
                            <td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row" style="display:none" v-bind:style="{display:cart.length > 0 ? '' : 'none'}">
        <div class="col-md-12">
            <button class="btn btn-success pull-right" v-on:click="saveProductTransfer">Re Issue</button>
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
        el: '#productTransfer',
        data() {
            return {
                transferId: parseInt('<?php echo $transferId; ?>'),
                transfer: {
                    transfer_id: 0,
                    transfer_date: moment().format('YYYY-MM-DD'),
                    transfer_by: '',
                    transfer_from: '',
                    transfer_to: '',
                    note: '',
                    total_amount: 0.00,
                    transfer_issue: "",
                    transferId: 0,
                },
                cart: [],
                employees: [],
                selectedEmployee: null,
                branches: [],
                selectedBranch: null,
                products: [],
                selectedProduct: null,
                batches: [],
                selectedBatch: null,
                productStock: 0,
                quantity: '',
                total: '',
            }
        },
        async created() {
            this.getEmployees();
            this.getBranches();
            this.getProducts();

            if (this.transferId != 0) {
                await this.getTransfer();
            }
        },
        methods: {
            getEmployees() {
                axios.get('/get_employees').then(res => {
                    this.employees = res.data;
                })
            },

            getBranches() {
                axios.get('/get_branches').then(res => {
                    let currentBranchId = parseInt("<?php echo $this->session->userdata('BRANCHid'); ?>");
                    let currentBranchInd = res.data.findIndex(branch => branch.brunch_id == currentBranchId);
                    res.data.splice(currentBranchInd, 1);
                    this.branches = res.data;
                })
            },

            getProducts() {
                axios.post('/get_products', {
                    isService: 'false'
                }).then(res => {
                    this.products = res.data;
                })
            },

            async onChangeProduct() {
                if (this.selectedProduct == null) {
                    return;
                }
                this.selectedBatch = null

                axios.post('/get_batchs', {
                        productId: this.selectedProduct.Product_SlNo
                    })
                    .then(res => {
                        this.batches = res.data.filter(item => item.curQty > 0);
                    })
                this.productStock = await this.getProductStock(this.selectedProduct.Product_SlNo);
            },

            BatchOnChange() {
                if (this.selectedBatch == null) {
                    return
                }
                this.productStock = this.selectedBatch.curQty

                this.$refs.quantity.focus();
            },

            async getProductStock(productId) {
                let stock = await axios.post('/get_product_stock', {
                    productId: productId
                }).then(res => {
                    return res.data;
                })
                return stock;
            },

            productTotal() {
                if (this.selectedProduct == null) {
                    return;
                }
                this.total = this.quantity * this.selectedProduct.Product_Purchase_Rate;
            },

            addToCart() {
                if (this.selectedProduct == null) {
                    alert('Select product');
                    return;
                }
                if (this.selectedBatch == null) {
                    alert('Select Batch');
                    return;
                }
                if (parseFloat(this.productStock) < parseFloat(this.quantity)) {
                    alert('Stock not available');
                    return;
                }
                let cartProduct = {
                    product_id   : this.selectedProduct.Product_SlNo,
                    product_code : this.selectedProduct.Product_Code,
                    name         : this.selectedProduct.Product_Name,
                    Batch_No     : this.selectedBatch.batch_no,
                    manufac_date : this.selectedBatch.manufac_date,
                    expire_date  : this.selectedBatch.expire_date,
                    quantity     : this.quantity,
                    purchase_rate: this.selectedProduct.Product_Purchase_Rate,
                    total        : this.total
                }

                let cartInd = this.cart.findIndex(p => p.product_id == cartProduct.product_id && p.Batch_No == cartProduct.Batch_No);
                if (cartInd > -1) {
                    this.cart.splice(cartInd, 1);
                }

                this.cart.push(cartProduct);

                this.selectedProduct = null;
                this.quantity = '';
                this.total = '';
                document.querySelector('#product input[role="combobox"]').focus();
            },

            async onChangeCartQuantity(productId) {
                let cartInd = this.cart.findIndex(product => product.product_id == productId);

                if (this.transfer.transfer_id == 0) {
                    let stock = await this.getProductStock(productId);

                    if (this.cart[cartInd].quantity > stock) {
                        alert('Stock not available');
                        this.cart[cartInd].quantity = stock;
                    }
                }

                this.cart[cartInd].total = this.cart[cartInd].quantity * this.cart[cartInd].purchase_rate;

            },

            removeFromCart(cartInd) {
                this.cart.splice(cartInd, 1);
            },

            saveProductTransfer() {
                if (this.transfer.transfer_date == null) {
                    alert('Select transfer date');
                    return;
                }

                if (this.selectedEmployee == null) {
                    alert('Select transfer by');
                    return;
                }

                if (this.selectedBranch == null) {
                    alert('Select branch');
                    return;
                }

                if (this.transfer.transfer_issue == 'true') {
                    alert('Already transfer this invoice');
                    return
                }

                let data = {
                    transfer: this.transfer,
                    cart: this.cart
                }

                this.transfer.total_amount = this.cart.reduce((p, c) => {
                    return p + +c.total
                }, 0);
                this.transfer.transfer_by = this.selectedEmployee.Employee_SlNo;
                this.transfer.transfer_to = this.selectedBranch.brunch_id;
                this.transfer.transferId = this.transferId
                this.transfer.transfer_issue = 'true'


                let url = '/add_product_transfer';
                if (this.transfer.transfer_id != 0) {
                    url = '/update_product_transfer';
                }
                axios.post(url, data).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        location.reload();
                    }
                })
            },

            async getTransfer() {
                let transfer = await axios.post('/get_transfers', {
                    transferId: this.transferId
                }).then(res => {
                    return res.data[0];
                })

                this.transfer.note = transfer.note
                this.transfer.total_amount = transfer.total_amount
                this.transfer.transfer_issue = transfer.transfer_issue

                let transferDetails = await axios.post('/get_transfer_details', {
                    transferId: this.transferId
                }).then(res => {
                    return res.data;
                })

                this.cart = transferDetails.map(td => {
                    let product = {
                        product_id: td.product_id,
                        name: td.Product_Name,
                        product_code: td.Product_Code,
                        Batch_No: td.Batch_No,
                        manufac_date: td.manufac_date,
                        expire_date: td.expire_date,
                        quantity: td.quantity,
                        purchase_rate: td.purchase_rate,
                        total: td.total
                    }

                    return product;
                });
            }
        }
    })
</script>