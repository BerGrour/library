<?php

namespace common\models;

use common\extensions\traits\DispositionTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "statrelease".
 *
 * @property int $id
 * @property string $name
 * @property string|null $additionalname
 * @property string|null $response
 * @property string|null $publishplace
 * @property int|null $publishyear
 * @property int|null $pages
 * @property string|null $recieptdate
 * @property float|null $cost
 * @property string $code
 * @property string|null $authorsign
 * @property int|null $numbersk
 * @property string $key_words
 * @property int|null $rubric_id
 * @property int $disposition
 * @property int $withraw
 *
 * @property StatreleaseRubric $rubric
 */
class Statrelease extends \yii\db\ActiveRecord
{
    use DispositionTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statrelease';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'withraw', 'rubric_id'], 'required', 'message' => 'Поле не может быть пустым'],
            [['publishyear', 'pages', 'numbersk', 'withraw', 'rubric_id', 'disposition'], 'integer', 'message' => 'Должно быть числом'],
            [['recieptdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Неверный формат даты'],
            [['cost'], 'number', 'message' => 'Неверный формат (дробная часть через точку)'],
            [['name', 'additionalname', 'response', 'publishplace'], 'string', 'max' => 250],
            [['key_words'], 'string'],
            [['code'], 'string', 'max' => 8],
            [['authorsign'], 'string', 'max' => 255],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatreleaseRubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
            [['withraw'], 'frontend\validators\WithrawValidator'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Заглавие',
            'additionalname' => 'Дополнительное заглавие',
            'response' => 'Сведения об отвественности',
            'publishplace' => 'Место издания',
            'publishyear' => 'Год издания',
            'pages' => 'Количество страниц',
            'recieptdate' => 'Дата поступления',
            'cost' => 'Цена',
            'code' => 'Регистр. номер',
            'authorsign' => 'Авторский знак',
            'numbersk' => 'Номер СК',
            'key_words' => 'Ключевые слова',
            'rubric_id' => 'Рубрика',
            'disposition' => 'Расположение',
            'withraw' => 'Списано',
        ];
    }

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(StatreleaseRubric::class, ['id' => 'rubric_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['statrelease/view', 'id' => $this->id]);
    }

    /**
     * Возвращает название рубрики
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function showRubric($linked = false)
    {
        $rubric_name = null;
        $rubric = $this->rubric;
        if ($rubric) {
            $rubric_name = $rubric->getInfoTitle();
            if ($linked) {
                return Html::a(
                    $rubric_name,
                    $rubric->getUrl(),
                    [
                        'class' => 'text-link',
                        'data-pjax' => 0,
                        'title' => $rubric->title
                    ]
                );
            }
        }
        return $rubric_name;
    }

    /**
     * Возвращает заголовок стат сборника cостоит из атрибутов name и additionalname
     * 
     * @param bool $strong нет функционала
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self")
    {
        $content = $this->name;
        if ($this->additionalname) {
            $content .= ': <i class="another-info">' . $this->additionalname . '</i>';
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->name
                ]
            );
        }
        return $content;
    }

    /**
     * Возвращает информацию о стат сборнике для GridView
     * Cостоит из атрибутов: publishplace, publishyear, pages + (для библиотекарей) code и authorsign
     * 
     * @param bool $linked нет функционала
     * @param bool $with_file нет функционала
     * @param string $target нет функционала
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self") {
        $result = '';
        if ($this->publishplace) {
            $result .= "{$this->publishplace}";
        }
        if ($this->publishyear) {
            $result .= ($result ? ', ' : '') . "{$this->publishyear}";
        }
        if ($this->pages) {
            $result .= ($result ? ' - ' : '') . "{$this->pages}с.";
        }
        if ($this->disposition) {
            $result .= ' ' . $this->nameDisposition();
        }
        // Доп инфо для библиотекарей
        if (Yii::$app->user->can('statrelease/update')) {
            if ($this->code) {
                $result .= ($result ? ', ' : '') . '<strong>' . "{$this->code}" . '</strong>';
            }
            if ($this->authorsign) {
                $result .= ($result ? ', ' : '') . '<span style="color: green;">' . "{$this->authorsign}" . '</span>';
            }
        }
        if ($this->withraw) {
            $result .= '<span class="status-edition"> [Списано]</span>';
        }
        $result .= Logbook::checkStatreleaseHands($this->id);
        return $result;
    }

    /**
     * Краткая информация о стат сборнике для вывода на странице рубрики
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($strong = false, $linked = false)
    {
        $content = $this->name;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($this->additionalname) {
            $content .= ": <i class=\"another-info\">{$this->additionalname}</i>";
        }
        if ($this->publishplace) {
            $content .= " - {$this->publishplace}";
        }
        if ($this->publishyear) {
            $content .= ", {$this->publishyear}";
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $this->name
                ]
            );
        }
        return $content;
    }

    /**
     * Выводит информацию о книге на главную страницу
     * @return string
     */
    public function getLibraryLink()
    {
        $statreleaseName = $this->name;
        $statreleaseAdditionalName = $this->additionalname;
        $statreleasePublishPlace = $this->publishplace;
        $statreleasePages = $this->pages;
        if (isset($this->rubric)) {
            $statreleaseRubric = $this->rubric->title;
        }
        $link = $statreleaseName.' : '.$statreleaseAdditionalName.' - '.$statreleasePublishPlace.'.- '. $statreleasePages.'c.';
        if (isset($statreleaseRubric)) $link .= ' ('.$statreleaseRubric.')';
        return $link;
    }

    /**
     * Получение краткой информации о издании для отображения в корзине
     * @param int $abbreviation ограничение основного заголовка
     * @param int $abbreviation_second ограничение доп заголовка
     * @return string
     */
    public function getBriefInfo($abbreviation, $abbreviation_second)
    {
        return StringHelper::truncate($this->name, $abbreviation) 
        . "<strong> {$this->code}, {$this->rubric->title}</strong> "
        . "(<span style='color:purple;'>Стат сборник</span>)";
    }
    
    /**
     * Метод для проверки статуса выдачи издания
     * 
     * @return bool на руках или нет
     */
    public function isBorrowed()
    {
        $logbook = Logbook::find()->where([
            'statrelease_id' => $this->id,
            'return_date' => null
        ]);
        if ($logbook->exists()) return true;
        return false;
    }
}
