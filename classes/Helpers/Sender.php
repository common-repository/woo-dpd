<?php
namespace DPD\Helper;

class Sender
{
    /**
     * Возвращает список отправителей
     *
     * @param boolean $addNew
     * 
     * @return array
     */
    public static function getList($addNew = false)
    {
        $senders = get_option('dpd_sender');
        $senders = unserialize($senders) ?: [];

        if (empty($senders)) {
            $sender            = static::makeSender();
            $sender['name']    = 'По умолчанию';
            $sender['default'] = 1;

            foreach ($sender as $k => $value) {
                if (!$value) {
                    $sender[$k] = get_option('dpd_sender_'. $k);
                }
            }

            $senders[] = $sender;
        }

        if ($addNew) {
            $senders[] = static::makeSender();
        }

        return $senders;
    }

    /**
     * Возвращает отправителя по умолчанию
     *
     * @return array
     */
    public static function getDefault()
    {
        $senders = static::getList();

        foreach ($senders as $sender) {
            if ($sender['default']) {
                return $sender;
            }
        }

        return reset($senders);
    }

    /**
     * Возвращает отправителя по индексу
     *
     * @param int $index
     * 
     * @return array|null
     */
    public static function getByIndex($index)
    {
        $senders = static::getList();

        return array_key_exists($index, $senders) ? $senders[$index] : null;
    }

    public static function makeSender(array $data = [])
    {
        return array_merge([
            'name'          => '',
            'default'       => 0,
            'city'          => '',
            'city_id'       => '',
            'street'        => '',
            'streetabbr'    => '',
            'house'         => '',
            'korpus'        => '',
            'str'           => '',
            'vlad'          => '',
            'office'        => '',
            'flat'          => '',
            'terminal_code' => '',
        ], $data);
    }
}