<?php


namespace Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Sites extends Command
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $spreadsheet = null;

    protected static $defaultName = 'app:crawl-sites';

    protected function configure()
    {
        $this->setDescription('Crawl sites from given file');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getSpreadSheet();
        $this->findUrlColumn();
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getSpreadSheet(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        if (!$this->spreadsheet) {
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('src/file.xlsx');
        }
        return $this->spreadsheet;
    }

    protected function findUrlColumn()
    {
        foreach ($this->spreadsheet->getAllSheets() as $sheet) {
            $col = $sheet->getHighestDataColumn();
            $col++;
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {

                    if (filter_var($cell->getValue(), FILTER_VALIDATE_URL)) {
                        $ch = curl_init($cell->getValue());
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        if (strstr($cell->getValue(), '://m.')) {
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36');
                        }
                        $content = curl_exec($ch);
//                        $info = curl_getinfo($ch);
                        curl_close($ch);

                        if (preg_match('/name="author" content="(.*)"/', $content, $matches)) {
                            $sheet->getCell($col . $row->getRowIndex())->setValue($matches[1]);
                        } elseif (preg_match('class="author__name".*"(.*)"', $content, $matches)) {
                            $sheet->getCell($col . $row->getRowIndex())->setValue($matches[1]);
                        }

                    }

                }
            }
        }
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Xlsx')->save('src/asdf.xlsx');
    }

}
