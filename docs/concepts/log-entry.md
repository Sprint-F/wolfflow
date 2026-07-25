
<b>Запись в логе действий</b> - это информация о выполнении того или иного [действия](./action.md) [бизнес-процесса](./workflow.md) 
над какой-то [сущностью](./entity.md), с неким [контекстом](./context.md), некоторым [актором](./actor.md). 

В записи лога действий фиксируются:

- [Действие](./action.md), которое производилось
- [Сущность](./entity.md), над которой производилось действие
- [Контекст](./context.md) действия
- [Актор](./actor.md), производивший действие
- Дата и время начала выполнения действия
- Дата и время окончания выполнения действия
- [Результат](./action.md#actionresult) выполнения действия
- Подробности (комментарий) о результате выполнения

# Классы и интерфейсы

## LogEntryInterface

Общий интерфейс <code><b>SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface</b></code> для всех классов, 
представляющих собой записи в логе действий. Задаёт следующие обязательные методы:

- Методы, задающие и возвращающие выполняемое [действие](./action.md):
  - <code><b>getAction()</b>: ActionInterface</code>
  - <code><b>getActionClass()</b>: string</code>
  - <code><b>getActionName()</b>: string</code>
  - <code><b>setAction(</b>ActionInterface $action<b>)</b></code>


- Методы, задающие и возвращающие [сущность](./entity.md), над которой производится действие:
  - <code><b>getEntity()</b>: EntityInterface</code>
  - <code><b>getEntityClass()</b>: string</code>
  - <code><b>getEntityId()</b>: int|string|\Stringable|null</code>
  - <code><b>setEntity(</b>EntityInterface $entity<b>)</b></code>


- Методы, задающие и возвращающие [контекст](./context.md) выполняемого действия:
  - <code><b>getContext()</b>: ContextInterface</code>
  - <code><b>setContext(</b>ContextInterface $context<b>)</b></code>


- Методы, задающие и возвращающие [актора](./actor.md), производящего действие:
  - <code><b>getActor()</b>: ?ActorInterface</code>
  - <code><b>setActor(</b>?ActorInterface $actor<b>)</b></code>


- Методы, задающие дату-время начала и окончания выполнения действия:
  - <code><b>getStartedAt()</b>: ?\DateTimeInterface</code>
  - <code><b>setStartedAt(</b>?\DateTimeInterface $startedAt<b>)</b></code>
  - <code><b>getFinishedAt()</b>: ?\DateTimeInterface</code>
  - <code><b>setFinishedAt(</b>?\DateTimeInterface $finishedAt<b>)</b></code>


- Методы, задающие и возвращающие [результат действия](./action.md#actionresult):
  - <code><b>getResult()</b>: ActionResult</code>
  - <code><b>setResult(</b>ActionResult $result<b>)</b></code>


- Методы, задающие и возвращающие подробности (комментарий) о результате действия:
  - <code><b>getReason()</b>: ?string</code>
  - <code><b>setReason(</b>?string $reason<b>)</b></code>


## LogEntryDoctrineTrait

Трейт <code><b>SprintF\Bundle\Wolfflow\LogEntry\LogEntryDoctrineTrait</b></code> содержит базовую реализацию интерфейса 
`LogEntryInterface` для случая, когда лог действий будет храниться в базе данных и каждая запись в логе 
будет представлять собой сущность Doctrine.

Для применения этого трейта нужно создать класс сущности записи в логе, указать таблицу для хранения лога действий 
и добавить свойства (связи) `$entity` и `$actor`.

Типовая схема применения этого трейта, в случае если [сущностью](./entity.md), над которой будут выполняться действия, 
будет, к примеру, заказ в интернет-магазине (`Order`), а [актором](./actor.md) - пользователь приложения (`User`), 
выглядит так:
```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryDoctrineTrait;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;

#[ORM\Entity]
#[ORM\Table('orders_action_log')]
#[ORM\Index(name: 'orders_action_log_started_at_idx', fields: ['startedAt'])]
#[ORM\Index(name: 'orders_action_log_entity_idx', columns: ['__entity_id'])]
#[ORM\Index(name: 'orders_action_log_user_idx', columns: ['__user_id'])]
class OrderLogEntry implements LogEntryInterface
{
    use LogEntryDoctrineTrait;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'logEntries')]
    #[ORM\JoinColumn(name: '__entity_id', referencedColumnName: '__id', nullable: true)]
    private Order $entity;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: '__user_id', referencedColumnName: '__id', nullable: true)]
    protected ?User $actor = null;
}
```
В данном примере опущен вопрос с первичным ключом записи в логе. Его нужно решить самостоятельно, сообразно политике первичных ключей в вашем приложении.