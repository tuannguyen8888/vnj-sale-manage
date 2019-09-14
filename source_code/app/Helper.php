<?php
class Enums{
    public static $PRODUCT_STATUS = "0|<lable class='label label-danger'>Hết hàng</lable>;1|<lable class='label label-info'>Còn hàng</lable>";
    public static $ORDER_TYPES = "4|<lable class='label label-warning'>ĐH đang nhập</lable>;3|<lable class='label label-danger'>ĐH vi phạm</lable>;2|<lable class='label label-info'>ĐH chuẩn</lable>;1|<lable class='label label-success'>ĐH nhanh</lable>";
    public static $TRANFER_TYPES = "2|<lable class='label label-warning'>Trả hàng</lable>;1|<lable class='label label-primary'>Chuyển kho</lable>";
    public static $IMPORT = "0|Phát sinh khi saler bán cho khách hàng mới;1|Import từ bảng công nợ của kế toán";
}

function employees() {
	if(Session::has('employees_id')) {
		$data = CRUDBooster::first('employees',Session::get('employees_id'));
		return $data;
	}else{
		return false;
	}
}

if (!function_exists('find_string_in_array')) {
    function find_string_in_array ($arr, $string) {
        return array_filter($arr, function($value) use ($string) {
            return strpos($value, $string) !== false;
        });
    }
}

if (!function_exists('get_string_in_array')) {
    function get_string_in_array ($key, $status) {
        $dataenum = (is_array($key))?$key:explode(";",$key);
        $results = find_string_in_array ($dataenum,$status."|");
        if( !empty($results) ) {
            foreach ($results as $value) {
                //$results = array_values($results)[0];
                $val = $lab = '';
                if(strpos($value,'|')!==FALSE) {
                    $draw = explode("|",$value);
                    $val = $draw[0];
                    $lab = $draw[1];
                }else{
                    $val = $lab = $value;
                }
                if ($status == $val){
                    return $lab;
                }
            }

        }
        return '';
    }
}

if (!function_exists('get_product_status')) {
    function get_product_status($status) {
        return get_string_in_array (Enums::$PRODUCT_STATUS, $status);
    }
}

if (!function_exists('get_order_type_name')) {
    function get_order_type_name($order_type) {
        return get_string_in_array (Enums::$ORDER_TYPES, $order_type);
    }
}
if (!function_exists('get_tranfer_type_name')) {
    function get_tranfer_type_name($tranfer_type) {
        return get_string_in_array (Enums::$TRANFER_TYPES, $tranfer_type);
    }
}
if (!function_exists('arrayCopy')) {
    function arrayCopy(array $array)
    {
        $result = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $result[$key] = arrayCopy($val);
            } elseif (is_object($val)) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }
}
if (!function_exists('date_time_format')) {
    function date_time_format($dateTimeStr, $formatInString, $formatOutString) {
        if(!$dateTimeStr) return $dateTimeStr;
        $dateTime = DateTime::createFromFormat($formatInString, $dateTimeStr); // 'Y-m-d H:i:s'
        return $dateTime->format($formatOutString);
    }
}
