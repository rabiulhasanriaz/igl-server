<?php

use App\Model\Operator;

class PhoneNumber
{

    /*add prefix of number if is not*/
    public static function addNumberPrefix($number)
    {
        $number = trim($number);
        $firstChar = substr($number, 0, 1);
        if ($firstChar == '8') {
            $phone_number = $number;
        } elseif ($firstChar == '0') {
            $phone_number = '88' . $number;
        } elseif ($firstChar == '1') {
            $phone_number = '880' . $number;
        } else {
            $phone_number = $number;
        }

        return $phone_number;
    }


    /*check number is valid or not*/
    public static function isValid($number)
    {
        $number = trim($number);
        if (!is_numeric($number)) {
            return false;
        } else if (!((substr($number, 0, 4) == "8801" && strlen($number) == 13) || (substr($number, 0, 2) == "01" && strlen($number) == 11) || (substr($number, 0, 1) == "1" && strlen($number) == 10))) {
            return false;
        } else if ((substr($number, 0, 5) != "88015") && (substr($number, 0, 5) != "88016") && (substr($number, 0, 5) != "88017") && (substr($number, 0, 5) != "88018") && (substr($number, 0, 5) != "88019") && (substr($number, 0, 5) != "88014") && (substr($number, 0, 5) != "88013")) {
            return false;
        } else {
            return true;
        }
    }


    /*check number operator*/
    public static function checkOperator($number)
    {
        $ope_number = substr($number, 0, 5);
        $operator = Operator::select('id', 'ope_operator_name')->where('ope_number', 'like',  '%'.$ope_number.'%')->first();
        return $operator;
    }


    /*count all operator number*/
    public static function countOperator($numbers)
    {
        $countOperator['1'] = 0;
        $countOperator['2'] = 0;
        $countOperator['3'] = 0;
        $countOperator['4'] = 0;
        $countOperator['5'] = 0;
        $operators = array();
        $getOperators = Operator::select('id', 'ope_operator_name', 'ope_number')->take(5)->get();
        foreach ($getOperators as $getOperator){
            $getOperators1 = explode(',', $getOperator['ope_number']);
            foreach ($getOperators1 as $getOperator1){
                $operators[$getOperator1] = $getOperator['id'];
            }
        }
        $operator = array();
        foreach ($operators as $key=>$value){
            $operator[$key] = '0';
        }
        foreach ($numbers as $number){
            $ope_number = substr($number, 0, 5);
            $operator[$ope_number]++;

        }
        foreach ($operators as $key=>$value){
            $countOperator[$value] = $countOperator[$value] + $operator[$key];
        }
        return $countOperator;
    }

    public static function getOperatorNameForLoadById($id){
        $data = [
            1 => 'airtel',
            2 => 'blink',
            3 => 'gp',
            4 => 'robi',
            5 => 'teletalk'
        ];

        return $data[$id];
    }

    public static function getOperatorNameForLoadByNumber($number){
        // dd($number);
        $operator = self::checkOperator($number)->id;
        return self::getOperatorNameForLoadById($operator);
    }

}
