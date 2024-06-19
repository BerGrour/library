<?php

namespace common\models;

use common\extensions\traits\DispositionTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $name
 * @property string|null $additionalname
 * @property string|null $response
 * @property string|null $additionalresponse
 * @property string|null $bookinfo
 * @property string|null $publishplace
 * @property string|null $publishhouse
 * @property int|null $publishyear
 * @property string $tom
 * @property int|null $pages
 * @property string|null $authorsign
 * @property string $code
 * @property int|null $numbersk
 * @property string|null $recieptdate
 * @property float|null $cost
 * @property string|null $ISBN
 * @property string|null $annotation
 * @property int $withraw
 * @property string $key_words
 * @property int|null $rubric_id
 * @property int|null $file_id
 * @property int $disposition
 *
 * @property BookAuthor[] $bookAuthors
 * @property BookAuthor[] $bookRedactors
 * @property Files $file
 * @property Rubric $rubric
 * @property Author[] $authors
 */
class Book extends \yii\db\ActiveRecord
{
    use DispositionTrait;
    public $uploadedFile;
    public $bookAuthorIds = [];
    public $bookRedactorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'withraw', 'rubric_id'], 'required', 'message' => 'Поле не может быть пустым'],
            [['publishyear', 'pages', 'numbersk', 'withraw', 'rubric_id', 'file_id', 'disposition'], 'integer', 'message' => 'Должно быть числом'],
            [['recieptdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Неверный формат даты'],
            [['cost'], 'number', 'message' => 'Неверный формат (дробная часть через точку)'],
            [['annotation', 'key_words'], 'string'],
            [['name', 'additionalname', 'response', 'bookinfo', 'publishplace', 'publishhouse'], 'string', 'max' => 250],
            [['additionalresponse'], 'string', 'max' => 10],
            [['tom'], 'string', 'max' => 150],
            [['authorsign'], 'string', 'max' => 3],
            [['code'], 'string', 'max' => 8],
            [['ISBN'], 'string', 'max' => 50],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
            [['uploadedFile'], 'file'],
            [['bookAuthorIds'], 'each', 'rule' => ['exist', 'targetClass' => Author::class, 'targetAttribute' => 'id']],
            [['bookRedactorIds'], 'each', 'rule' => ['exist', 'targetClass' => Author::class, 'targetAttribute' => 'id']],
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
            'response' => 'Сведения об ответственности',
            'additionalresponse' => 'Дополнительные сведения об отвественности',
            'bookinfo' => 'Сведения об издании',
            'publishplace' => 'Место издания',
            'publishhouse' => 'Издательство',
            'publishyear' => 'Год издания',
            'tom' => 'Том',
            'pages' => 'Количество страниц',
            'authorsign' => 'Авторский знак',
            'code' => 'Инвентарный номер',
            'numbersk' => 'Номер С. К.',
            'recieptdate' => 'Дата поступления',
            'cost' => 'Стоимость',
            'ISBN' => 'ISBN',
            'annotation' => 'Аннотация',
            'withraw' => 'Списано',
            'key_words' => 'Ключевые слова',
            'rubric_id' => 'Рубрика',
            'file_id' => 'Файл',
            'uploadedFile' => 'Файл: ',
            'bookAuthorIds' => 'Авторы',
            'bookRedactorIds' => 'Редакторы',
            'disposition' => 'Расположение',

            'selectDate' => 'Выберите дату:',
            'codestart' => 'с номера:',
            'codeend' => 'по:',
            'selectDisposition' => 'Расположение',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        foreach ($this->bookAuthors as $author_rel) {
            $this->bookAuthorIds[] = $author_rel->author->id;
        }
        
        foreach ($this->bookRedactors as $redactor_rel) {
            $this->bookRedactorIds[] = $redactor_rel->author->id;
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
            ->via('bookAuthors');
    }

    /**
     * Gets query for [[BookAllAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAllAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id'])->where(['type' => 0]);
    }

    /**
     * Gets query for [[BookRedactors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookRedactors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id'])->where(['type' => 1]);
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['book/view', 'id' => $this->id]);
    }

    /**
     * Возвращает кликабельного автора книги
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param string $target открытие ссылки в новой вкладке
     * @param bool $redactors вывод редакторов или авторов, дефолт авторы
     * @return string
     */
    public function showAuthor($link = false, $target = "_self", $redactors = false)
    {
        $separator = null;
        $subject = $this->bookAuthors;
        if ($redactors) {
            $subject = $this->bookRedactors;
            $separator = "под ред. ";
        }

        return $separator . implode(', ', array_map(function($author_rel) use ($link, $target) {
            $nameParts = array_filter([$author_rel->author->name]);
            if (preg_match('/^\pL+$/u', $author_rel->author->middlename)) {
                $nameParts = array_filter([
                    mb_substr($author_rel->author->name, 0, 1),
                    mb_substr($author_rel->author->middlename, 0, 1)
                ]);
            }
            $withDots = implode('. ', $nameParts);
            $nameParts = array_filter([$author_rel->author->surname, $withDots]);
            $content = implode(' ', $nameParts) . '.';

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
        }, $subject));
    }

    /**
     * Массив с авторами
     * @return array
     */
    public function getArrayAuthors($delimeter = ' ')
    {
        $authors_rel = $this->bookAuthors;

        $authorSurnames = [];
        foreach ($authors_rel as $author_rel) {
            $authorSurnames[] = $author_rel->author->showFIO($delimeter);
        }

        return $authorSurnames;
    }

    /**
     * Возвращает массив с редакторами
     * @return array
     */
    public function getArrayRedactors()
    {
        $redactors_rel = $this->bookRedactors;

        $redactorSurnames = [];
        foreach ($redactors_rel as $redactor_rel) {
            $redactorSurnames[] = $redactor_rel->author->showFIO();
        }
        return $redactorSurnames;
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
     * Возвращает заголовок книги, состоит из атрибутов name и additionalname
     * 
     * @param bool $strong нет функционала
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file прикрепить к названию иконку с файлом
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self")
    {
        $file = null;
        $content = $this->name;
        if ($this->additionalname) {
            $content .= ': <i class="another-info">' . $this->additionalname . '</i>';
        }
        if ($with_file && $this->file) {
            $file = Html::a(
                '<img src="/Files/Images/doc.png" class="image-file-link">',
                $this->file->getLinkOnFile(),
                [
                    'class' => 'custom-link-file',
                    'target' => "_blank",
                    'data-pjax' => 0,
                    'title' => 'Скачать',
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
                    'title' => $this->annotation
                ]
            ) . $file;
        }
        return $content;
    }

    /**
     * Возвращает информацию о книге для GridView
     * Cостоит из атрибутов: publishplace, publishhouse, publishyear, pages, withraw + (для библиотекарей), code, authorsign и кнопку на файл
     * 
     * @param bool $linked нет функционала
     * @param bool $with_file нет функционала
     * @param string $target нет функционала
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self")
    {
        $result = '';
        if ($this->publishplace) {
            $result .= "{$this->publishplace}";
        }
        if ($this->publishhouse) {
            $result .= ($result ? ': ' : '') . "{$this->publishhouse}";
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
        if (Yii::$app->user->can('book/update')) {
            if ($this->code) {
                $result .= ($result ? ', ' : '') . '<strong>' . "{$this->code}" . '</strong>';
            }
            if ($this->authorsign) {
                $result .= ($result ? ', ' : '') . '<span style="color: green;">' . "{$this->authorsign}" . '</span>';
            }
        }
        if ($this->withraw) {
            $result .= '<span class="status-edition"> [Списано]</span> ';
        }
        $result .= Logbook::checkBookHands($this->id);
        return $result;
    }

    /**
     * Краткая информация о книге для вывода на странице автора и рубрики
     * 
     * @param bool $strong нет функционала
     * @param bool $linked кликабельное название на детальную страницу
     * @return string
     */
    public function getInfoTitle($strong = false, $linked = false)
    {
        $content = $this->inventoryTitle();
        if ($linked) {
            return Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'title' => $this->annotation
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
        $content = StringHelper::truncate($this->name, $abbreviation) . "<strong> {$this->code}";
        if ($this->rubric) {
            $content .= ", {$this->rubric->title}";
        }
        $content .= "</strong> (<span style='color:darkorange;'>Книга</span>)";
        return $content;
    }

    /**
     * Выводит информацию о книге на главную страницу
     * @return string
     */
    public function getLibraryLink()
    {
        $libLink = $this->name;
        $libPlace = $this->publishplace;
        $libYear = $this->publishyear;
        $libPage = $this->pages;
        if (isset($this->rubric)) {
            $libRubric = $this->rubric->title;
        }
        # авторы
        $authorsArray = array_map(function($bookAuthor) {
            $author = $bookAuthor->author;
            if (trim($author->middlename)) 
                return "{$author->name}.{$author->middlename}. {$author->surname}";
            return "{$author->name}. {$author->surname}";
        }, $this->bookAuthors);
        if ($authorsArray) $libLink .= ' / '.implode(', ', $authorsArray).'. - '.$libPlace.', '.$libYear. '. - '.$libPage.' c.';
        if (isset($libRubric)) $libLink .=' '.'('.$libRubric.')';
        # возвращаем результат
        return $libLink;
    }

    /**
     * Краткая информация о книге для инвентарной книги
     * @return string
     */
    public function inventoryTitle()
    {
        $result = "<strong>{$this->name}</strong>";
        if ($this->additionalname) {
            $result .= " : {$this->additionalname}";
        }
        if ($this->response) {
            $result .= " / {$this->response}.";
        }
        if ($this->publishplace) {
            $result .= " - {$this->publishplace}";
        }
        if ($this->publishhouse) {
            $result .= " : {$this->publishhouse}";
        }
        if ($this->publishyear) {
            $result .= ", {$this->publishyear}.";
        }
        if ($this->tom) {
            $result .= " - {$this->tom}.";
        }
        if ($this->pages) {
            $result .= " - {$this->pages} c.";
        }
        return $result;
    }

    /**
     * Метод для проверки статуса выдачи издания
     * 
     * @return bool на руках или нет
     */
    public function isBorrowed()
    {
        $logbook = Logbook::find()->where([
            'book_id' => $this->id,
            'return_date' => null
        ]);
        if ($logbook->exists()) return true;
        return false;
    }
}
