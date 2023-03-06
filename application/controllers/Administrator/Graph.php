<?php
    class Graph extends CI_Controller{
        public function __construct(){
            parent::__construct();
            $access = $this->session->userdata('userId');
            $this->branchId = $this->session->userdata('BRANCHid');
            if($access == '' ){
                redirect("Login");
            }
            $this->load->model('Model_table', "mt", TRUE);
        }
        
        public function graph(){
            $access = $this->mt->userAccess();
            if(!$access){
                redirect(base_url());
            }
            $data['title'] = "Graph";
            $data['content'] = $this->load->view('Administrator/graph/graph', $data, true);
            $this->load->view('Administrator/index', $data);
        }

        public function getGraphData(){
            // Monthly Record
            $data = json_decode($this->input->raw_input_stream);
            $clauses = "";
            if($this->branchId == 1){
                if(isset($data->branchId) && $data->branchId != ""){
                    $clauses .= "and sm.SaleMaster_branchid = '$data->branchId'";
                }
            }else{
                $clauses .= "and sm.SaleMaster_branchid = '$this->branchId'";
            }
            
            $monthlyRecord = [];
            $year = date('Y');
            $month = date('m');
            $dayNumber = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            for($i = 1; $i <= $dayNumber; $i++){
                $date = $year . '-' . $month . '-'. sprintf("%02d", $i);
                $query = $this->db->query("
                    select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as sales_amount 
                    from tbl_salesmaster sm 
                    where sm.SaleMaster_SaleDate = ?
                    and sm.Status = 'a'
                    $clauses
                    group by sm.SaleMaster_SaleDate
                ", [$date]);

                $amount = 0.00;

                if($query->num_rows() == 0){
                    $amount = 0.00;
                } else {
                    $amount = $query->row()->sales_amount;
                }
                $sale = [sprintf("%02d", $i), $amount];
                array_push($monthlyRecord, $sale);
            }

            $yearlyRecord = [];
            for($i = 1; $i <= 12; $i++) {
                $yearMonth = $year . sprintf("%02d", $i);
                $query = $this->db->query("
                    select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as sales_amount 
                    from tbl_salesmaster sm 
                    where extract(year_month from sm.SaleMaster_SaleDate) = ?
                    and sm.Status = 'a'
                    $clauses
                    group by extract(year_month from sm.SaleMaster_SaleDate)
                ", [$yearMonth]);

                $amount = 0.00;
                $monthName = date("M", mktime(0, 0, 0, $i, 10));

                if($query->num_rows() == 0){
                    $amount = 0.00;
                } else {
                    $amount = $query->row()->sales_amount;
                }
                $sale = [$monthName, $amount];
                array_push($yearlyRecord, $sale);
            }

            // Sales text for marquee
            $sales_text = $this->db->query("
                select 
                    concat(
                        'Invoice: ', sm.SaleMaster_InvoiceNo,
                        ', Customer: ', c.Customer_Code, ' - ', c.Customer_Name,
                        ', Amount: ', sm.SaleMaster_TotalSaleAmount,
                        ', Paid: ', sm.SaleMaster_PaidAmount,
                        ', Due: ', sm.SaleMaster_DueAmount
                    ) as sale_text
                from tbl_salesmaster sm 
                join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where sm.Status = 'a'
                $clauses
                order by sm.SaleMaster_SlNo desc limit 20
            ")->result();

            // Today's Sale
            $todaysSale = $this->db->query("
                select 
                    ifnull(sum(ifnull(sm.SaleMaster_TotalSaleAmount, 0)), 0) as total_amount
                from tbl_salesmaster sm
                where sm.Status = 'a'
                and sm.SaleMaster_SaleDate = ?
                $clauses
            ", [date('Y-m-d')])->row()->total_amount;

            // This Month's Sale
            $thisMonthSale = $this->db->query("
                select 
                    ifnull(sum(ifnull(sm.SaleMaster_TotalSaleAmount, 0)), 0) as total_amount
                from tbl_salesmaster sm
                where sm.Status = 'a'
                and month(sm.SaleMaster_SaleDate) = ?
                and year(sm.SaleMaster_SaleDate) = ?
                $clauses
            ", [$month, $year])->row()->total_amount;

            // Today's Cash Collection
            $todaysCollection = $this->db->query("
                select 
                ifnull((
                    select sum(ifnull(sm.SaleMaster_PaidAmount, 0)) 
                    from tbl_salesmaster sm
                    where sm.Status = 'a'
                    " . ($data->branchId == '' ? "" : " and sm.SaleMaster_branchid = '$data->branchId' ") . "
                    and sm.SaleMaster_SaleDate = '" . date('Y-m-d') . "'
                ), 0) +
                ifnull((
                    select sum(ifnull(cp.CPayment_amount, 0)) 
                    from tbl_customer_payment cp
                    where cp.CPayment_status = 'a'
                    and cp.CPayment_TransactionType = 'CR'
                    " . ($data->branchId == '' ? "" : " and cp.CPayment_brunchid = '$data->branchId' ") . "
                    and cp.CPayment_date = '" . date('Y-m-d') . "'
                ), 0) +
                ifnull((
                    select sum(ifnull(ct.In_Amount, 0)) 
                    from tbl_cashtransaction ct
                    where ct.status = 'a'
                    " . ($data->branchId == '' ? "" : " and ct.Tr_branchid = '$data->branchId' ") . "
                    and ct.Tr_date = '" . date('Y-m-d') . "'
                ), 0) as total_amount
            ")->row()->total_amount;

            // Cash Balance
            $cashBalance = $this->mt->getTransactionSummary("", $data->branchId)->cash_balance;

            // Top Customers
            $topCustomers = $this->db->query("
                select 
                c.Customer_Name as customer_name,
                ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as amount
                from tbl_salesmaster sm 
                join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where 1=1
                $clauses
                group by sm.SalseCustomer_IDNo
                order by amount desc 
                limit 10
            ")->result();

            // Top Products
            $topProducts = $this->db->query("
                select 
                    p.Product_Name as product_name,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as sold_quantity
                from tbl_saledetails sd
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                group by sd.Product_IDNo
                order by sold_quantity desc
                limit 5
            ")->result();

            // Customer Due
            $customerDueResult = $this->mt->customerDue("", "", $data->branchId);
            $customerDue = array_sum(array_map(function($due) {
                return $due->dueAmount;
            }, $customerDueResult));

            // Supplier Due
            $supplierDueResult = $this->mt->supplierDue("", "", $data->branchId);
            $supplierDue = array_sum(array_map(function($due) {
                return $due->due;
            }, $supplierDueResult));

            // Bank balance
            $bankTransactions = $this->mt->getBankTransactionSummary("", "", $data->branchId);
            $bankBalance = array_sum(array_map(function($bank){
                return $bank->balance;
            }, $bankTransactions));

            // Invest balance
            $investTransactions = $this->mt->getInvestmentTransactionSummary("", "", $data->branchId);
            $investBalance = array_sum(array_map(function($bank){
                return $bank->balance;
            }, $investTransactions));

            // Loan balance
            $loanTransactions = $this->mt->getLoanTransactionSummary("", "", $data->branchId);
            $loanBalance = array_sum(array_map(function($bank){
                return $bank->balance;
            }, $loanTransactions));

            //Assets Value
            $assets = $this->mt->assetsReport("", "", $data->branchId);
            $assets_value = array_reduce($assets, function($prev, $curr){ return $prev + $curr->approx_amount;});

            //stock value
            $stocks = $this->mt->currentStock("", $data->branchId);
            $stockValue = array_sum(
                array_map(function($product){
                    return $product->stock_value;
                }, $stocks)
            );

            //this month profit loss
            $sales = $this->db->query("
                select 
                    sm.*
                from tbl_salesmaster sm
                where sm.Status = 'a'
                $clauses
                and month(sm.SaleMaster_SaleDate) = ?
                and year(sm.SaleMaster_SaleDate) = ?
            ", [$month, $year])->result();

            foreach($sales as $sale){
                $sale->saleDetails = $this->db->query("
                    select
                        sd.*,
                        (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                        (select sd.SaleDetails_TotalAmount - purchased_amount) as profit_loss
                    from tbl_saledetails sd
                    where sd.SaleMaster_IDNo = ?
                ", $sale->SaleMaster_SlNo)->result();
            }

            $profits = array_reduce($sales, function($prev, $curr){ 
                return $prev + array_reduce($curr->saleDetails, function($p, $c){
                    return $p + $c->profit_loss;
                });
            });

            $total_transport_cost = array_reduce($sales, function($prev, $curr){ 
                return $prev + $curr->SaleMaster_Freight;
            });
            
            $total_discount = array_reduce($sales, function($prev, $curr){ 
                return $prev + $curr->SaleMaster_TotalDiscountAmount;
            });

            $total_vat = array_reduce($sales, function($prev, $curr){ 
                return $prev + $curr->SaleMaster_TaxAmount;
            });


            $other_income_expense = $this->db->query("
                select
                (
                    select ifnull(sum(ct.In_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.status = 'a'
                    " . ($data->branchId == '' ? "" : " and ct.Tr_branchid = '$data->branchId' ") . "
                    and month(ct.Tr_date) = '$month'
                    and year(ct.Tr_date) = '$year'
                ) as income,
            
                (
                    select ifnull(sum(ct.Out_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.status = 'a'
                    " . ($data->branchId == '' ? "" : " and ct.Tr_branchid = '$data->branchId' ") . "
                    and month(ct.Tr_date) = '$month'
                    and year(ct.Tr_date) = '$year'
                ) as expense,

                (
                    select ifnull(sum(it.amount), 0)
                    from tbl_investment_transactions it
                    where it.transaction_type = 'Profit'
                    " . ($data->branchId == '' ? "" : " and it.branch_id = '$data->branchId' ") . "
                    and it.status = 1
                    and month(it.transaction_date) = '$month'
                    and year(it.transaction_date) = '$year'
                ) as profit_distribute,

                (
                    select ifnull(sum(lt.amount), 0)
                    from tbl_loan_transactions lt
                    where lt.transaction_type = 'Interest'
                    " . ($data->branchId == '' ? "" : " and lt.branch_id = '$data->branchId' ") . "
                    and lt.status = 1
                    and month(lt.transaction_date) = '$month'
                    and year(lt.transaction_date) = '$year'
                ) as loan_interest,

                (
                    select ifnull(sum(a.valuation - a.as_amount), 0)
                    from tbl_assets a
                    where a.buy_or_sale = 'sale'
                    " . ($data->branchId == '' ? "" : " and a.branchid = '$data->branchId' ") . "
                    and a.status = 'a'
                    and month(a.as_date) = '$month'
                    and year(a.as_date) = '$year'
                ) as assets_sales_profit_loss,
            
                (
                    select ifnull(sum(ep.total_payment_amount), 0)
                    from tbl_employee_payment ep
                    where ep.status = 'a'
                    " . ($data->branchId == '' ? "" : " and ep.branch_id = '$data->branchId' ") . "
                    and month(ep.payment_date) = '$month'
                    and year(ep.payment_date) = '$year'
                ) as employee_payment,

                (
                    select ifnull(sum(dd.damage_amount), 0) 
                    from tbl_damagedetails dd
                    join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
                    where dd.status = 'a'
                    " . ($data->branchId == '' ? "" : " and d.Damage_brunchid = '$data->branchId' ") . "
                    and month(d.Damage_Date) = '$month'
                    and year(d.Damage_Date) = '$year'
                ) as damaged_amount,

                (
                    select ifnull(sum(rd.SaleReturnDetails_ReturnAmount) - sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails rd
                    join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                    join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                    join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                    where r.Status = 'a'
                    " . ($data->branchId == '' ? "" : " and r.SaleReturn_brunchId = '$data->branchId' ") . "
                    and month(r.SaleReturn_ReturnDate) = '$month'
                    and year(r.SaleReturn_ReturnDate) = '$year'
                ) as returned_amount
            ")->row();

            $net_profit = (
                $profits + $total_transport_cost + 
                $other_income_expense->income + $total_vat
            ) - (
                $total_discount + 
                $other_income_expense->returned_amount + 
                $other_income_expense->damaged_amount + 
                $other_income_expense->expense + 
                $other_income_expense->employee_payment + 
                $other_income_expense->profit_distribute + 
                $other_income_expense->loan_interest + 
                $other_income_expense->assets_sales_profit_loss 
            );


            $responseData = [
                'monthly_record'    => $monthlyRecord,
                'yearly_record'     => $yearlyRecord,
                'sales_text'        => $sales_text,
                'todays_sale'       => $todaysSale,
                'this_month_sale'   => $thisMonthSale,
                'todays_collection' => $todaysCollection,
                'cash_balance'      => $cashBalance,
                'top_customers'     => $topCustomers,
                'top_products'      => $topProducts,
                'customer_due'      => $customerDue,
                'supplier_due'      => $supplierDue,
                'bank_balance'      => $bankBalance,
                'invest_balance'    => $investBalance,
                'loan_balance'      => $loanBalance,
                'asset_value'       => $assets_value,
                'stock_value'       => $stockValue,
                'this_month_profit' => $net_profit,
            ];

            echo json_encode($responseData, JSON_NUMERIC_CHECK);
        }
    }
?>