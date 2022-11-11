<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Action;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $actions = [
            [
                'actionName' => 'Scan',
                'commandToRun' => 'clamscan',
                'enabled' => true,
                'description' => 'Run virus scan, maximum file size 100 MB',
                'provider' => 'commandToRun',
                'type' => 'commandToRun',
                'hidden' => false,
            ],
        ];

        foreach ($actions as $item) {
            $action = new Action();
            $action->setActionName($item['actionName']);
            $action->setCommandToRun($item['commandToRun']);
            $action->setEnabled($item['enabled']);
            $action->setDescription($item['description']);
            $action->setProvider($item['provider']);
            $action->setType($item['provider']);
            $action->setHidden($item['hidden']);

            $manager->persist($action);
            $manager->flush($action);
        }
    }
}
