<?php
require_once dirname(__FILE__) . '/../bootstrap.php';

abstract class Importer
{
    public static function run()
    {
        $data = self::_readFile();
        $parsedData = array();
        foreach($data as $row) {
            $parsedData[$row[0]] = array($row[1], self::_parseIngredients($row[1]));
        }
        echo '<pre>';
        print_r($parsedData);
    }

    private static function _parseIngredients($ingredientsStr) {
        $ingredientsStr = self::_removeBracket($ingredientsStr, '[', ']');
        $ingredientsStr = self::_removeBracket($ingredientsStr, '(', ')');
        $return  = array();
        foreach(explode(',', $ingredientsStr) as $ingredientName)
            $return[] = preg_replace("/(\n|\r|(\s+))/", " ", str_replace('|', ', ', trim($ingredientName)));
        return $return;
    }

    private static function _removeBracket($ingredientsStr, $bracketStart, $bracketEnd) {
        $matches=  null;
        preg_match_all('#\\' . $bracketStart . '(.*?)\\' . $bracketEnd . '#', $ingredientsStr, $matches);
        $map = array();
        foreach($matches[1] as $match)
        {
            $map[$match] = array_map(create_function('$a', 'return trim($a);'), explode(',', $match));
            $ingredientsStr = str_replace($match, implode('|', $map[$match]), $ingredientsStr);
        }
        return $ingredientsStr;
    }

    private static function _readFile()
    {
        $inputFileName = dirname(__FILE__) . '/data.xlsx';
        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        $rows = array();
        for ($row = 1; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rData = array();
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            foreach($rowData[0] as $col) {
                $rData[] = trim($col);
            }
            $rows[] = $rData;
        }
        return $rows;
    }
}

Importer::run();

// $string = 'SUSHI RICE (Sugar, Vinegar, Salt, Brown Sugar), AVOCADO (11%), CUCUMBER (11%), TASMANIAN SALMON (21%), SEAWEED, SESAME';
// $matches=  null;
// preg_match_all('#\((.*?)\)#', $string, $matches);
// var_dump($matches);