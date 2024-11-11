<?php

namespace SprintF\Bundle\Wolfflow\Entity;

use Doctrine\ORM\Mapping as ORM;
use SprintF\Bundle\Wolfflow\ActionLog\ActionLogEntryInterface;
use SprintF\Bundle\Wolfflow\ActionLog\ActionLogEntryTrait;

#[ORM\Entity]
#[ORM\Table(name: 'action_logs')]
class ActionLogEntry implements ActionLogEntryInterface
{
    use ActionLogEntryTrait;
}