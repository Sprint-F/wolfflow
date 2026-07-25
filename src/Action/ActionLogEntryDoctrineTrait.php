<?php

namespace SprintF\Bundle\Wolfflow\Action;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\Persistence\ManagerRegistry;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Базовая реализация методов Action::insertLogEntry() и Action::updateLogEntry() для Doctrine.
 */
trait ActionLogEntryDoctrineTrait
{
    protected EntityManagerInterface $em;

    #[Required]
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected ManagerRegistry $doctrine;

    #[Required]
    public function setManagerRegistry(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function insertLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        $metadata = $this->em->getClassMetadata(get_class($logEntry));
        $connection = $this->em->getConnection();

        // В этот момент наша сущность получает ID, если у нее pre-insert генерация ID
        $this->em->persist($logEntry);
        // И мы убираем ее из UoW, чтобы не сработал лишний INSERT
        $this->em->detach($logEntry);

        $qb = $connection->createQueryBuilder();
        $qb->insert($metadata->getTableName());

        // Если при persist сработал генератор ID, мы получим значение ID и подготовим его к записи в БД
        if (!empty($metadata->getIdentifierValues($logEntry))) {
            $qb->setValue(
                $metadata->getSingleIdentifierColumnName(),
                $qb->createNamedParameter(
                    $connection->convertToDatabaseValue(
                        $metadata->getIdentifierValues($logEntry)[$metadata->getSingleIdentifierFieldName()],
                        $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName())
                    ),
                )
            );
        }

        // Подготовка остальных данных для INSERT

        $column = $metadata->getColumnName('actionClass');
        $type = $metadata->getTypeOfField('actionClass');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getActionClass(),
                $type
            )
        ));

        $column = $metadata->getColumnName('actionName');
        $type = $metadata->getTypeOfField('actionName');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getActionName(),
                $type
            )
        ));

        $column = $metadata->getColumnName('entityClass');
        $type = $metadata->getTypeOfField('entityClass');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getEntityClass(),
                $type
            )
        ));

        $column = $metadata->getSingleAssociationJoinColumnName('entity');
        if (!$logEntry->getEntity()->isNew()) {
            $entityMetadata = $this->em->getClassMetadata(get_class($logEntry->getEntity()));
            $type = $entityMetadata->getTypeOfField($entityMetadata->getSingleIdentifierFieldName());
            $qb->setValue($column, $qb->createNamedParameter(
                $connection->convertToDatabaseValue(
                    $logEntry->getEntityId(),
                    $type
                )
            ));
        }

        $column = $metadata->getColumnName('contextInDb');
        $type = $metadata->getTypeOfField('contextInDb');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getContext(),
                $type
            )
        ));

        $column = $metadata->getSingleAssociationJoinColumnName('actor');
        if (null !== $logEntry->getActor()) {
            $actorMetadata = $this->em->getClassMetadata(get_class($logEntry->getActor()));
            $type = $actorMetadata->getTypeOfField($actorMetadata->getSingleIdentifierFieldName());
            $qb->setValue($column, $qb->createNamedParameter(
                $connection->convertToDatabaseValue(
                    $logEntry->getActor()->getActorId(),
                    $type
                )
            ));
        } else {
            $qb->setValue($column, $qb->createNamedParameter(null));
        }

        $column = $metadata->getColumnName('startedAt');
        $type = $metadata->getTypeOfField('startedAt');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getStartedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('finishedAt');
        $type = $metadata->getTypeOfField('finishedAt');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getFinishedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('result');
        $type = $metadata->getTypeOfField('result');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getResult()->value,
                $type
            )
        ));

        $column = $metadata->getColumnName('reason');
        $type = $metadata->getTypeOfField('reason');
        $qb->setValue($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getReason(),
                $type
            )
        ));

        // Собственно выполнение INSERT
        $qb->executeStatement();

        // В том случае, когда post-insert генерация ID, генератор придется вызвать вручную
        $idGenerator = $metadata->idGenerator;
        if ($idGenerator->isPostInsertGenerator()) {
            $generatedIdValue = $idGenerator->generateId($this->em, $logEntry);
            if (!$idGenerator instanceof AssignedGenerator) {
                $convertedIdValue = $connection->convertToPHPValue(
                    $generatedIdValue,
                    $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName()),
                );
                $idValue = [$metadata->getSingleIdentifierFieldName() => $convertedIdValue];
                $metadata->setIdentifierValues($logEntry, $idValue);
            }
        }

        // А это мы делаем, чтобы избежать повторный insert нашей сущности средствами Entity Manager
        // или повторный persist с генерацией ID
        $refreshed = $this->em->find(get_class($logEntry), $metadata->getIdentifierValues($logEntry)[$metadata->getSingleIdentifierFieldName()]);
        $refreshed
            ->setAction($logEntry->getAction())
            ->setEntity($logEntry->getEntity())
            ->setContext($logEntry->getContext())
            ->setActor($logEntry->getActor())
        ;

        return $refreshed;
    }

    protected function updateLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        $metadata = $this->em->getClassMetadata(get_class($logEntry));

        $connection = $this->em->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->update($metadata->getTableName());

        // Подготовка даннных для UPDATE

        $qb->where(
            $qb->expr()->eq(
                $metadata->getSingleIdentifierColumnName(),
                $qb->createNamedParameter(
                    $connection->convertToDatabaseValue(
                        $metadata->getFieldValue($logEntry, $metadata->getSingleIdentifierFieldName()),
                        $metadata->getTypeOfField($metadata->getSingleIdentifierFieldName())
                    )
                )
            )
        );

        $column = $metadata->getSingleAssociationJoinColumnName('entity');
        if (!$logEntry->getEntity()->isNew()) {
            $entityMetadata = $this->em->getClassMetadata(get_class($logEntry->getEntity()));

            $entityTableName = $entityMetadata->getTableName();
            $entityIdFieldName = $metadata->getSingleAssociationReferencedJoinColumnName('entity');
            $type = $entityMetadata->getTypeOfField($entityMetadata->getSingleIdentifierFieldName());

            $checkIfEntityExists = $connection->createQueryBuilder();
            $checkIfEntityExists
                ->select('count(*)')
                ->from($entityTableName)
                ->where($entityIdFieldName.'=:id')
                ->createNamedParameter(
                    $connection->convertToDatabaseValue(
                        $logEntry->getEntityId(),
                        $type,
                    ),
                    placeHolder: ':id'
                )
            ;
            $result = $checkIfEntityExists->executeQuery()->fetchOne();

            if (1 === $result) {
                $type = $entityMetadata->getTypeOfField($entityMetadata->getSingleIdentifierFieldName());
                $qb->set($column, $qb->createNamedParameter(
                    $connection->convertToDatabaseValue(
                        $logEntry->getEntityId(),
                        $type
                    )
                ));
            }
        }

        $column = $metadata->getColumnName('finishedAt');
        $type = $metadata->getTypeOfField('finishedAt');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getFinishedAt(),
                $type
            )
        ));

        $column = $metadata->getColumnName('result');
        $type = $metadata->getTypeOfField('result');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getResult()->value,
                $type
            )
        ));

        $column = $metadata->getColumnName('reason');
        $type = $metadata->getTypeOfField('reason');
        $qb->set($column, $qb->createNamedParameter(
            $connection->convertToDatabaseValue(
                $logEntry->getReason(),
                $type
            )
        ));

        // Собственно выполнение UPDATE
        $qb->executeStatement();

        // Переоткрытие EM, если он закрылся от ошибки Doctrine
        if (!$this->em->isOpen()) {
            $this->em = $this->doctrine->resetManager();
            $this->em->persist($logEntry);
        }

        $this->em->refresh($logEntry);

        return $logEntry;
    }
}
