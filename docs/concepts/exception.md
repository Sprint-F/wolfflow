
Бандл WolffloW включает в себя три базовых типа (класса) **исключений**:

- [NoNeededAttributeException](#NoNeededAttributeException)
- [CanNotException](#CanNotException)
- [FailException](#FailException)

## <a id="NoNeededAttributeException"></a>NoNeededAttributeException
Класс <code><b>SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException</b></code>.

Исключение, говорящее о том, что для некоего класса не указан обязательный атрибут. 
К примеру: вы создали действие, реализовали в нем интерфейс `ActionInterface`, но забыли указать атрибут `#[AsAction]`.

## <a id="CanNotException"></a>CanNotException
Класс <code><b>SprintF\Bundle\Wolfflow\Exception\CanNotException</b></code>.

Исключение, указывающее на невозможность произвести действие до начала самого действия.
В реальном приложении рекомендуется построить собственную иерархию исключений, наследуясь от `CanNotException`

## <a id="FailException"></a>FailException

Класс <code><b>SprintF\Bundle\Wolfflow\Exception\FailException</b></code>.

Исключение, указывающее на ошибку, возникшую в ходе выполнения действия.