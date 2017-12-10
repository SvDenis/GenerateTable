<?php

class HtmlCreator
{
    public $params;

    public function __construct($params)
    {
        $this->params = $this->processParams($params);
    }

    /**
     * Precessing incoming params
     * @param $params - income params
     * @return array - processed params
     */
    public function processParams($params)
    {
        $processed = [];
        foreach($params as $param) {
            $cells = explode(",", $param['cells']);
            unset($param['cells']);
            sort($cells);
            foreach($cells as $k=>$cell_num)
            {
                if( $k == 0 ){
                    $processed[$cell_num] = $param;
                    if( $col_row=$this->combineCells($cells) )
                        $processed[$cell_num]['col_row'] = $col_row;
                }else
                    $processed[$cell_num] = [];
            }
        }
        return $processed;
    }

    /**
     * Combine cells into one cell
     * @param $cell_nums - list of cell numbers to combine
     * @return string - combining string for table tag td
     */
    public function combineCells($cell_nums)
    {
        $col_row = "";
        $t_row = [];
        foreach($cell_nums as $num)
        {
            if( $num <= 3 )
                $t_row[1][] = $num;
            elseif( ($num > 3) && ($num <= 6) )
                $t_row[2][] = $num;
            elseif( ($num > 6) && ($num <= 9) )
                $t_row[3][] = $num;
        }
        if( $t_row )
        {
            if( ($rows=count($t_row)) && ($rows > 1) )
                $col_row .= "rowspan='$rows'";
            if( ($cols=count(array_shift($t_row))) && ($cols > 1) )
                $col_row .= ($col_row?" ":"")."colspan='$cols'";

        }
        return $col_row;
    }

    /**
     * Generate page skeleton
     * @return string - page html data
     */
    public function getPage()
    {
        $page = "<html>";
        $page .= "<header><title>Генерация HTML страницы по заданым параметрам</title>";
        $page .= "<style>table{border-collapse: collapse;}table tr{height: 100px;}table tr td{border:1px solid #555;}</style>";
        $page .= "</header><body>";

        $page .= $this->pageBody();

        $page .= "</body></html>";

        return $page;
    }

    /**
     * Generate page skeleton of table
     * @return string - page body html
     */
    public function pageBody()
    {
        $html = "<table><colgroup width='100'><colgroup width='100'><colgroup width='100'>";
        $num = 0;
        for($i = 1; $i <= 3; $i++)
        {
            $html .= "<tr>";
            for($j = 1; $j <= 3; $j++)
            {
                $num++;
                if( $cell=$this->getCellData($num) )
                {
                    $html .= $cell;
                }else continue;
            }
            $html .= "</tr>";
        }

        $html .= "</table>";
        return $html;
    }

    /**
     * Filling cell with data from incoming params and definition cell attributes
     * @param $num - cell number
     * @return null|string - cell html data
     */
    public function getCellData($num)
    {
        $cell = "";

        if( isset($this->params[$num]) )
        {
            if( $this->params[$num] )
            {
                $cell = "<td";
                if( !empty($this->params[$num]['align']) )
                    $cell .= " align='". $this->params[$num]['align'] ."'";
                if( !empty($this->params[$num]['valign']) )
                    $cell .= " valign='". $this->params[$num]['valign'] ."'";
                if( !empty($this->params[$num]['bgcolor']) )
                    $cell .= " bgcolor='". $this->params[$num]['bgcolor'] ."'";
                if( !empty($this->params[$num]['color']) )
                    $cell .= " style='color:". $this->params[$num]['color'] .";'";
                if( !empty($this->params[$num]['col_row']) )
                    $cell .= $this->params[$num]['col_row'];
                $cell .= ">";
                if( !empty($this->params[$num]['text']) )
                    $cell .= $this->params[$num]['text'];
            }
        }else{
            $cell = "<td></td>";
        }

        return $cell?$cell:null;
    }
}



$page_params = array(
                array('text'=>'Текст красного цвета',
                      'cells'=>'1,2,4,5',
                      'align'=>'center',
                      'valign'=>'center',
                      'color'=>'FF0000',
                      'bgcolor'=>'0000FF'
                     ),
                array('text'=>'Текст черного цвета',
                      'cells'=>'3,6,9',
                      'align'=>'center',
                      'valign'=>'top',
                      'color'=>'000000',
                      'bgcolor'=>'FF00FF'
                     ),
                array('text'=>'Текст зеленого цвета',
                      'cells'=>'8',
                      'align'=>'right',
                      'valign'=>'bottom',
                      'color'=>'00FF00',
                      'bgcolor'=>'FFFFFF'
                     )
                );

$creator = new HtmlCreator($page_params);
echo $creator->getPage();