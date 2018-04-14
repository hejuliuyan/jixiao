<?php

namespace StarHub\Excel;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;
use StarHubl\Exception\ExcelException;

class Excel
{
    private $mode;
    private $suffix;

    protected $file;
    protected $allowed_extensions = ["xlsx"];

    /**
     * Excel constructor.
     * @param string $mode 格式
     * @param string $suffix 后缀名
     */
    public function __construct($mode = 'Excel2007', $suffix = '.xlsx')
    {
        $this->mode = $mode;
        $this->suffix = $suffix;
    }

    /**
     * 导出终审表
     *
     * @param string $title 文件名
     * @param array $data 所有数据
     * @param int $count 奖金内容的长度
     * @param string $path 保存路径
     */
    public function passTable($title, $data, $count, $path = '')
    {
        //创建实例
        $objPHPExcel = new PHPExcel();

        //填充数据
        $objPHPExcel->getActiveSheet()
            ->fromArray(
                $data,  // The data to set
                NULL,   // Array values with this value will not be set
                'A1'  // Top left coordinate of the worksheet range where
            );

        //参数
        $colName = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $dataCount = count($data);

        //合并单元格
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$colName.'1' );
        $objPHPExcel->getActiveSheet()->mergeCells('A2:'.$colName.'2' );

        //设置列宽
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true); //自适应宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(33);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);

        //设置行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(36);

        //设置字体
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$colName.'1')->getFont()->setName('宋体')->setBold(true)->setSize(16); //主标题
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$colName.'2')->getFont()->setName('宋体')->setBold(true)->setSize(14); //副标题
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$colName.'3')->getFont()->setName('宋体')->setBold(true)->setSize(11); //项目标题
        $objPHPExcel->getActiveSheet()->getStyle('A4:'.$colName.($count + 3))->getFont()->setName('宋体')->setBold(false)->setSize(11); //项目内容
        $objPHPExcel->getActiveSheet()->getStyle('A'.($count + 3).':'.$colName.($count + 3))->getFont()->setName('宋体')->setBold(true)->setSize(11); //奖金总计

        //自动换行和居中
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$colName.$dataCount)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$colName.$dataCount)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$colName.$dataCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$colName.($count + 3))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        //设置背景色
        $objPHPExcel->getActiveSheet()->getStyle('D'.($count + 3 ).':'.$colName.($count + 3))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFF00');

        //创建写容器
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->mode);

        //下载
        $this->downFiles($objWriter, $title, $path);
    }

    /**
     * 下载Excel文件
     * @param string $writer 写容器
     * @param string $title 名称
     * @param string $path 文件路径
     */
    public function downFiles($writer, $title, $path) {
//        $name = mb_convert_encoding($title.$this->suffix, "gb2312", "UTF-8");
        $name = $title.$this->suffix;

        if(empty($path)) {
            //设置头部信息
            header("Pragma:public");
            header("Expires:0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header('Content-Disposition: attachment;filename="'.$name.'"');//设置excel文件名
            header("Content-Transfer-Encoding:binary");

            //下载
            $writer->save('php://output');
        }else {
            //保存数据
            $savePath = $path.$name;
            $writer->save($savePath);

            //下载文件流
            header('Location: /'.$savePath);
        }

        //解决“部分内容有问题...”提示
        //exit();
    }

    protected function checkAllowedExtensionsOrFail()
    {
        $extension = strtolower($this->file->getClientOriginalExtension());
        if ($extension && !in_array($extension, $this->allowed_extensions)) {
            throw new ExcelException('extension');
        }
    }
}