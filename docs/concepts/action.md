**Действие бизнес-процесса** - это атомарное действие, которое производит (_кто?_) [Актор](./actor.md) (_над чем?_) 
над некоей [Сущностью](./entity.md) с использованием (_каких данных?_) [Контекста действия](./context.md).

Действие обязательно отражается в [Логе действий](./log-entry.md) - начало действия, его прогресс, завершение, 
статус завершения и причина неуспеха, если статус неуспешный.

Совокупность неких действий над одним и тем же классом [сущностей](./entity.md) образует [Бизнес-процесс](./workflow.md).

Каждое действие - это класс PHP и сервис Symfony.

# Классы и интерфейсы

## #[AsAction]
Атрибут <code><b>SprintF\Bundle\Wolfflow\Attribute\AsAction</b></code> применяется к классам действий и обозначает 
тот факт, что класс, отмеченный этим атрибутом, является классом действия [бизнес-процесса](./workflow.md).

- <code>string <b>$workflow</b></code> Символьное имя <бизнес-процесса>, которому принадлежит действие
- <code>?string <b>$name</b></code> Собственное символьное имя действия

Всем сервисам, помеченным данным атрибутом и реализующим интерфейс `ActionInterface`, компилятор Symfony добавляет тег 
<code><b>workflow.action</b></code>с параметрами:

- <code><b>workflow</b></code> => символьное имя процесса, в который входит данное действие
- <code><b>name</b></code> => собственное символьное имя действия

# ActionInterface

Общий интерфейс <code><b>SprintF\Bundle\Wolfflow\Action\ActionInterface</b></code> для всех действий 
всех [бизнес-процессов](./workflow.md). Задает следующие обязательные методы:

- Методы, возвращающие символьное имя [бизнес-процесса](./workflow.md), которому принадлежит данное действие, 
и сам объект этого бизнес-процесса:
  - <code><b>getWorkflowName()</b>: string</code>
  - <code><b>getWorkflow()</b>: WorkflowInterface</code>

  
- Методы, задающие и возвращающие [сущность](./entity.md), над которой будет производиться действие:
  - <code><b>setEntity(</b>WorkflowEntityInterface $entity<b>)</b></code>
  - <code><b>getEntity()</b>: WorkflowEntityInterface</code>


- Методы, задающие и возвращающие [контекст](./context.md) данного действия:
  - <code><b>setContext(</b>ContextInterface $context<b>)</b></code>
  - <code><b>getContext()</b>: ContextInterface</code>


- Методы, задающие и возвращающие [актора](./actor.md), производящего данное действие:
  - <code><b>setActor(</b>?ActorInterface $actor<b>)</b></code>
  - <code><b>getActor()</b>: ?ActorInterface</code>


- Метод, отвечающий на вопрос: Может ли данное действие быть выполнено над данной [сущностью](./entity.md) 
данным [актором](./actor.md) с заданным [контекстом](./context.md) в настоящее время? Если действие не может быть выполнено, 
метод обязан выбросить исключение, унаследованное от [CanNotException](./exception.md):
  - <code><b>can()</b>: bool</code>


- Основной метод действия, метод непосредственно выполнения действия. 
Такое имя метода выбрано специально, чтобы не выбирать никакое определенное имя:
  - <code><b>__invoke()</b>: ActionResult</code>


Таким образом типовой сценарий вызова действия может выглядеть примерно так:
```php
($action
  ->setEntity($entity)
  ->setContext(new SomeContext(foo: 'bar', baz: 42))
)();
```

## ActionAbstract

Типовой абстрактный базовый класс <code><b>SprintF\Bundle\Wolfflow\Action\ActionAbstract</b></code> для действий, 
включенный в состав бандла для удобства применения в приложениях. Класс реализует:

- Инъекцию сервиса `ActorProviderInterface`
- Методы `getWorkflowName()` и `getWorkflow()`
- Метод `setEntity()` с проверкой на допустимый тип сущности
- Метод `start()`, реализующий запись в [логе действий](./log-entry.md) информации о начале выполнения действия
- Метод `close()`, реализующий запись в [логе действий](./log-entry.md) информации о завершении выполнения действия
- Метод `__invoke()` с полной логикой работы действия: старт, проверка возможности выполнения, выполнение, логирование результата выполнения<
- Абстрактные методы `insertLogEntry()` и `updateLogEntry()` в которых должна быть реализована логика, соответственно, 
создания записи в [логе действий](./log-entry.md) и ее обновления

Свои классы действий в приложении можно наследовать от данного абстрактного класса, добавляя к ним лишь 
реализацию методов `can()` и `do()`:

```php
use SprintF\Bundle\Wolfflow\Action\ActionAbstract;
use SprintF\Bundle\Wolfflow\Action\ActionLogEntryDoctrineTrait;

class SomeAction extends ActionAbstact
{
    use ActionLogEntryDoctrineTrait;

    // Устанавливаем ограничение: действие может выполнять только администратор
    public function can(): bool
    {
        return (bool)$this->getActor()?->hasRole('ROLE_ADMIN');
    }

    public function do()
    {
        // Выполняем полезное действие
    }
}
```
С классом `ActionAbstract` тесно связан трейт `ActionLogEntryDoctrineTrait`, который рассмотрен ниже.

## ActionLogEntryDoctrineTrait

Трейт <code><b>SprintF\Bundle\Wolfflow\Action\ActionLogEntryDoctrineTrait</b></code> содержит дефолтную реализацию 
методов `ActionAbstract::insertLogEntry()` и `ActionAbstract::updateLogEntry()` для того случая, когда вы храните 
[лог действий](./log-entry.md) в базе данных и используете Doctrine.

Пугающая сложность реализации этих методов связана с необходимостью обхода стандартного механизма Unit of Work в Doctrine 
и необходимостью обеспечить вставку и обновление записей в БД даже в случае так называемого "закрытого" Entity Manager.

## ActionResult

Перечисление <code><b>SprintF\Bundle\Wolfflow\Action\ActionResult</b></code>, определяющее тип 
результата выполнения действия бизнес-процесса:
- <code><b>CANNOT</b></code> => Действие не может быть выполнено и по этой причине не начиналось
- <code><b>PROGRESS</b></code> => Действие в процессе исполнения
- <code><b>SUCCESS</b></code> => Действие успешно завершено
- <code><b>FAIL</b></code> => Действие завершилось неуспехом</li>