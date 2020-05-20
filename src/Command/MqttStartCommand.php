<?php

namespace App\Command;

use App\Entity\Data;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use karpy47\PhpMqttClient\MQTTClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MqttStartCommand extends Command
{
    protected static $defaultName = 'mqtt:start';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Démarrer le serveur MQTT');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = new MQTTClient('test.mosquitto.org', 1883);
        $client->sendConnect(5599, false, 999);
        $client->sendSubscribe(["EPSI/DHT11/EPSIRON/ALL"]);
        $io->success("Serveur MQTT démarré, en attente de résultats...");
        $this->getPublishMessages($client, $io);

        return 0;
    }

    /**
     * @param $client MQTTClient
     * @param $io SymfonyStyle
     */
    private function getPublishMessages($client, $io){
        $messages = $client->getPublishMessages();
        foreach ($messages as $message) {
            $io->comment("[".$message['topic']."] ".$message['message']);

            $data = json_decode($message);
            $type = $data['type'];
            $value = $data['value'];
            $unit = $data['unit'];

            $this->createTypeIfNotExist($type);

            $currentType = $this->entityManager->getRepository(Type::class)->findOneBy(['name' => $type]);
            $this->saveData($currentType, $value, $unit);
        }

        $this->getPublishMessages($client, $io);
    }

    private function createTypeIfNotExist(string $type)
    {
        $currentType = $this->entityManager->getRepository(Type::class)->findOneBy(['name' => $type]);
        if (empty($currentType)) {
            $newType = new Type();
            $newType->setName($type);
            $this->entityManager->persist($newType);
            $this->entityManager->flush();
        }
    }

    /**
     * @param object|null $currentType
     * @param string $value
     */
    private function saveData(?object $currentType, string $value, string $unit): void
    {
        $newData = new Data();
        $newData->setType($currentType);
        $newData->setValue($value);
        $newData->setUnit($unit);
        $this->entityManager->persist($newData);
        $this->entityManager->flush();
    }
}
