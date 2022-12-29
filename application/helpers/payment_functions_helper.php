<?php defined('BASEPATH') or exit('No direct script access allowed');

//get payment gateway
if (!function_exists('get_payment_gateway')) {
    function get_payment_gateway($name_key)
    {
        $ci =& get_instance();
        return $ci->settings_model->get_payment_gateway($name_key);
    }
}

//get active payment gateways
if (!function_exists('get_active_payment_gateways')) {
    function get_active_payment_gateways()
    {
        $ci =& get_instance();
        return $ci->settings_model->get_active_payment_gateways();
    }
}

//get payment method
if (!function_exists('get_payment_method')) {
    function get_payment_method($payment_method)
    {
        if ($payment_method == "Bank Transfer") {
            return trans("bank_transfer");
        } elseif ($payment_method == "Cash On Delivery") {
            return trans("cash_on_delivery");
        } else {
            return $payment_method;
        }
    }
}
//get payment status
if (!function_exists('get_payment_status')) {
    function get_payment_status($payment_status)
    {
        if ($payment_status == "payment_received") {
            return trans("payment_received");
        } elseif ($payment_status == "awaiting_payment") {
            return trans("awaiting_payment");
        } elseif ($payment_status == "Completed") {
            return trans("completed");
        } else {
            return $payment_status;
        }
    }
}



//price currency format
if (!function_exists('price_currency_format')) {
    function price_currency_format($price, $currency_code)
    {
        $ci =& get_instance();
        
        $currency = $ci->currency_model->get_symbol($currency_code);
        $space = "";
        if ($currency->space_money_symbol == 1) {
            $space = " ";
        }
        if ($currency->symbol_direction == "left") {
            $price = "<span>" . $currency->symbol . "</span>" . $space . $price;
        } else {
            $price = $price . $space . "<span>" . $currency->symbol . "</span>";
        }
        return $price;
    }
}


//price formatted
if (!function_exists('price_formatted')) {
    function price_formatted($price, $currency_code, $convert_currency = false)
    {
        $ci =& get_instance();
        $price = $price / 100;

        $dec_point = '.';
        $thousands_sep = ',';
        if (isset($ci->currencies[$currency_code]) && $ci->currencies[$currency_code]->currency_format != 'us') {
            $dec_point = ',';
            $thousands_sep = '.';
        }

        if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
            $price = number_format($price, 0, $dec_point, $thousands_sep);
        } else {
            $price = number_format($price, 2, $dec_point, $thousands_sep);
        }
        $price = price_currency_format($price, $currency_code);
        return $price;
    }
}

//get price
if (!function_exists('get_price')) {
    function get_price($price, $format_type)
    {
        $ci =& get_instance();
        if ($format_type == "input") {
            $price = $price / 100;
            if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                $price = number_format($price, 0, ".", "");
            } else {
                $price = number_format($price, 2, ".", "");
            }
            if ($ci->thousands_separator == ',') {
                $price = str_replace('.', ',', $price);
            }
            return $price;
        } elseif ($format_type == "decimal") {
            $price = $price / 100;
            if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                return number_format($price, 0, ".", "");
            } else {
                return number_format($price, 2, ".", "");
            }
        } elseif ($format_type == "database") {
            $price = str_replace(',', '.', $price);
            $price = floatval($price);
            $price = number_format($price, 2, '.', '') * 100;
            return $price;
        } elseif ($format_type == "separator_format") {
            $price = $price / 100;
            $dec_point = '.';
            $thousands_sep = ',';
            if ($ci->thousands_separator != '.') {
                $dec_point = ',';
                $thousands_sep = '.';
            }
            return number_format($price, 2, $dec_point, $thousands_sep);
        }
    }
}

?>