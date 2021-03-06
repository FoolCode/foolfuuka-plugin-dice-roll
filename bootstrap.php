<?php

use Foolz\FoolFrame\Model\Autoloader;
use Foolz\FoolFrame\Model\Context;
use Foolz\Plugin\Event;

class HHVM_Dice
{
    public function run()
    {
        Event::forge('Foolz\Plugin\Plugin::execute#foolz/foolfuuka-plugin-dice-roll')
            ->setCall(function($result) {

                /* @var Context $context */
                $context = $result->getParam('context');
                /** @var Autoloader $autoloader */
                $autoloader = $context->getService('autoloader');

                $autoloader->addClass('Foolz\FoolFuuka\Plugins\DiceRoll\Model\Dice', __DIR__.'/classes/model/dice.php');

                Event::forge('Foolz\FoolFuuka\Model\CommentInsert::insert#obj.afterInputCheck')
                    ->setCall('Foolz\FoolFuuka\Plugins\DiceRoll\Model\Dice::roll')
                    ->setPriority(4);

                Event::forge('Foolz\FoolFuuka\Model\RadixCollection::structure#var.structure')
                    ->setCall(function($result) {
                        $structure = $result->getParam('structure');
                        $structure['plugin_dice_roll_enable'] = [
                            'database' => true,
                            'boards_preferences' => true,
                            'type' => 'checkbox',
                            'help' => _i('Enable dice roll?')
                        ];
                        $result->setParam('structure', $structure)->set($structure);
                    })->setPriority(4);
            });
    }
}

(new HHVM_Dice())->run();
