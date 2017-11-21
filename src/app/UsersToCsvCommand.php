<?php  namespace App;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UsersToCsvCommand extends Command
{

    private $notAllowedCommands = [
                                'DELETE',
                                'TRUNCATE',
                                'DROP',
                                'USE'
                                ];

    private function createConnection()
    {
        $conf = new Configuration();
        $conn = DriverManager::getConnection([
            "path" => "app.db",
            "driver" => "pdo_sqlite"
        ], $conf);

        return $conn;
    }

    public function isSafeQuery($query, &$response)
    {
        $response = '';
        $temp= explode(' ', strtoupper($query));

        if(!count(array_intersect(['USERS'], $temp)) > 0)
        {
            $response = 'query does not contain [from users] statement';
            return false;
        }

        if(!count(array_intersect(['SELECT'], $temp)) > 0)
        {
            $response = 'query does not contain [select bla bla] statement';
            return false;
        }

        //all other sql
        if(count(array_intersect($this->notAllowedCommands, $temp)) > 0)
        {
            $response = "Query contains bad statements [".implode(",",$this->notAllowedCommands)."]!";
            return false;
        }
        else
        {
            return true;
        }
    }

    public function writeToCsv($filename, $data, $exception = false)
    {
        if (count($data) === 0)
            return false;

        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        try {
            $csv = Writer::createFromPath($filename, "w");
            $csv->setOutputBOM(Reader::BOM_UTF8);

            //CSV header (to be user later)
            //$csv->insertOne(['firstname', 'lastname', 'email']);

            //test exception
            if ($exception)
              $i = 6/0;

            // insert data into the CSV
            $csv->insertAll($data);

            return true;
        }
        catch(\Exception $e)
        {

            //clean up
            if (file_exists($filename))
                unlink($filename);

            throw $e;
        }
    }

    protected function configure()
    {
        $this
            ->setName("users")
            ->setDescription("run SQL query and store result to file in csv format. Example #php app users 'select * from users' users.csv")
            ->addArgument('input', InputArgument::REQUIRED, 'Input query.')
            ->addArgument('output', InputArgument::REQUIRED, 'Output CSV file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inQuery  = $input->getArgument('input');
        $outfile = $input->getArgument('output');

        $safeResponse = '';
        if(!$this->isSafeQuery($inQuery, $safeResponse))
        {
            throw new \Exception($safeResponse);
        }

        if (file_exists($outfile))
        {
            unlink($outfile);
        }

        $conn = $this->createConnection();

        try {
            $rows = $stmt = $conn->query($inQuery)->fetchAll();

            // Verify that uniq "id" column is included, this is not done in isSafeQuery in case of join and more complex query
            // What is expected to be a complete dump?
            if ( (count($rows) > 0) && !array_key_exists("id", $rows[0]))
                throw new \Exception("Column \"id\" must be selected in query.");

            if ($this->writeToCsv($outfile, $rows))
            {

            }
            //    $this->delete_data($conn, $rows);

        } finally {
            $conn->close();
        }



    }
}

?>