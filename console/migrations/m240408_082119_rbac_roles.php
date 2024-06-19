<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240408_082119_rbac_roles
 */
class m240408_082119_rbac_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Создание ролей
        $admin = $auth->getRole('admin');
        if (!$admin) {
            $admin = $auth->createRole('admin');
            $auth->add($admin);
        }
        $library = $auth->createRole('librarian');
        $auth->add($library);

        $reader = $auth->createRole('reader');
        $auth->add($reader);

        // Создание разрешений

        // Управление пользователями
        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Управление пользователями';
        $auth->add($manageUsers);

        // Журнал
        $journalCreate = $auth->createPermission('journal/create');
        $journalCreate->description = 'Создание журнала';
        $auth->add($journalCreate);

        $journalUpdate = $auth->createPermission('journal/update');
        $journalUpdate->description = 'Редактирование журнала';
        $auth->add($journalUpdate);

        $journalDelete = $auth->createPermission('journal/delete');
        $journalDelete->description = 'Удаление журнала';
        $auth->add($journalDelete);

        // Выпуск
        $issueCreate = $auth->createPermission('issue/create');
        $issueCreate->description = 'Создание выпуска для журнала';
        $auth->add($issueCreate);

        $issueUpdate = $auth->createPermission('issue/update');
        $issueUpdate->description = 'Редактирование выпуска для журнала';
        $auth->add($issueUpdate);

        $issueDelete = $auth->createPermission('issue/delete');
        $issueDelete->description = 'Удаление выпуска для журнала';
        $auth->add($issueDelete);

        // Статья
        $articleCreate = $auth->createPermission('article/create');
        $articleCreate->description = 'Создание статьи для выпуска из журнала';
        $auth->add($articleCreate);

        $articleUpdate = $auth->createPermission('article/update');
        $articleUpdate->description = 'Редактирование статьи для выпуска из журнала';
        $auth->add($articleUpdate);

        $articleDelete = $auth->createPermission('article/delete');
        $articleDelete->description = 'Удаление статьи для выпуска из журнала';
        $auth->add($articleDelete);

        // Автор
        $authorCreate = $auth->createPermission('author/create');
        $authorCreate->description = 'Создание информации об авторе';
        $auth->add($authorCreate);

        $authorUpdate = $auth->createPermission('author/update');
        $authorUpdate->description = 'Редактирование информации об авторе';
        $auth->add($authorUpdate);

        $authorDelete = $auth->createPermission('author/delete');
        $authorDelete->description = 'Удаление информации об авторе';
        $auth->add($authorDelete);

        // Книга
        $bookCreate = $auth->createPermission('book/create');
        $bookCreate->description = 'Создание книги';
        $auth->add($bookCreate);

        $bookUpdate = $auth->createPermission('book/update');
        $bookUpdate->description = 'Редактирование книги';
        $auth->add($bookUpdate);

        $bookDelete = $auth->createPermission('book/delete');
        $bookDelete->description = 'Удаление книги';
        $auth->add($bookDelete);

        // Рубрика
        $rubricCreate = $auth->createPermission('rubric/create');
        $rubricCreate->description = 'Создание рубрики';
        $auth->add($rubricCreate);

        $rubricUpdate = $auth->createPermission('rubric/update');
        $rubricUpdate->description = 'Редактирование рубрики';
        $auth->add($rubricUpdate);

        $rubricDelete = $auth->createPermission('rubric/delete');
        $rubricDelete->description = 'Удаление рубрики';
        $auth->add($rubricDelete);

        // Информационная серия
        $seriaCreate = $auth->createPermission('seria/create');
        $seriaCreate->description = 'Создание информационной серии';
        $auth->add($seriaCreate);

        $seriaUpdate = $auth->createPermission('seria/update');
        $seriaUpdate->description = 'Редактирование информационной серии';
        $auth->add($seriaUpdate);

        $seriaDelete = $auth->createPermission('seria/delete');
        $seriaDelete->description = 'Удаление информационной серии';
        $auth->add($seriaDelete);

        // Информационный выпуск
        $inforeleaseCreate = $auth->createPermission('inforelease/create');
        $inforeleaseCreate->description = 'Создание информационного выпуска';
        $auth->add($inforeleaseCreate);

        $inforeleaseUpdate = $auth->createPermission('inforelease/update');
        $inforeleaseUpdate->description = 'Редактирование информационного выпуска';
        $auth->add($inforeleaseUpdate);

        $inforeleaseDelete = $auth->createPermission('inforelease/delete');
        $inforeleaseDelete->description = 'Удаление информационного выпуска';
        $auth->add($inforeleaseDelete);

        // Информационная статья
        $infoarticleCreate = $auth->createPermission('infoarticle/create');
        $infoarticleCreate->description = 'Создание информационной статьи';
        $auth->add($infoarticleCreate);

        $infoarticleUpdate = $auth->createPermission('infoarticle/update');
        $infoarticleUpdate->description = 'Редактирование информационной статьи';
        $auth->add($infoarticleUpdate);

        $infoarticleDelete = $auth->createPermission('infoarticle/delete');
        $infoarticleDelete->description = 'Удаление информационной статьи';
        $auth->add($infoarticleDelete);

        // Статистический сборник
        $statreleaseCreate = $auth->createPermission('statrelease/create');
        $statreleaseCreate->description = 'Создание статистического сборника';
        $auth->add($statreleaseCreate);

        $statreleaseUpdate = $auth->createPermission('statrelease/update');
        $statreleaseUpdate->description = 'Редактирование статистического сборника';
        $auth->add($statreleaseUpdate);

        $statreleaseDelete = $auth->createPermission('statrelease/delete');
        $statreleaseDelete->description = 'Удаление статистического сборника';
        $auth->add($statreleaseDelete);

        // Рубрика для статистического сборника
        $statreleaserubricCreate = $auth->createPermission('statreleaserubric/create');
        $statreleaserubricCreate->description = 'Создание рубрики для статистического сборника';
        $auth->add($statreleaserubricCreate);

        $statreleaserubricUpdate = $auth->createPermission('statreleaserubric/update');
        $statreleaserubricUpdate->description = 'Редактирование рубрики для статистического сборника';
        $auth->add($statreleaserubricUpdate);

        $statreleaserubricDelete = $auth->createPermission('statreleaserubric/delete');
        $statreleaserubricDelete->description = 'Удаление рубрики для статистического сборника';
        $auth->add($statreleaserubricDelete);

        // Инвентарная книга
        $inventoryBook = $auth->createPermission('inventorybook/access');
        $inventoryBook->description = 'Доступ к инвентарной книге';
        $auth->add($inventoryBook);

        // Корзина пользователя
        $cartCreate = $auth->createPermission('cart/create');
        $cartCreate->description = 'Создание корзины пользователя';
        $auth->add($cartCreate);

        $cartUpdate = $auth->createPermission('cart/update');
        $cartUpdate->description = 'Редактирование корзины пользователя';
        $auth->add($cartUpdate);

        $cartDelete = $auth->createPermission('cart/delete');
        $cartDelete->description = 'Удаление корзины пользователя';
        $auth->add($cartDelete);

        $cartUnlock = $auth->createPermission('cart/unlock');
        $cartUnlock->description = 'Возможность просмотра чужих открытых подборок';
        $auth->add($cartUnlock);

        // Формуляр
        $logbookAccess = $auth->createPermission('logbook/access');
        $logbookAccess->description = 'Доступ к формуляру';
        $auth->add($logbookAccess);

        $logbookGive = $auth->createPermission('logbook/give');
        $logbookGive->description = 'Выдача изданий в формуляре';
        $auth->add($logbookGive);

        $logbookReturn = $auth->createPermission('logbook/return');
        $logbookReturn->description = 'Возврат изданий в формуляре';
        $auth->add($logbookReturn);


        // Наследование

        // Читатель 

        // Для cart только create, т.к. остальные actions привязаны к id текущего 
        // пользователя, чтобы он мог редактировать только свою корзину
        $auth->addChild($reader, $cartCreate);

        // Библиотекарь
        $auth->addChild($library, $journalCreate);
        $auth->addChild($library, $journalUpdate);
        $auth->addChild($library, $journalDelete);

        $auth->addChild($library, $issueCreate);
        $auth->addChild($library, $issueUpdate);
        $auth->addChild($library, $issueDelete);

        $auth->addChild($library, $articleCreate);
        $auth->addChild($library, $articleUpdate);
        $auth->addChild($library, $articleDelete);

        $auth->addChild($library, $authorCreate);
        $auth->addChild($library, $authorUpdate);
        $auth->addChild($library, $authorDelete);

        $auth->addChild($library, $bookCreate);
        $auth->addChild($library, $bookUpdate);
        $auth->addChild($library, $bookDelete);

        $auth->addChild($library, $rubricCreate);
        $auth->addChild($library, $rubricUpdate);
        $auth->addChild($library, $rubricDelete);

        $auth->addChild($library, $seriaCreate);
        $auth->addChild($library, $seriaUpdate);
        $auth->addChild($library, $seriaDelete);

        $auth->addChild($library, $inforeleaseCreate);
        $auth->addChild($library, $inforeleaseUpdate);
        $auth->addChild($library, $inforeleaseDelete);

        $auth->addChild($library, $infoarticleCreate);
        $auth->addChild($library, $infoarticleUpdate);
        $auth->addChild($library, $infoarticleDelete);

        $auth->addChild($library, $statreleaseCreate);
        $auth->addChild($library, $statreleaseUpdate);
        $auth->addChild($library, $statreleaseDelete);

        $auth->addChild($library, $statreleaserubricCreate);
        $auth->addChild($library, $statreleaserubricUpdate);
        $auth->addChild($library, $statreleaserubricDelete);

        $auth->addChild($library, $inventoryBook);

        $auth->addChild($library, $manageUsers);

        $auth->addChild($library, $cartUnlock);

        $auth->addChild($library, $logbookAccess);
        $auth->addChild($library, $logbookGive);
        $auth->addChild($library, $logbookReturn);

        // Библиотекарь - наследует все права читателя
        $auth->addChild($library, $reader);

        // Админ - наследует все права библиотекаря
        $auth->addChild($admin, $library);
        // управление корзинами пользователей (лишь ограничение для post-запросов, функционала не несет)
        $auth->addChild($admin, $cartUpdate);
        $auth->addChild($admin, $cartDelete);

        // Назначение роли admin для пользователя admin
        $user = User::findByUsername('admin');
        $auth->assign($admin, $user->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $this->delete($auth->itemChildTable);
        $this->delete($auth->itemTable);
    }
}
