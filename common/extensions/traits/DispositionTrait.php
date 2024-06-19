<?php
namespace common\extensions\traits;

/**
 * This is the trait class for tables: book, journal, statrelease.
 *
 * @property int $disposition
 */
trait DispositionTrait
{
    /**
     * Возвращает наименование атрибута disposition (расположение)
     * 
     * @return string
     */
    public function nameDisposition()
    {
        if ($this->disposition == 1){
            return 'Волнц';
        }
        return 'СЗНИИ';
    }
}