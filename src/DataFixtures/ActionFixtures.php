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
            ],
        ];

        foreach ($actions as $item) {
            $action = new Action();
            $action->setActionName($item['actionName']);
            $action->setCommandToRun($item['commandToRun']);
            $action->setEnabled($item['enabled']);

            $manager->persist($action);
            $manager->flush($action);
        }
    }
}
