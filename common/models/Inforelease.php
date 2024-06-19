<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "inforelease".
 *
 * @property int $id
 * @property int|null $seria_id
 * @property string $number
 * @property int $numbersk
 * @property int $publishyear
 * @property int|null $file_id
 * @property int|null $rubric_id
 *
 * @property Infoarticle[] $infoarticles
 * @property Files $file
 * @property Rubric $rubric
 * @property Seria $seria
 */
class Inforelease extends \yii\db\ActiveRecord
{
    public $uploadedFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforelease';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'numbersk', 'publishyear'], 'required'],
            [['seria_id', 'numbersk', 'publishyear', 'rubric_id', 'file_id'], 'integer'],
            [['number'], 'string', 'max' => 9],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['uploadedFile'], 'file'],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
            [['seria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seria::class, 'targetAttribute' => ['seria_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seria_id' => 'ID серии',
            'number' => 'Номер',
            'numbersk' => 'Номер С К',
            'publishyear' => 'Год выпуска',
            'rubric_id' => 'Рубрика',
            'file_id' => 'ID файла',
            'uploadedFile' => 'Файл',
            'name' => 'Выпуск'
        ];
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
     * Gets query for [[Infoarticles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfoarticles()
    {
        return $this->hasMany(Infoarticle::class, ['inforelease_id' => 'id']);
    }

    /**
     * Gets query for [[Seria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeria()
    {
        return $this->hasOne(Seria::class, ['id' => 'seria_id']);
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
        return Url::to(['inforelease/view', 'id' => $this->id]);
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
     * Возвращает заголовок родительской серии
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file нет функционала
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showTitle($strong = false, $linked = false, $with_file = false, $target = "_self")
    {
        $content = $this->seria->name;
        if ($strong) {
            $content = "<strong>{$content}</strong>";
        }
        if ($linked) {
            return Html::a(
                $content,
                $this->seria->getUrl(),
                [
                    'class' => 'text-link',
                    'data-pjax' => 0,
                    'target' => $target,
                    'title' => $this->seria->name
                ]
            );
        }
        return $content;
    }

    /**
     * Возвращает информацию о выпуске
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param bool $with_file прикрепить к названию иконку с файлом
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showInfo($linked = false, $with_file = false, $target = "_self")
    {
        $content = "№ {$this->number} за {$this->publishyear}";
        $file = null;
        if ($with_file && $this->file) {
            $file = Html::a(
                '<img src="/Files/Images/doc.png" class="image-file-link">',
                $this->file->getLinkOnFile(),
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
                    'title' => $content
                ]
            ) . $file;
        }
        return $content;
    }

    /**
     * Краткая информация о инфо выпуске для вывода на странице рубрики
     * 
     * @param bool $strong жирное начертание для заголовка
     * @param bool $linked  кликабельные наименования на детальные страницы
     * @return string
     */
    public function getInfoTitle($strong = false, $linked = false)
    {
        return $this->showTitle($strong, $linked) . ": " . $this->showInfo($linked);
    }

    /**
     * Возвращает список инфо выпусков сортированных по номеру
     * 
     * @param int $id индекс инфо выпуска
     * @return \yii\db\ActiveQuery
     */
    static function getReleasesOrdered($id, $rubric_id)
    {
        $query = Inforelease::find();
        if (!$rubric_id) $query->where(['seria_id' => $id]);
        else $query->where(['seria_id' => $id, 'rubric_id' => $rubric_id]);

        return $query->orderBy([
            'publishyear' => SORT_DESC,
            new \yii\db\Expression("CONVERT(SUBSTRING_INDEX(number, '-', 1), SIGNED)"),
            new \yii\db\Expression("CASE WHEN LOCATE('-', number) > 0 THEN CONVERT(SUBSTRING_INDEX(number, '-', -1), SIGNED) END"),
            'number' => SORT_ASC
        ]);
    }
}
