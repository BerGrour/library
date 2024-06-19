<?php
namespace common\extensions\traits;

use common\models\Logbook;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * This is the trait class for tables: Logbook and Cart.
 *
 */
trait EditionInfoTrait
{
    /**
     * Возвращает краткую информацию по соответствующему изданию
     * @param int|bool $abbreviation ограничение строки-заголовка (true - снять ограничение)
     * @param int|bool $abbreviation_second ограничение доп строки (true - снять ограничение)
     * @param bool $linked кликабельные ссылки на издание
     * @param string $target открытие в новой вкладке
     * @return string
     * @throws NotFoundHttpException если изданий не найдено
     */
    public function getEditionInfo($abbreviation = 50, $abbreviation_second = 15, $linked = false, $on_hands = false, $target = "_self")
    {
        if ($this->book_id) {
            $book = $this->book->getBriefInfo($abbreviation, $abbreviation_second);
            if ($linked) {
                return Html::a(
                    $book,
                    $this->book->getUrl(),
                    ['class' => 'text-link', 'data-pjax' => 0, 'target' => $target]
                );
            }
            if ($on_hands) {
                return $book . Logbook::checkBookHands($this->book_id);
            }
            return $book;
        }
        if ($this->statrelease_id) {
            $statrelease = $this->statrelease->getBriefInfo($abbreviation, $abbreviation_second);
            if ($linked) {
                return Html::a(
                    $statrelease,
                    $this->statrelease->getUrl(),
                    ['class' => 'text-link', 'data-pjax' => 0, 'target' => $target]
                );
            }
            if ($on_hands) {
                return $statrelease . Logbook::checkStatreleaseHands($this->statrelease_id);
            }
            return $statrelease;
        }
        if ($this->issue_id) {
            $issue = $this->issue->getBriefInfo($abbreviation, $abbreviation_second);
            if ($linked) {
                return Html::a(
                    $issue,
                    $this->issue->getUrl(),
                    ['class' => 'text-link', 'data-pjax' => 0, 'target' => $target]
                );
            }
            if ($on_hands) {
                return $issue . Logbook::checkIssueHands($this->issue_id);
            }
            return $issue;
        }
        if ($this->article_id) {
            $article = $this->article->getBriefInfo($abbreviation, $abbreviation_second);
            if ($linked) {
                return Html::a(
                    $article,
                    $this->article->getUrl(),
                    ['class' => 'text-link', 'data-pjax' => 0, 'target' => $target]
                );
            }
            if ($on_hands) {
                return $article . Logbook::checkIssueHands($this->article->issue_id);
            }
            return $article;
        }
        if ($this->infoarticle_id) {
            $infoarticle = $this->infoarticle->getBriefInfo($abbreviation, $abbreviation_second);
            if ($linked) {
                return Html::a(
                    $infoarticle,
                    $this->infoarticle->getUrl(),
                    ['class' => 'text-link', 'data-pjax' => 0, 'target' => $target]
                );
            }
            return $infoarticle;
        }
        throw new NotFoundHttpException('Изданий не найдено.');
    }

    /**
     * Возвращает подробную информацию по соответствующему изданию для библиографического списка
     * 
     * @param $print доп инфо для файла
     * @return string
     * @throws NotFoundHttpException если изданий не найдено
     */
    public function getBibliographyInfo($print = false)
    {
        if ($this->book_id) {
            $book = null;
            if ($this->book->getArrayAuthors() and count($this->book->getArrayAuthors()) < 4) {
                $book = "{$this->book->getArrayAuthors(', ')[0]} ";
            }
            $book .= $this->book->getInfoTitle(true);
            if ($print) return $book . "{$this->book->code}, {$this->book->nameDisposition()});";
            else return $book;
        }
        if ($this->statrelease_id) {
            $statrelease = $this->statrelease->getInfoTitle(true);
            if ($print) return $statrelease . "{$this->statrelease->code}, {$this->statrelease->nameDisposition()});";
            else return $statrelease;
        }
        if ($this->issue_id) {
            $issue = $this->issue->journal->name . " <strong>" . $this->issue->showInfo() . "</strong>";
            return $issue;
        }
        if ($this->article_id) {
            $article = null;
            if ($this->article->getArrayAuthors() and count($this->article->getArrayAuthors()) < 4) {
                $article = "{$this->article->getArrayAuthors(', ')[0]} ";
            }
            $article .= $this->article->showTitle(true, false, false) . ' / ';
            if ($this->article->getArrayAuthors()) {
                $article .= "{$this->article->showAuthor(reverse: true)} // ";
            }
            $article .= "{$this->article->issue->journal->name}. - {$this->article->issue->issueyear}. - № {$this->article->issue->issuenumber}.";
            if ($this->article->pages) {
                $article .= " С. {$this->article->pages}";
                if ($this->article->last_pages) {
                    $article .= "-{$this->article->last_pages}";
                }
                $article .= ".";
            }
            return $article;
        }
        if ($this-> infoarticle_id) {
            $infoarticle = null;
            if ($this->infoarticle->getArrayAuthors() and count($this->infoarticle->getArrayAuthors()) < 4) {
                $infoarticle = "{$this->infoarticle->getArrayAuthors(', ')[0]} ";
            }
            $infoarticle .= $this->infoarticle->showTitle(true, false, false) . ' / ';
            if ($this->infoarticle->getArrayAuthors()) {
                $infoarticle .= "{$this->infoarticle->showAuthor(reverse: true)} // ";
            }
            list($source, $number) = $this->infoarticle->getDevideSource();
            $infoarticle .= "{$source}. - {$this->infoarticle->getYearFromDate()}. - № {$number}";            // 
            $infoarticle .= ". - ({$this->infoarticle->inforelease->seria->name}, {$this->infoarticle->inforelease->publishyear}, №{$this->infoarticle->inforelease->number}).";
            return $infoarticle;
        }
        throw new NotFoundHttpException("Изданий не найдено.");
    }
}