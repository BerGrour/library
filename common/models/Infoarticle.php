<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "infoarticle".
 *
 * @property int $id
 * @property int|null $inforelease_id
 * @property string|null $name
 * @property int $type
 * @property string|null $source
 * @property string|null $recieptdate
 * @property string|null $additionalinfo
 *
 * @property InfoarticleAuthor[] $infoarticleAuthors
 * @property Inforelease $inforelease
 * @property Author[] $authors
 */
class Infoarticle extends \yii\db\ActiveRecord
{
    CONST TYPE_PAPER = "Газета";
    CONST TYPE_JOURNAL = "Журнал";
    CONST TYPE_LINKSITE = "Интернет страница";
    
    // public $type;
    public $source_name;
    public $source_number;
    public $reciept_year;

    public $infoarticleAuthorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoarticle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required', 'message' => 'Поле не может быть пустым'],
            [['inforelease_id'], 'integer'],
            [['name', 'source', 'additionalinfo'], 'string', 'max' => 250],
            [['inforelease_id'], 'exist', 'skipOnError' => true, 'targetClass' => Inforelease::class, 'targetAttribute' => ['inforelease_id' => 'id']],
            [['infoarticleAuthorIds'], 'each', 'rule' => ['exist', 'targetClass' => Author::class, 'targetAttribute' => 'id']],

            [['source_name', 'source_number', 'recieptdate'], 'required',
                'when' => function ($model) { return $model->type == 1; },
                'whenClient' => "function(attribute, value) {
                    return $('#field-resource').is(':visible') && $('#field-recieptdate').is(':visible');
                }"
            ],
            [['source_name', 'source_number', 'reciept_year'], 'required',
                'when' => function ($model) { return $model->type == 2; },
                'whenClient' => "function(attribute, value) {
                    return $('#field-resource').is(':visible') && $('#field-reciept_year').is(':visible');
                }"
            ],
            [['source', 'recieptdate'], 'required',
                'when' => function ($model) { return $model->type == 3;},
                'whenClient' => "function(attribute, value) {
                    return $('#field-source').is(':visible') && $('#field-recieptdate').is(':visible');
                }"
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inforelease_id' => 'ID информационного выпуска',
            'name' => 'Название',
            'type' => 'Вид источника',
            'source' => 'Ресурс',
            'recieptdate' => 'Дата издания источника',
            'additionalinfo' => 'Дополнительная информация',
            'infoarticleAuthorIds' => 'Авторы',
            
            'reciept_year' => 'Год издания источника',
            'source_name' => 'Наименование ресурса',
            'source_number' => 'Номер ресурса'
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        foreach ($this->infoarticleAuthors as $author_rel) {
            $this->infoarticleAuthorIds[] = $author_rel->author->id;
        }
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this
            ->hasMany(Author::class, ['id' => 'author_id'])
            ->via('infoarticleAuthors');
    }

    /**
     * Gets query for [[InfoarticleAllAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfoarticleAllAuthors()
    {
        return $this->hasMany(InfoarticleAuthor::class, ['infoarticle_id' => 'id']);
    }

    /**
     * Gets query for [[InfoarticleAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfoarticleAuthors()
    {
        return $this->hasMany(InfoarticleAuthor::class, ['infoarticle_id' => 'id'])->where(['type' => 0]);
    }

    /**
     * Gets query for [[Inforelease]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInforelease()
    {
        return $this->hasOne(Inforelease::class, ['id' => 'inforelease_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['infoarticle/view', 'id' => $this->id]);
    }

    /**
     * Возвращает кликабельного автора инфо выпуска
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param string $target открытие ссылки в новой вкладке
     * @param bool $reverse порядок ФИО или ИОФ
     * @return string
     */
    public function showAuthor($link = false, $target = "_self", $reverse = false)
    {
        $separator = null;

        return $separator . implode(', ', array_map(function($author_rel) use ($link, $target, $reverse) {
            $nameParts = array_filter([$author_rel->author->name]);
            if (preg_match('/^\pL+$/u', $author_rel->author->middlename)) {
                $nameParts = array_filter([
                    mb_substr($author_rel->author->name, 0, 1),
                    mb_substr($author_rel->author->middlename, 0, 1)
                ]);
            }
            $withDots = implode('. ', $nameParts) . '.';
            if ($reverse) {
                $nameParts = array_filter([$withDots, $author_rel->author->surname]);
                $content = implode(' ', $nameParts);
            } else {
                $nameParts = array_filter([$author_rel->author->surname, $withDots]);
                $content = implode(' ', $nameParts);
            }

            if ($link) {
                return Html::a(
                    $content,
                    $author_rel->author->getUrl(),
                    [
                        'class' => 'text-link',
                        'data-pjax' => 0,
                        'target' => $target,
                        'title' => $content
                    ]
                );
            } else return $content;
        }, $this->infoarticleAuthors));
    }
    
    /**
     * Массив с авторами
     * @return array
     */
    public function getArrayAuthors()
    {
        $authors_rel = $this->infoarticleAuthors;

        $authorSurnames = [];
        foreach ($authors_rel as $author_rel) {
            $authorSurnames[] = $author_rel->author->showFIO();
        }

        return $authorSurnames;
    }

    /**
     * Возвращает название рубрики
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function showRubric($linked = false) {
        $rubric_name = null;
        $rubric = $this->inforelease->rubric;
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
     * Возвращает заголовок статьи
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file прикрепить к названию иконку с файлом
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self")
    {
        $content = $this->name;
        $file = null;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($with_file && $this->inforelease->file) {
            $file = Html::a(
                '<img src="/Files/Images/doc.png" class="image-file-link">',
                $this->inforelease->file->getLinkOnFile(),
                [
                    'class' => 'custom-link-file',
                    'target' => "_blank",
                    'data-pjax' => 0,
                    'title' => 'Скачать'
                ]
            );
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->additionalinfo
                ]
            ) . $file;
        }
        return $content;
    }

    /**
     * Возвращает кликабельные ссылки на серию и на инфо выпуск
     * 
     * @param bool $linked кликабельные серия и номер на их детальные страницы
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self") {
        $seria = "<strong>{$this->inforelease->seria->name}</strong>";
        $inforelease = "№ {$this->inforelease->number} за {$this->inforelease->publishyear}";

        if ($linked) {
            $seria = Html::a(
                $seria,
                $this->inforelease->seria->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->inforelease->seria->name
                ]
            );
            $inforelease = Html::a(
                $inforelease,
                $this->inforelease->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $inforelease
                ]
            );
        }
        return $seria . " " . $inforelease;
    }

    /**
     * Возвращает краткую инфо для детальной страницы с автором
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($strong = false, $linked = false)
    {
        $content = $this->showTitle($strong) . " {$this->inforelease->seria->name} 
            № {$this->inforelease->number} за {$this->inforelease->publishyear}";
        if($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $this->additionalinfo
                ]
            );
        }
        return $content;
    }

    /**
     * Получение краткой информации о издании для отображения в корзине
     * @param int $abbreviation ограничение основного заголовка
     * @param int $abbreviation_second ограничение доп заголовка
     * @return string
     */
    public function getBriefInfo($abbreviation, $abbreviation_second)
    {
        $title = StringHelper::truncate($this->name, $abbreviation);
        $seria = StringHelper::truncate($this->inforelease->seria->name, $abbreviation_second);

        return  $title
        . "<strong> {$seria} - {$this->inforelease->publishyear}, №{$this->inforelease->number}</strong> "
        . "(<span style='color:hotpink;'>Инфовыпуск</span>)";
    }

    /**
     * Метод для разделения атрибута с источником инфостатьи на две части
     * 1 - наименование источника
     * 2 - номер
     * @return array
     */
    public function getDevideSource()
    {
        $parts = preg_split('/№/', $this->source);
        return array(trim($parts[0]), trim($parts[1]));
    }

    /**
     * Метод для получения года из даты в формате строки
     * @return string
     */
    public function getYearFromDate()
    {
        return substr($this->recieptdate, 0, 4);
    }
}
