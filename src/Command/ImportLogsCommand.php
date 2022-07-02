<?php

namespace App\Command;

use App\Entity\Log;
use App\Entity\LogParser;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use SplFileObject;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'app:import-logs',
    description: 'Import log file into database.',
)]
class ImportLogsCommand extends Command implements SignalableCommandInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // return Command::FAILURE;

        $defaultFile = '%kernel.root_dir%/../src/AppBundle/Data/logs.txt';
        $pointer= 0;
        $fileName = basename($defaultFile);
        //check if file already parse
        $fileParse = $this->getFile($fileName, $defaultFile);
        // var_dump(($fileParse->getParseAt()));
        // return 0;
        if ($fileParse) {
            if ($fileParse->getParseAt()) {
                $helper = $this->getHelper('question');
                $question = new ChoiceQuestion(
                    "This file has been already proceesed at ".$fileParse->getParseAt()->format('d/M/Y:h:i:s O')." \n Woudl you like to import it again.?",
                    // choices can also be PHP objects that implement __toString() method
                    ['No', 'Yes'],
                    1
                );
                $question->setErrorMessage('Choice %s is invalid.');

                $askProcess = $helper->ask($input, $output, $question);
                // return 0;
                if($askProcess=='No'){
                    return Command::SUCCESS;
                }
            }else if($fileParse->getPointer() != 0){
                $helper = $this->getHelper('question');
                $question = new ChoiceQuestion(
                    'This file was intrupted at the line number.  '.$fileParse->getPointer(),
                    // choices can also be PHP objects that implement __toString() method
                    ['Import from start', 'Import from where it was intrupted', 'Do not import'],
                    1
                );
                $question->setErrorMessage('Choice %s is invalid.');

                $askImport = $helper->ask($input, $output, $question);
                $io->writeln("You choose ".$askImport);
                if($askImport === 2){
                    $io->warning("File $fileName will not process.");
                    return Command::SUCCESS;
                }else if($askImport === 0){
                    $pointer = 0;
                }else{
                    $pointer = $fileParse->getPointer();
                }
            }
        }
        $output->writeln('Import starting');

        $logFilePath = $defaultFile;
        $pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "([A-Z]+) ([^ "]+)? HTTP\/[0-9.]+" ([0-9]{3})/';
        $logFile = new SplFileObject($logFilePath);
        while(!$logFile->eof()){
            $logFile->seek($pointer);
            $contents = $logFile->current();
            if(!$contents){
                $io->writeln($fileName.' has been proceed.');
                break;
            }
            $io->writeln($contents);
            if (preg_match($pattern, $contents, $matches)) {
                list($whole_match, $service, $date, $method, $url, $status) = $matches;
                $date = DateTime::createFromFormat("d/M/Y:h:i:s O", $date);
                $log = new Log();
                $log->setServiceName($service);
                $log->setDate($date);
                $log->setStatusCode($status);
                $this->entityManager->persist($log);
                $this->entityManager->flush();
                // echo `$service $method $status`;
            } else {
                // complain if the line didn't match the pattern
                $io->error("Can't parse line ".$pointer." : ".$contents);
            }
            $this->updateFilePointer($fileName, $defaultFile, $pointer);
            $pointer++;

        }
        $this->fileCompleted($fileName,$defaultFile);
        $io->success('File '.$fileName.' has been proccesed.');
        
        return Command::SUCCESS;
    }

    public function getSubscribedSignals(): array
    {
        // return here any of the constants defined by PCNTL extension
        // https://www.php.net/manual/en/pcntl.constants.php
        return [SIGINT, SIGTERM];
    }

    public function handleSignal(int $signal): void
    {
        // var_dump($signal);
        //     if (SIGINT === $signal) {

        //     }
    }


    function getFilePointer($fileName, $filePath)
    {
        $file = $this->getFile($fileName, $filePath);
        return $file->getPointer();
    }

    function fileCompleted($fileName, $filePath){
        $file = $this->getFile($fileName, $filePath);
        $file->setParseAt(new \DateTime('NOW'));
        $this->entityManager->persist($file);
        $this->entityManager->flush();
   }
    function getFile($fileName, $filePath)
    {
        $file = $this->entityManager->getRepository(LogParser::class)->findOneBy(['fileName' => $fileName, 'filePath' => $filePath]);
        return $file;
    }

    function updateFilePointer($fileName, $filePath, $pointer)
    {
        $file = $this->getFile($fileName, $filePath);
        if(!$file){
            $file = $this->createNewFileEntry($fileName, $filePath);
        }

        $file->setPointer($pointer);
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }

    function createNewFileEntry($fileName, $filePath)
    {
        //check if the file already exits?
        $file = $this->getFile($fileName, $filePath);
        if (!$file) {
            $logFile = new LogParser();
            $logFile->setFileName($fileName);
            $logFile->setFilePath($filePath);
            $logFile->setPointer(0);
            $logFile->setCreatedAt(new \DateTime('NOW'));
            $this->entityManager->persist($logFile);
            $this->entityManager->flush();
            return $logFile;
        }
        return $file;
    }
}
