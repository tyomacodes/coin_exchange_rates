<?php

namespace App\Command;

use App\Entity\CoinPair;
use App\Entity\Price;
use App\Repository\CoinPairRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class KursCommand extends Command
{
    protected const URL_price = "https://api.binance.com/api/v3/ticker/price";
    protected const URL_avg_price = "https://api.binance.com/api/v3/avgPrice?symbol=";
    protected static $defaultName = 'Kurs';
    protected static $defaultDescription = 'Gets 5 minute average and at the moment prices for passed in CoinPair.';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('CoinPair', InputArgument::OPTIONAL, 'CoinPair');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CoinPairRepository $repository */
        $repository = $this->em->getRepository(CoinPair::class);
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('CoinPair');
        if ($arg1) {
            try {
                $coin = $this->sendRequest(self::URL_price . '?symbol=' . $arg1);
                $io->note(sprintf('You passed an argument: %s', $arg1));
                $coin_avg = $this->sendRequest(self::URL_avg_price . $arg1);
                $coinpair = $repository->findOneBy([
                    'name' => $coin['symbol']
                ]);
                if (!$coinpair instanceof CoinPair) {
                    $coinpair = $this->createNewCoinPair($coin['symbol']);
                }

                $price = $this->createNewPrice($coinpair, $coin['price']);
                $price
                    ->setAvgValue($coin_avg['price'])
                    ->setRatio($coin['price'] / $coin_avg['price']);
                $this->em->flush();
                $io->success('Command executed successfully.');
                return Command::SUCCESS;
            } catch (Exception $e) {
                $io->note($e->getMessage());
                return Command::FAILURE;
            }

        } else {
            $io->note(sprintf('You did not pass an argument, getting prices for every coinpair.' ));
            $coins = $this->sendRequest(self::URL_price);

            foreach ($coins as $coin)
            {
                $coinpair = $repository->findOneBy([
                    'name' => $coin['symbol']
                ]);
                if (!$coinpair instanceof CoinPair) {
                    $coinpair = $this->createNewCoinPair($coin['symbol']);
                }

                $this->createNewPrice($coinpair, $coin['price']);
            }
            $this->em->flush();
            $io->success('Command executed successfully.');
            return Command::SUCCESS;

        }
    }

    protected function sendRequest(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $json = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json, true);
        if (isset($result['code']) && $result['code'] == -1121) {
            throw new RuntimeException($result['msg'] ?? 'Error response.');
        }

        return json_decode($json, true);
    }

    /**
     * @param CoinPair $pair
     * @param float $price
     * @return Price
     */
    protected function createNewPrice(CoinPair $pair, float $price): Price
    {
        $price = (new Price())
            ->setValue($price)
            ->setCoin($pair)
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($price);

        return $price;
    }

    /**
     * @param string $name
     * @return CoinPair
     */
    protected function createNewCoinPair(string $name): CoinPair
    {
        $coinpair = new CoinPair();
        $coinpair->setName($name);

        $this->em->persist($coinpair);
        return $coinpair;
    }
}
