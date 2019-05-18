<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
    use Enums;
    use Illuminate\Support\Facades\Log;

	class AdminGoldCustomersController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "code";
			$this->limit = "20";
			$this->orderby = "name,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_customers";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Mã khách hàng","name"=>"code"];
			$this->col[] = ["label"=>"Mã tạm","name"=>"tmp_code"];
            $this->col[] = ["label"=>"Tên khách hàng","name"=>"name"];
            $this->col[] = ["label"=>"Ngày sinh","name"=>"dob", "callback_php"=>'date_time_format($row->dob, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			$this->col[] = ["label"=>"Tên tiệm vàng","name"=>"store_name"];
			$this->col[] = ["label"=>"Địa chỉ","name"=>"address"];
            $this->col[] = ["label"=>"Số ĐT bàn","name"=>"phone"];
            $this->col[] = ["label"=>"Số ĐT Zalo","name"=>"zalo_phone"];
            $this->col[] = ["label"=>"Nhân viên bán hàng","name"=>"saler_id","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Ngày tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Ngày sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
            $this->form[] = ['label' => 'Mã khách hàng', 'name' => 'code', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-4', 'help' => 'Mã này do kế toán phát sinh', 'readonly' => (CRUDBooster::myPrivilegeId() != 5)];
			$this->form[] = ['label'=>'Mã khách hàng tạm nhập','name'=>'tmp_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-4','help'=>'Mã này do saler phát sinh tạm cho khách hàng mới', 'readonly' => (CRUDBooster::myPrivilegeId() != 2)];
            $this->form[] = ['label'=>'Tên khách hàng','name'=>'name','type'=>'text','validation'=>'required|string|min:3|max:70','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Ngày sinh chủ tiệm','name'=>'dob','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Tên tiệm vàng','name'=>'store_name','type'=>'text','validation'=>'required|string|min:3|max:70','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Số điện thoại bàn','name'=>'phone','type'=>'number','validation'=>'required|numeric','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Số điện thoại di động có Zalo','name'=>'zalo_phone','type'=>'number','validation'=>'required|numeric','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Nhân viên bán hàng','name'=>'saler_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'cms_users,name', 'datatable_where'=>'id_cms_privileges = 2', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            //$this->form[] = ['label'=>'Địa chỉ','name'=>'address','type'=>'textarea','validation'=>'required|min:1|max:255','width'=>'col-sm-10','help'=>'Số nhà, Tên Đường/chợ/ấp/khu phố/thôn/xóm, Xã/phường/thị trấn, Huyện/quận/thị xã/thành phố, Tỉnh/thành phố trực thuộc TW'];
            $this->form[] = ['label'=>'Số nhà','name'=>'address_home_number','type'=>'text','validation'=>'required|min:1|max:20','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Tên đường','name'=>'address_street','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','help'=>'Tên Đường/chợ/ấp/khu phố/thôn/xóm'];
            $this->form[] = ['label'=>'Xã/Phường','name'=>'address_ward','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','help'=>'Xã/phường/thị trấn'];
            $this->form[] = ['label'=>'Quận/Huyện','name'=>'address_district','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','help'=>'Huyện/quận/thị xã/thành phố trực thuộc Tỉnh'];
            $this->form[] = ['label'=>'Tỉnh/Thành','name'=>'address_province','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','help'=>'Tỉnh/thành phố trực thuộc Trung Ương'];
            $this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Code","name"=>"code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tmp Code","name"=>"tmp_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Name","name"=>"name","type"=>"text","required"=>TRUE,"validation"=>"required|string|min:3|max:70","placeholder"=>"Bạn chỉ có thể nhập số"];
			//$this->form[] = ["label"=>"Address","name"=>"address","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Phone","name"=>"phone","type"=>"number","required"=>TRUE,"validation"=>"required|numeric","placeholder"=>"Bạn chỉ có thể nhập số"];
			//$this->form[] = ["label"=>"Import","name"=>"import","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Notes","name"=>"notes","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Deleted By","name"=>"deleted_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
            if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            {
                $query->where('gold_customers.saler_id', CRUDBooster::myId());
            }
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
	        //Your code here
            $postdata['address'] = $postdata['address_home_number'].', '.$postdata['address_street'].', '.$postdata['address_ward'].', '.$postdata['address_district'].', '.$postdata['address_province'];
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here
            $postdata['address'] = $postdata['address_home_number'].', '.$postdata['address_street'].', '.$postdata['address_ward'].', '.$postdata['address_district'].', '.$postdata['address_province'];
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }

	    //By the way, you can still create your own method in here... :)

        public function getSearchCustomer(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $customer_code = $para['customer_code'];

//            Log::debug('$para = '.json_encode($para));
            $customer = DB::table('gold_customers as C')
                ->whereRaw('C.deleted_at is null')
                ->where('C.code', $customer_code)
                //->select('C.id','C.code','C.tmp_code','C.name','C.address','C.phone')
                ->first();
//            Log::debug($customers->tosql());
            if(!$customer) {
                $customer = DB::table('gold_customers as C')
                    ->whereRaw('C.deleted_at is null')
                    ->where('C.tmp_code', $customer_code)
                    //->select('C.id','C.code','C.tmp_code','C.name','C.address','C.phone')
                    ->first();
            }
            if($customer) {
                $debt = DB::table('gold_liabilities as L')
                    ->whereRaw('L.deleted_at is null')
                    ->where('L.customer_id', $customer->id)
                    ->orderBy('L.import_liabilities_id', 'desc')
                    ->first();
            }
            return ['customer'=>$customer, 'debt'=>$debt];
        }

	}