<?php

namespace App\Command;

use App\Entity\CoinPair;
use App\Entity\Price;
use App\Repository\CoinPairRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class KursCommand extends Command
{
    protected CONST URL = "https://api.binance.com/api/v3/ticker/price";
    protected static $defaultName = 'Kurs';
    protected static $defaultDescription = 'Add a short description for your command';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CoinPairRepository $repository */
        $repository = $this->em->getRepository(CoinPair::class);
        $io = new SymfonyStyle($input, $output);
        $coins = $this->sendRequest();
        foreach ($coins as $coin)
        {
            $coinpair = $repository->findOneBy([
                'name' => $coin['symbol']
            ]);
            if(!$coinpair instanceof CoinPair)
            {
                $coinpair = new CoinPair();
                $coinpair->setName($coin['symbol']);
                $this->em->persist($coinpair);
            }
            $price = (new Price())
                ->setValue($coin['price'])
                ->setCoin($coinpair)
                ;
            $this->em->persist($price);
        }

        $this->em->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        return Command::SUCCESS;
    }

    protected function sendRequest(): array
    {
        $ch = curl_init(self::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $json = curl_exec($ch);
        curl_close($ch);
        return json_decode($json, true);
    }
}
